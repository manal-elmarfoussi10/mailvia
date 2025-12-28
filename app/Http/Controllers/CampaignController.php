<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Jobs\DispatchCampaignJob;
use App\Jobs\PickCampaignWinnerJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CampaignController extends Controller
{
    public function index()
    {
        $company = auth()->user()->companies()->first();
        $campaigns = $company->campaigns()->with('template', 'provider', 'sender')->latest()->paginate(20);
        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $company = auth()->user()->companies()->first();

        $templates = $company->templates;
        $lists = $company->lists;
        $segments = $company->segments;

        $campaign = new \App\Models\Campaign();

        return view('campaigns.create', compact('templates', 'lists', 'segments', 'campaign'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->companies()->first();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'preheader' => 'nullable|string|max:255',
            'from_name' => 'nullable|string|max:255',
            'from_email' => 'nullable|email|max:255',
            'template_id' => 'nullable|exists:templates,id',
            'audience' => 'required|array',
            'audience.type' => 'required|in:all,lists,segments',
            'audience.ids' => 'nullable|array',
            'audience.exclude_suppressed' => 'nullable|boolean',
            'throttle_rate' => 'nullable|integer|min:1|max:100',
            'throttle_concurrency' => 'nullable|integer|min:1|max:10',
            'scheduled_at' => 'nullable|date|after:now',
            'track_opens' => 'nullable|boolean',
            'track_clicks' => 'nullable|boolean',
            'is_ab_test' => 'nullable|boolean',
            'ab_variations' => 'nullable|array',
            'ab_winner_criteria' => 'nullable|string|in:open_rate,click_rate',
            'ab_test_duration' => 'nullable|integer|min:1|max:72',
            'ab_test_sample_size' => 'nullable|numeric|min:1|max:50',
        ]);

        $data['track_opens'] = $request->boolean('track_opens', true);
        $data['track_clicks'] = $request->boolean('track_clicks', true);

        $data['status'] = Campaign::STATUS_DRAFT;
        $campaign = $company->campaigns()->create($data);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign created as draft.');
    }

    public function show(Campaign $campaign, Request $request)
    {
        $this->authorize('view', $campaign);
        $campaign->load('template', 'provider', 'sender');

        // Engagement Timeline (last 24 hours or since start)
        $timelineData = $campaign->events()
            ->whereIn('type', ['opened', 'clicked'])
            ->selectRaw('type, DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour, count(*) as count')
            ->groupBy('type', 'hour')
            ->orderBy('hour')
            ->get()
            ->groupBy('type');

        // Top Failing Domains
        $failingDomains = $campaign->events()
            ->whereIn('type', ['bounced', 'complained'])
            ->join('contacts', 'campaign_events.contact_id', '=', 'contacts.id')
            ->selectRaw('SUBSTRING_INDEX(contacts.email, "@", -1) as domain, count(*) as count')
            ->groupBy('domain')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Recent Activity
        $recentEvents = $campaign->events()->with('contact')->latest()->limit(20)->get();

        // Recipient Table with Sorting/Filtering
        $recipients = $campaign->sends()
            ->with(['contact', 'events'])
            ->when($request->get('search'), function($query, $search) {
                return $query->whereHas('contact', function($q) use ($search) {
                    $q->where('email', 'like', "%{$search}%");
                });
            })
            ->when($request->get('status'), function($query, $status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Bounce Reasons (Extract from metadata)
        $bounceReasons = $campaign->events()
            ->where('type', 'bounced')
            ->get()
            ->map(function($event) {
                $bouncedRecipients = $event->metadata['bouncedRecipients'] ?? [];
                $diagnosticCode = $bouncedRecipients[0]['diagnosticCode'] ?? 'Unknown diagnostic code';
                return [
                    'reason' => $event->metadata['bounceType'] ?? 'Unknown',
                    'subtype' => $event->metadata['bounceSubType'] ?? 'Unknown',
                    'code' => $diagnosticCode,
                ];
            })
            ->groupBy(function($item) {
                return $item['reason'] . ' - ' . $item['subtype'];
            })
            ->map(function($group) {
                return [
                    'label' => $group->first()['reason'] . ' (' . $group->first()['subtype'] . ')',
                    'count' => $group->count(),
                    'example_code' => $group->first()['code']
                ];
            })
            ->sortByDesc('count')
            ->values();

        return view('campaigns.show', compact(
            'campaign', 
            'recentEvents', 
            'timelineData', 
            'failingDomains', 
            'recipients',
            'bounceReasons'
        ));
    }

    public function edit(Campaign $campaign)
    {
        $this->authorize('view', $campaign);
        
        if ($campaign->status !== Campaign::STATUS_DRAFT) {
            return redirect()->route('campaigns.show', $campaign)->with('error', 'Only draft campaigns can be edited.');
        }

        $company = auth()->user()->companies()->first();
        $templates = $company->templates;
        $providers = $company->providers;
        $senders = $company->senders;
        $lists = $company->contactLists;
        $segments = $company->segments;

        return view('campaigns.edit', compact('campaign', 'templates', 'providers', 'senders', 'lists', 'segments'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'preheader' => 'nullable|string|max:255',
            'from_name' => 'nullable|string|max:255',
            'from_email' => 'nullable|email|max:255',
            'template_id' => 'nullable|exists:templates,id',
            'audience' => 'required|array',
            'throttle_rate' => 'nullable|integer|min:1|max:100',
            'throttle_concurrency' => 'nullable|integer|min:1|max:10',
            'scheduled_at' => 'nullable|date|after:now',
            'track_opens' => 'nullable|boolean',
            'track_clicks' => 'nullable|boolean',
            'is_ab_test' => 'nullable|boolean',
            'ab_variations' => 'nullable|array',
            'ab_winner_criteria' => 'nullable|string|in:open_rate,click_rate',
            'ab_test_duration' => 'nullable|integer|min:1|max:72',
            'ab_test_sample_size' => 'nullable|numeric|min:1|max:50',
        ]);

        $data['track_opens'] = $request->boolean('track_opens', true);
        $data['track_clicks'] = $request->boolean('track_clicks', true);

        $campaign->update($data);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign updated.');
    }

    public function launch(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        if (!$campaign->canBeLaunched()) {
            return back()->with('error', 'This campaign cannot be launched in its current status.');
        }

        $isScheduled = $campaign->scheduled_at && $campaign->scheduled_at->isFuture();

        if ($isScheduled) {
            $campaign->update([
                'status' => Campaign::STATUS_SCHEDULED,
            ]);
            
            DispatchCampaignJob::dispatch($campaign)->delay($campaign->scheduled_at);
            
            return back()->with('success', 'Campaign scheduled for ' . $campaign->scheduled_at->toDayDateTimeString());
        }

        $campaign->update([
            'status' => Campaign::STATUS_SENDING,
            'started_at' => now(),
        ]);

        DispatchCampaignJob::dispatch($campaign);

        // A/B Testing: Schedule winner selection
        if ($campaign->is_ab_test && $campaign->ab_test_duration > 0) {
            PickCampaignWinnerJob::dispatch($campaign)->delay(now()->addHours($campaign->ab_test_duration));
        }

        return back()->with('success', 'Campaign launched successfully!');
    }

    public function cancelSchedule(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        if ($campaign->status !== Campaign::STATUS_SCHEDULED) {
            return back()->with('error', 'Only scheduled campaigns can be cancelled.');
        }

        $campaign->update([
            'status' => Campaign::STATUS_DRAFT,
        ]);

        return back()->with('success', 'Campaign scheduling cancelled. It is now back to draft.');
    }

    public function pause(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        if (!$campaign->canBePaused()) {
            return back()->with('error', 'Only sending campaigns can be paused.');
        }

        $campaign->update([
            'status' => Campaign::STATUS_PAUSED,
            'paused_at' => now(),
        ]);

        return back()->with('success', 'Campaign paused.');
    }

    public function resume(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        if (!$campaign->canBeResumed()) {
            return back()->with('error', 'Only paused campaigns can be resumed.');
        }

        $campaign->update([
            'status' => Campaign::STATUS_SENDING,
        ]);

        // We don't necessarily need to re-dispatch DispatchCampaignJob if it's already running 
        // and checking status, but a new dispatch might be safer to pick up where it left off 
        // if the previous job failed or stopped.
        DispatchCampaignJob::dispatch($campaign);

        return back()->with('success', 'Campaign resumed.');
    }

    public function stop(Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $campaign->update([
            'status' => Campaign::STATUS_STOPPED,
        ]);

        return back()->with('success', 'Campaign stopped permanently.');
    }

    public function duplicate(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $newCampaign = $campaign->replicate([
            'status', 'started_at', 'completed_at', 'paused_at', 
            'sent_count', 'delivered_count', 'failed_count', 'bounced_count'
        ]);
        
        $newCampaign->name = $campaign->name . ' (Copy)';
        $newCampaign->status = Campaign::STATUS_DRAFT;
        $newCampaign->save();

        return redirect()->route('campaigns.edit', $newCampaign)->with('success', 'Campaign duplicated as draft.');
    }

    public function export(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        $filename = "campaign-{$campaign->id}-recipients.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($campaign) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Email', 'Status', 'Sent At', 'Delivered At', 'Bounced At', 'Opens', 'Clicks']);

            $campaign->sends()->with(['contact', 'events'])->chunk(500, function($sends) use ($file) {
                foreach ($sends as $send) {
                    fputcsv($file, [
                        $send->contact->email,
                        $send->status,
                        $send->created_at,
                        $send->delivered_at,
                        $send->bounced_at,
                        $send->events->where('type', 'opened')->count(),
                        $send->events->where('type', 'clicked')->count(),
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function progress(Campaign $campaign)
    {
        $this->authorize('view', $campaign);

        // Calculate rates
        $delivered = $campaign->delivered_count ?: 1;
        $attempted = $campaign->sent_count ?: 1;
        
        $rates = [
            'open_rate' => round(($campaign->open_count / $delivered) * 100, 1),
            'click_rate' => round(($campaign->click_count / $delivered) * 100, 1),
            'bounce_rate' => round(($campaign->bounced_count / $attempted) * 100, 1),
            'complaint_rate' => round(($campaign->complaint_count / $delivered) * 100, 2),
        ];

        // Timeline Data (last 24h)
        $timelineData = $campaign->events()
            ->whereIn('type', ['opened', 'clicked'])
            ->selectRaw('type, DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour, count(*) as count')
            ->groupBy('type', 'hour')
            ->orderBy('hour')
            ->get()
            ->groupBy('type');

        $timeline = [
            'hours' => $timelineData->first()?->pluck('hour') ?? [],
            'opens' => $timelineData->get('opened')?->pluck('count') ?? [],
            'clicks' => $timelineData->get('clicked')?->pluck('count') ?? []
        ];

        // Recent Events
        $recentEvents = $campaign->events()
            ->with('contact')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($e) {
                return [
                    'email' => $e->contact->email,
                    'type' => $e->type, // delivered, opened, clicked, etc.
                    'created_at' => $e->created_at->diffForHumans(null, true)
                ];
            });

        return response()->json([
            'status' => $campaign->status,
            'progress_percentage' => $campaign->getProgressPercentage(),
            'metrics' => [
                'total_recipients' => $campaign->total_recipients,
                'sent_count' => $campaign->sent_count,
                'delivered_count' => $campaign->delivered_count,
                'bounced_count' => $campaign->bounced_count,
                'open_count' => $campaign->open_count,
                'click_count' => $campaign->click_count,
            ],
            'rates' => $rates,
            'timeline' => $timeline,
            'recent_events' => $recentEvents
        ]);
    }

    public function sendTest(Request $request, Campaign $campaign)
    {
        $this->authorize('update', $campaign);

        $request->validate([
            'emails' => 'required|array|min:1|max:3',
            'emails.*' => 'email'
        ]);

        $emails = $request->emails;

        try {
            $template = $campaign->template;
            $content = $template ? ($template->content_html ?? $template->content_text) : 'No content';

            foreach ($emails as $email) {
                // Simple string replacement for basic personalization testing
                $personalizedContent = str_replace('{{email}}', $email, $content);
                $personalizedContent = str_replace('{{name}}', 'Test User', $personalizedContent);

                \Mail::mailer(config('mail.default'))->send([], [], function ($message) use ($email, $campaign, $personalizedContent) {
                    $message->to($email)
                        ->from($campaign->from_email, $campaign->from_name)
                        ->subject('[TEST] ' . $campaign->subject)
                        ->html($personalizedContent);

                    if ($campaign->reply_to) {
                        $message->replyTo($campaign->reply_to);
                    }
                });
            }

            return response()->json(['message' => 'Test emails sent to ' . implode(', ', $emails)]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send test: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Campaign $campaign)
    {
        $this->authorize('delete', $campaign);
        
        if ($campaign->isSending()) {
            return back()->with('error', 'Cannot delete an active campaign. Stop it first.');
        }

        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted.');
    }
}
