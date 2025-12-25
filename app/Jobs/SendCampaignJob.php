<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class SendCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        $campaign = $this->campaign;
        $company = $campaign->company;

        // Update status
        $campaign->update(['status' => 'sending', 'started_at' => now()]);

        // Get recipients based on audience
        $contacts = $this->getRecipients($campaign);
        
        $campaign->update(['total_recipients' => $contacts->count()]);

        // Get provider and sender
        $provider = $campaign->provider ?? $company->providers()->first();
        $sender = $campaign->sender ?? $company->senders()->first();
        $template = $campaign->template;

        if (!$provider || !$sender || !$template) {
            $campaign->update(['status' => 'failed']);
            return;
        }

        // Rate limiting key
        $rateLimitKey = "campaign:{$campaign->id}:provider:{$provider->id}";

        foreach ($contacts as $contact) {
            // Throttle: 10 emails per minute per provider (configurable)
            RateLimiter::attempt(
                $rateLimitKey,
                $perMinute = 10,
                function() use ($contact, $template, $sender, $provider, $campaign) {
                    $this->sendEmail($contact, $template, $sender, $provider, $campaign);
                },
                $decaySeconds = 60
            );

            // If rate limit hit, wait
            if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
                sleep(60); // Wait 1 minute
                RateLimiter::clear($rateLimitKey);
            }
        }

        // Mark as completed
        $campaign->update(['status' => 'completed', 'completed_at' => now()]);
    }

    protected function getRecipients(Campaign $campaign)
    {
        $company = $campaign->company;
        $audience = $campaign->audience;

        if ($audience['type'] === 'all') {
            return $company->contacts()->where('status', 'subscribed')->get();
        }

        if ($audience['type'] === 'lists') {
            $listIds = $audience['ids'] ?? [];
            return Contact::whereHas('lists', function($q) use ($listIds) {
                $q->whereIn('contact_lists.id', $listIds);
            })->where('status', 'subscribed')->get();
        }

        if ($audience['type'] === 'segments') {
            // Simplified segment logic - in production would evaluate criteria
            return $company->contacts()->where('status', 'subscribed')->get();
        }

        return collect();
    }

    protected function sendEmail(Contact $contact, $template, $sender, $provider, $campaign)
    {
        try {
            // Interpolate variables
            $variables = [
                'email' => $contact->email,
                'first_name' => $contact->first_name ?? '',
                'last_name' => $contact->last_name ?? '',
            ];

            $interpolated = $template->interpolate($variables);
            $subject = $campaign->subject ?? $interpolated['subject'];

            // Configure mailer dynamically (simplified - would need proper transport setup)
            Mail::send([], [], function ($message) use ($contact, $subject, $interpolated, $sender) {
                $message->to($contact->email)
                    ->from($sender->email, $sender->name)
                    ->subject($subject)
                    ->html($interpolated['html'])
                    ->text($interpolated['text']);
            });

            // Update sent count
            $campaign->increment('sent_count');

        } catch (\Exception $e) {
            // Log error (in production would track failed sends)
            \Log::error("Failed to send email to {$contact->email}: " . $e->getMessage());
        }
    }
}
