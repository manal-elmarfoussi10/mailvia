<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignEvent;
use App\Models\Contact;
use App\Models\Suppression;
use Illuminate\Http\Request;

class UnsubscribeController extends Controller
{
    public function __invoke(Request $request)
    {
        $email = $request->query('email');
        $campaignId = $request->query('c');

        if (!$email) {
            return view('unsubscribe.error', ['message' => 'Missing email address.']);
        }

        $contact = Contact::where('email', $email)->first();

        if (!$contact) {
            return view('unsubscribe.error', ['message' => 'Contact not found.']);
        }

        // Mark as unsubscribed if not already
        if ($contact->status !== 'unsubscribed') {
            $contact->update([
                'status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);

            // Log event if campaign ID is provided
            if ($campaignId) {
                CampaignEvent::create([
                    'campaign_id' => $campaignId,
                    'contact_id' => $contact->id,
                    'type' => 'unsubscribed',
                    'metadata' => [
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]
                ]);

                $campaign = Campaign::find($campaignId);
                if ($campaign) {
                    Suppression::updateOrCreate(
                        ['company_id' => $campaign->company_id, 'email' => strtolower($contact->email)],
                        ['reason' => 'unsubscribed', 'suppressed_at' => now()]
                    );
                }
            }
        }

        return view('unsubscribe.success', compact('contact'));
    }
}
