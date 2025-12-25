<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignSend;
use App\Models\CampaignEvent;
use App\Models\Contact;
use App\Models\Suppression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SESWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = $request->json()->all();

        // SNS Confirmation logic (Amazon sends a SubscriptionConfirmation type first)
        if (($payload['Type'] ?? '') === 'SubscriptionConfirmation') {
            Log::info("SNS Subscription Confirmation: " . ($payload['SubscribeURL'] ?? 'No URL'));
            return response('OK', 200);
        }

        $message = json_decode($payload['Message'] ?? '{}', true);
        $eventType = $message['eventType'] ?? 'unknown';

        Log::info("SES Webhook Event: {$eventType}");

        switch ($eventType) {
            case 'Send':
                // Handled internally, but SES can also report it
                break;

            case 'Delivery':
                $this->handleDelivery($message);
                break;

            case 'Bounce':
                $this->handleBounce($message);
                break;

            case 'Complaint':
                $this->handleComplaint($message);
                break;

            case 'Reject':
                $this->handleReject($message);
                break;

            case 'Unsubscribe':
                $this->handleUnsubscribe($message);
                break;

            case 'Rendering Failure':
                // Log template errors
                break;
        }

        return response('OK', 200);
    }

    protected function handleDelivery(array $message)
    {
        $data = $message['delivery'] ?? [];
        $headers = $this->getHeaders($message);
        $campaignId = $headers['X-Campaign-Id'] ?? null;
        $contactId = $headers['X-Contact-Id'] ?? null;

        if ($campaignId && $contactId) {
            $this->logEvent($campaignId, $contactId, 'delivered', $data);
            
            // Update CampaignSend record
            CampaignSend::where('campaign_id', $campaignId)
                ->where('contact_id', $contactId)
                ->update(['status' => 'delivered', 'delivered_at' => now()]);

            // Increment Campaign delivered_count
            Campaign::where('id', $campaignId)->increment('delivered_count');
        }
    }

    protected function handleBounce(array $message)
    {
        $data = $message['bounce'] ?? [];
        $headers = $this->getHeaders($message);
        $campaignId = $headers['X-Campaign-Id'] ?? null;
        $contactId = $headers['X-Contact-Id'] ?? null;

        if ($campaignId && $contactId) {
            $this->logEvent($campaignId, $contactId, 'bounced', $data);
            
            CampaignSend::where('campaign_id', $campaignId)
                ->where('contact_id', $contactId)
                ->update(['status' => 'bounced', 'bounced_at' => now()]);

            Campaign::where('id', $campaignId)->increment('bounced_count');
            
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                $this->checkAlerts($campaign);
            }
            
            // Mark contact as bounced (optional, for suppression)
            Contact::where('id', $contactId)->update(['status' => 'bounced']);

            // Auto-suppress on hard bounces (Permanent)
            if (($data['bounceType'] ?? '') === 'Permanent') {
                $campaign = Campaign::find($campaignId);
                if ($campaign) {
                    Suppression::updateOrCreate(
                        ['company_id' => $campaign->company_id, 'email' => strtolower($headers['To'] ?? $data['bouncedRecipients'][0]['emailAddress'] ?? '')],
                        ['reason' => 'bounced', 'suppressed_at' => now()]
                    );
                }
            }
        }
    }

    protected function handleComplaint(array $message)
    {
        $data = $message['complaint'] ?? [];
        $headers = $this->getHeaders($message);
        $campaignId = $headers['X-Campaign-Id'] ?? null;
        $contactId = $headers['X-Contact-Id'] ?? null;

        if ($campaignId && $contactId) {
            $this->logEvent($campaignId, $contactId, 'complained', $data);
            
            CampaignSend::where('campaign_id', $campaignId)
                ->where('contact_id', $contactId)
                ->update(['status' => 'complained']);

            Campaign::where('id', $campaignId)->increment('complaint_count');
            
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                $this->checkAlerts($campaign);
            }
            
            // Mark contact as unsubscribed/suppressed
            Contact::where('id', $contactId)->update(['status' => 'unsubscribed']);

            // Auto-suppress on complaints
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                Suppression::updateOrCreate(
                    ['company_id' => $campaign->company_id, 'email' => strtolower($headers['To'] ?? $data['complainedRecipients'][0]['emailAddress'] ?? '')],
                    ['reason' => 'complained', 'suppressed_at' => now()]
                );
            }
        }
    }

    protected function handleReject(array $message)
    {
        $data = $message['reject'] ?? [];
        $headers = $this->getHeaders($message);
        $campaignId = $headers['X-Campaign-Id'] ?? null;
        $contactId = $headers['X-Contact-Id'] ?? null; 

        if ($campaignId && $contactId) {
            $this->logEvent($campaignId, $contactId, 'rejected', $data);
        }
    }

    protected function handleUnsubscribe(array $message)
    {
        $headers = $this->getHeaders($message);
        $campaignId = $headers['X-Campaign-Id'] ?? null;
        $contactId = $headers['X-Contact-Id'] ?? null;

        if ($campaignId && $contactId) {
            $this->logEvent($campaignId, $contactId, 'unsubscribed', []);
            
            Contact::where('id', $contactId)->update([
                'status' => 'unsubscribed',
                'unsubscribed_at' => now(),
            ]);

            // Auto-suppress on unsubscribe
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                $contact = Contact::find($contactId);
                if ($contact) {
                    Suppression::updateOrCreate(
                        ['company_id' => $campaign->company_id, 'email' => strtolower($contact->email)],
                        ['reason' => 'unsubscribed', 'suppressed_at' => now()]
                    );
                }
            }
        }
    }

    protected function logEvent($campaignId, $contactId, $type, $metadata)
    {
        CampaignEvent::create([
            'campaign_id' => $campaignId,
            'contact_id' => $contactId,
            'type' => $type,
            'metadata' => $metadata
        ]);
    }

    protected function checkAlerts(Campaign $campaign)
    {
        $campaign->refresh();
        $totalSent = $campaign->total_recipients ?: 1;
        $bounceRate = ($campaign->bounced_count / $totalSent) * 100;
        $complaintRate = ($campaign->complaint_count / $totalSent) * 100;

        $thresholds = [
            'bounce_warning' => 5,
            'bounce_critical' => 10,
            'complaint_warning' => 0.1,
            'complaint_critical' => 0.5,
        ];

        // Bounce Alert
        if ($bounceRate >= $thresholds['bounce_critical']) {
            $this->createAlert($campaign, 'bounce_rate', 'critical', "Critical bounce rate detected: " . number_format($bounceRate, 2) . "%");
            if ($campaign->isSending()) {
                $campaign->update(['status' => Campaign::STATUS_PAUSED, 'paused_at' => now()]);
            }
        } elseif ($bounceRate >= $thresholds['bounce_warning']) {
            $this->createAlert($campaign, 'bounce_rate', 'warning', "High bounce rate warning: " . number_format($bounceRate, 2) . "%");
        }

        // Complaint Alert
        if ($complaintRate >= $thresholds['complaint_critical']) {
            $this->createAlert($campaign, 'complaint_rate', 'critical', "Critical complaint rate detected: " . number_format($complaintRate, 2) . "%");
            if ($campaign->isSending()) {
                $campaign->update(['status' => Campaign::STATUS_PAUSED, 'paused_at' => now()]);
            }
        } elseif ($complaintRate >= $thresholds['complaint_warning']) {
            $this->createAlert($campaign, 'complaint_rate', 'warning', "High complaint rate warning: " . number_format($complaintRate, 2) . "%");
        }
    }

    protected function createAlert(Campaign $campaign, $type, $severity, $message)
    {
        // Simple deduplication: don't create same active alert in last hour
        $exists = \App\Models\Alert::where('company_id', $campaign->company_id)
            ->where('type', $type)
            ->whereNull('resolved_at')
            ->where('created_at', '>', now()->subHour())
            ->exists();

        if (!$exists) {
            \App\Models\Alert::create([
                'company_id' => $campaign->company_id,
                'type' => $type,
                'severity' => $severity,
                'message' => $message,
                'metadata' => [
                    'campaign_id' => $campaign->id,
                    'campaign_name' => $campaign->name,
                ]
            ]);
        }
    }

    protected function getHeaders(array $message)
    {
        $headers = [];
        $mailHeaders = $message['mail']['headers'] ?? [];
        
        foreach ($mailHeaders as $header) {
            $headers[$header['name']] = $header['value'];
        }

        // SES also puts them in commonHeaders
        $commonHeaders = $message['mail']['commonHeaders'] ?? [];
        // Combine or prefer specific ones
        
        return $headers;
    }
}
