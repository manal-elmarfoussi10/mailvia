<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\CampaignEvent;
use App\Models\CampaignSend;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Provider;
use App\Models\Sender;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $campaign;
    protected $contact;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->company = Company::create(['name' => 'Test Company']);
        $user->companies()->attach($this->company);
        
        $provider = Provider::create([
            'company_id' => $this->company->id,
            'name' => 'SES',
            'type' => 'ses',
            'credentials' => ['key' => 'test', 'secret' => 'test'],
        ]);

        $sender = Sender::create([
            'company_id' => $this->company->id,
            'name' => 'Test',
            'email' => 'test@example.com',
        ]);

        $template = Template::create([
            'company_id' => $this->company->id,
            'name' => 'Test',
            'subject' => 'Test',
            'content_html' => 'Hello',
        ]);

        $this->campaign = Campaign::create([
            'company_id' => $this->company->id,
            'name' => 'Test Campaign',
            'subject' => 'Test Subject',
            'template_id' => $template->id,
            'provider_id' => $provider->id,
            'sender_id' => $sender->id,
            'status' => 'sending',
            'audience' => ['type' => 'all'],
            'total_recipients' => 1,
        ]);

        $this->contact = Contact::create([
            'company_id' => $this->company->id,
            'email' => 'contact@example.com',
        ]);

        CampaignSend::create([
            'campaign_id' => $this->campaign->id,
            'contact_id' => $this->contact->id,
            'status' => 'sent',
        ]);
    }

    public function test_open_tracking()
    {
        $response = $this->get(route('track.open', [$this->campaign->id, $this->contact->id]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/gif');

        $this->assertDatabaseHas('campaign_events', [
            'campaign_id' => $this->campaign->id,
            'contact_id' => $this->contact->id,
            'type' => 'opened',
        ]);

        $this->campaign->refresh();
        $this->assertEquals(1, $this->campaign->open_count);
    }

    public function test_click_tracking()
    {
        $url = 'https://google.com';
        $response = $this->get(route('track.click', [
            'campaign' => $this->campaign->id,
            'contact' => $this->contact->id,
            'url' => $url
        ]));

        $response->assertRedirect($url);

        $this->assertDatabaseHas('campaign_events', [
            'campaign_id' => $this->campaign->id,
            'contact_id' => $this->contact->id,
            'type' => 'clicked',
        ]);

        $this->campaign->refresh();
        $this->assertEquals(1, $this->campaign->click_count);
    }

    public function test_ses_delivery_webhook()
    {
        $payload = [
            'Type' => 'Notification',
            'Message' => json_encode([
                'eventType' => 'Delivery',
                'delivery' => [
                    'timestamp' => '2023-01-01T00:00:00.000Z',
                    'processingTimeMillis' => 100,
                    'recipients' => ['contact@example.com'],
                ],
                'mail' => [
                    'headers' => [
                        ['name' => 'X-Campaign-Id', 'value' => $this->campaign->id],
                        ['name' => 'X-Contact-Id', 'value' => $this->contact->id],
                    ],
                ]
            ])
        ];

        $response = $this->postJson(route('webhooks.ses'), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('campaign_events', [
            'campaign_id' => $this->campaign->id,
            'contact_id' => $this->contact->id,
            'type' => 'delivered',
        ]);

        $this->assertDatabaseHas('campaign_sends', [
            'campaign_id' => $this->campaign->id,
            'contact_id' => $this->contact->id,
            'status' => 'delivered',
        ]);
    }

    public function test_public_unsubscribe()
    {
        $response = $this->get(route('unsubscribe', [
            'email' => $this->contact->email,
            'c' => $this->campaign->id
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('unsubscribe.success');

        $this->contact->refresh();
        $this->assertEquals('unsubscribed', $this->contact->status);
        $this->assertNotNull($this->contact->unsubscribed_at);

        $this->assertDatabaseHas('campaign_events', [
            'campaign_id' => $this->campaign->id,
            'contact_id' => $this->contact->id,
            'type' => 'unsubscribed',
        ]);
    }
}
