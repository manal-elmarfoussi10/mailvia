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
use Illuminate\Support\Facades\Log;

class DispatchCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Campaign $campaign)
    {
    }

    public function handle(): void
    {
        if (!$this->campaign->isSending()) {
            return;
        }

        // Get recipients based on audience
        $contacts = $this->getRecipients();
        $totalCount = $contacts->count(); // Store original count before potential sampling

        // A/B Testing: Split sample if applicable
        if ($this->campaign->is_ab_test && $this->campaign->ab_test_sample_size > 0 && !$this->campaign->ab_winner_id) {
            $sampleSize = (int) round(($this->campaign->ab_test_sample_size / 100) * $totalCount);
            // Limit to sample
            $contacts = $contacts->take($sampleSize);
        }
        
        $this->campaign->update([
            'total_recipients' => $totalCount, // Use the total count before sampling for this field
            'started_at' => now(), // Keep original 'started_at' update
            'status' => Campaign::STATUS_SENDING, // Add status update
        ]);

        $delay = 0;
        $rate = $this->campaign->eps ?: $this->campaign->throttle_rate ?: 10; // emails per second

        foreach ($contacts as $contact) {
            // Check if campaign was paused/stopped in the meantime
            $this->campaign->refresh();
            if (!$this->campaign->isSending()) { // Use isSending() for consistency
                break;
            }

            // Create send record
            $send = CampaignSend::create([
                'campaign_id' => $this->campaign->id,
                'contact_id' => $contact->id,
                'status' => 'pending',
            ]);

            // Dispatch individual email job with delay for throttling
            SendCampaignEmailJob::dispatch($this->campaign, $contact, $send)
                ->delay(now()->addSeconds($delay));

            // Increment delay based on rate
            // If rate is 10/sec, delay between sends is 0.1sec
            $delay += (1 / $rate);
        }
    }

    protected function getRecipients()
    {
        $audience = $this->campaign->audience;
        $type = $audience['type'] ?? 'all';
        $ids = $audience['ids'] ?? [];

        $query = Contact::query()->where('company_id', $this->campaign->company_id);

        if ($type === 'lists') {
            $query->whereHas('lists', function($q) use ($ids) {
                $q->whereIn('contact_lists.id', $ids);
            });
        } elseif ($type === 'segments') {
            $query->where(function($q) use ($ids) {
                foreach ($ids as $segmentId) {
                    $segment = \App\Models\Segment::find($segmentId);
                    if ($segment) {
                        $q->orWhere(function($sub) use ($segment) {
                            $segment->apply($sub);
                        });
                    }
                }
            });
        }

        // Exclude suppressed/unsubscribed
        $query->where('status', 'subscribed')
            ->whereNotExists(function ($q) {
                $q->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('suppressions')
                    ->whereColumn('suppressions.email', 'contacts.email')
                    ->whereColumn('suppressions.company_id', 'contacts.company_id');
            });

        return $query->get();
    }
}
