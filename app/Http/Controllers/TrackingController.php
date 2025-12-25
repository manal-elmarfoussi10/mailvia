<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    public function open($campaignId, $contactId)
    {
        // Log open event
        CampaignEvent::create([
            'campaign_id' => $campaignId,
            'contact_id' => $contactId,
            'type' => 'opened',
            'metadata' => [
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]
        ]);

        Campaign::where('id', $campaignId)->increment('open_count');

        // Return a 1x1 transparent pixel
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($pixel, 200)
            ->header('Content-Type', 'image/gif')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function click(Request $request)
    {
        $campaignId = $request->query('campaign');
        $contactId = $request->query('contact');
        $targetUrl = $request->query('url');

        if (!$targetUrl) {
            return redirect('/');
        }

        // Log click event
        CampaignEvent::create([
            'campaign_id' => $campaignId,
            'contact_id' => $contactId,
            'type' => 'clicked',
            'metadata' => [
                'url' => $targetUrl,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]
        ]);

        Campaign::where('id', $campaignId)->increment('click_count');

        return redirect($targetUrl);
    }
}
