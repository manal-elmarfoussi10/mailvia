<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CampaignProgressController extends Controller
{
    /**
     * Get real-time progress data for a campaign.
     *
     * @param Campaign $campaign
     * @return JsonResponse
     */
    public function show(Campaign $campaign): JsonResponse
    {
        // Authorization check
        $this->authorize('view', $campaign->company);

        // Cache progress data for 2 seconds to reduce database load
        $cacheKey = "campaign_progress_{$campaign->id}";
        
        $data = Cache::remember($cacheKey, 2, function () use ($campaign) {
            // Refresh campaign data
            $campaign->refresh();

            // Get recent events for live feed (last 20)
            $recentEvents = $campaign->events()
                ->with('contact:id,email')
                ->latest()
                ->limit(20)
                ->get()
                ->map(function ($event) {
                    return [
                        'type' => $event->type,
                        'email' => $event->contact->email ?? 'Unknown',
                        'created_at' => $event->created_at->diffForHumans(null, true),
                        'created_at_full' => $event->created_at->toISOString(),
                    ];
                });

            // Get engagement timeline data (last 24 hours, grouped by hour)
            $timelineEvents = $campaign->events()
                ->whereIn('type', ['opened', 'clicked'])
                ->where('created_at', '>=', now()->subDay())
                ->get();

            // Group by type and hour in PHP (database-agnostic)
            $timelineData = $timelineEvents->groupBy('type')->map(function ($events) {
                return $events->groupBy(function ($event) {
                    return $event->created_at->format('Y-m-d H:00');
                })->map(function ($hourEvents) {
                    return [
                        'hour' => $hourEvents->first()->created_at->format('Y-m-d H:00'),
                        'count' => $hourEvents->count(),
                    ];
                })->values();
            });

            // Format timeline data for Chart.js
            $hours = [];
            $openData = [];
            $clickData = [];

            if ($timelineData->isNotEmpty()) {
                $allHours = $timelineData->flatten(1)->pluck('hour')->unique()->sort()->values();
                
                foreach ($allHours as $hour) {
                    $hours[] = $hour;
                    $openData[] = $timelineData->get('opened', collect())->firstWhere('hour', $hour)->count ?? 0;
                    $clickData[] = $timelineData->get('clicked', collect())->firstWhere('hour', $hour)->count ?? 0;
                }
            }

            // Calculate rates
            $delivered = $campaign->delivered_count ?: 1;
            $sent = $campaign->sent_count ?: 1;

            return [
                'status' => $campaign->status,
                'progress_percentage' => $campaign->getProgressPercentage(),
                'metrics' => [
                    'total_recipients' => $campaign->total_recipients,
                    'sent_count' => $campaign->sent_count,
                    'delivered_count' => $campaign->delivered_count,
                    'bounced_count' => $campaign->bounced_count,
                    'failed_count' => $campaign->failed_count,
                    'open_count' => $campaign->open_count,
                    'click_count' => $campaign->click_count,
                    'complaint_count' => $campaign->complaint_count,
                ],
                'rates' => [
                    'open_rate' => round(($campaign->open_count / $delivered) * 100, 1),
                    'click_rate' => round(($campaign->click_count / $delivered) * 100, 1),
                    'bounce_rate' => round(($campaign->bounced_count / $sent) * 100, 1),
                    'complaint_rate' => round(($campaign->complaint_count / $delivered) * 100, 2),
                ],
                'recent_events' => $recentEvents,
                'timeline' => [
                    'hours' => $hours,
                    'opens' => $openData,
                    'clicks' => $clickData,
                ],
                'is_active' => in_array($campaign->status, ['sending', 'paused']),
                'timestamp' => now()->toISOString(),
            ];
        });

        return response()->json($data);
    }
}
