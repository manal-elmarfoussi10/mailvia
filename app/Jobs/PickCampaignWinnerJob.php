<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignEvent;
use App\Models\CampaignSend;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PickCampaignWinnerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Campaign $campaign)
    {
    }

    public function handle(): void
    {
        if (!$this->campaign->is_ab_test || $this->campaign->ab_winner_id) {
            return;
        }

        $criteria = $this->campaign->ab_winner_criteria ?: 'open_rate';
        $variations = $this->campaign->ab_variations ?: [];
        $vCount = count($variations);

        // Indices: 0 (Control), 1..N (Variations)
        $stats = [];
        for ($i = 0; $i <= $vCount; $i++) {
            $sends = $this->campaign->sends()->whereRaw("JSON_EXTRACT(metadata, '$.variation_index') = ?", [$i])->count();
            if ($sends === 0) {
                $stats[$i] = 0;
                continue;
            }

            $eventType = ($criteria === 'click_rate') ? 'clicked' : 'opened';
            $actions = CampaignEvent::where('campaign_id', $this->campaign->id)
                ->where('type', $eventType)
                ->whereHas('send', function($q) use ($i) {
                    $q->whereRaw("JSON_EXTRACT(metadata, '$.variation_index') = ?", [$i]);
                })
                ->distinct('contact_id')
                ->count();

            $stats[$i] = $actions / $sends;
        }

        // Find max stat
        $winnerIndex = array_keys($stats, max($stats))[0];
        $winnerId = ($winnerIndex === 0) ? 'control' : ($variations[$winnerIndex - 1]['id'] ?? 'v' . $winnerIndex);

        $this->campaign->update([
            'ab_winner_id' => $winnerId,
        ]);

        Log::info("A/B Winner picked for Campaign #{$this->campaign->id}: {$winnerId} (Index {$winnerIndex})");

        // Now dispatch to the rest of the audience
        $this->dispatchToRemainingAudience();
    }

    protected function dispatchToRemainingAudience()
    {
        // We reuse logic from DispatchCampaignJob but with a filter for contacts who haven't been sent yet
        $contacts = $this->getRemainingRecipients();
        
        foreach ($contacts as $contact) {
            $send = CampaignSend::create([
                'campaign_id' => $this->campaign->id,
                'contact_id' => $contact->id,
                'status' => 'pending',
                'metadata' => ['is_winner_dispatch' => true]
            ]);

            SendCampaignEmailJob::dispatch($this->campaign, $contact, $send);
        }
    }

    protected function getRemainingRecipients()
    {
        // Simplification: We fetch the full target audience and subtract those already in campaign_sends
        $alreadySentIds = $this->campaign->sends()->pluck('contact_id');
        
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

        return $query->where('status', 'subscribed')
            ->whereNotIn('id', $alreadySentIds)
            ->get();
    }
}
