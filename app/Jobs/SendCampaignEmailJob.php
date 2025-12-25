<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\CampaignSend;
use App\Models\Suppression;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendCampaignEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1m, 5m, 15m

    public function __construct(
        protected Campaign $campaign,
        protected Contact $contact,
        protected CampaignSend $send
    ) {
    }

    public function handle(): void
    {
        // Re-check campaign status
        if ($this->campaign->status !== Campaign::STATUS_SENDING) {
            $this->release(60); // Check again in a minute
            return;
        }

        // Safety net: check if email became suppressed after dispatch
        if (Suppression::isSuppressed($this->campaign->company_id, $this->contact->email)) {
            $this->send->markAsFailed('Suppressed');
            return;
        }

        try {
            $template = $this->campaign->template;
            $sender = $this->campaign->sender;
            $provider = $this->campaign->provider;
            $subject = $this->campaign->subject;

            // A/B Testing Variation Selection
            if ($this->campaign->is_ab_test && !empty($this->campaign->ab_variations)) {
                $variations = $this->campaign->ab_variations;
                
                if ($this->campaign->ab_winner_id) {
                    // Winning variation selected
                    if ($this->campaign->ab_winner_id !== 'control') {
                        // Find variation by ID or index-based identifier
                        $winnerIndex = -1;
                        foreach ($variations as $idx => $v) {
                            $vId = $v['id'] ?? 'v' . ($idx + 1);
                            if ($vId === $this->campaign->ab_winner_id) {
                                $winnerIndex = $idx;
                                break;
                            }
                        }
                        
                        if ($winnerIndex !== -1) {
                            $variation = $variations[$winnerIndex];
                            $subject = $variation['subject'] ?? $subject;
                            if (isset($variation['template_id'])) {
                                $vTemplate = \App\Models\Template::find($variation['template_id']);
                                if ($vTemplate) $template = $vTemplate;
                            }
                        }
                    }
                    // If winner is 'control', we stick with default $subject and $template
                } else {
                    // Sample Phase: Stable rotation distribution
                    $vCount = count($variations);
                    $index = $this->contact->id % ($vCount + 1);
                    
                    if ($index > 0) {
                        $variation = $variations[$index - 1];
                        $subject = $variation['subject'] ?? $subject;
                        if (isset($variation['template_id'])) {
                            $vTemplate = \App\Models\Template::find($variation['template_id']);
                            if ($vTemplate) $template = $vTemplate;
                        }
                    }
                    
                    // Track which variation was sent in metadata
                    $currentMeta = $this->send->metadata ?? [];
                    $this->send->update(['metadata' => array_merge($currentMeta, ['variation_index' => $index])]);
                }
            }

            // Prepare content (variables replacement)
            $html = $this->replaceVariables($template->content_html);
            $text = $this->replaceVariables($template->content_text);

            // Inject Tracking Pixel
            if ($this->campaign->track_opens) {
                $pixelUrl = route('track.open', [$this->campaign->id, $this->contact->id]);
                $pixelTag = "<img src=\"{$pixelUrl}\" width=\"1\" height=\"1\" alt=\"\" style=\"display:none\" />";
                $html = str_ireplace('</body>', $pixelTag . '</body>', $html);
                if (stripos($html, '</body>') === false) {
                    $html .= $pixelTag;
                }
            }

            // Rewrite Links
            if ($this->campaign->track_clicks) {
                $html = $this->rewriteLinks($html);
            }

            // Send via Mail facade (configured via custom provider logic)
            Mail::send([], [], function ($message) use ($sender, $html, $text, $subject) {
                $message->to($this->contact->email)
                    ->from($sender->from_email, $sender->from_name)
                    ->subject($subject)
                    ->html($html)
                    ->plain($text);
                
                // Custom headers for SES (will be returned in webhooks)
                $message->getHeaders()->addTextHeader('X-Campaign-Id', $this->campaign->id);
                $message->getHeaders()->addTextHeader('X-Contact-Id', $this->contact->id);
            });

            $this->send->markAsSent();
            $this->campaign->increment('sent_count');

            // Check if completed
            if ($this->campaign->sent_count >= $this->campaign->total_recipients) {
                $this->campaign->update([
                    'status' => Campaign::STATUS_COMPLETED,
                    'completed_at' => now(),
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Failed to send campaign email to {$this->contact->email}: " . $e->getMessage());
            $this->send->markAsFailed($e->getMessage());
            $this->campaign->increment('failed_count');
            
            throw $e;
        }
    }

    protected function replaceVariables($content)
    {
        $vars = [
            '{{email}}' => $this->contact->email,
            '{{first_name}}' => $this->contact->first_name,
            '{{last_name}}' => $this->contact->last_name,
            '{{unsubscribe_url}}' => route('unsubscribe', ['email' => $this->contact->email, 'c' => $this->campaign->id]),
        ];

        return str_replace(array_keys($vars), array_values($vars), $content);
    }

    protected function rewriteLinks($html)
    {
        return preg_replace_callback('/<a\s+[^>]*href="([^"]*)"/i', function ($matches) {
            $url = $matches[1];
            
            // Skip tracking for common cases
            if (str_starts_with($url, '#') || str_starts_with($url, 'mailto:') || str_starts_with($url, 'tel:')) {
                return $matches[0];
            }

            $trackedUrl = route('track.click', [
                'campaign' => $this->campaign->id,
                'contact' => $this->contact->id,
                'url' => $url
            ]);

            return str_replace($url, $trackedUrl, $matches[0]);
        }, $html);
    }
}
