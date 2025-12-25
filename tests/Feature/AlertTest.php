<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Provider;
use App\Models\Sender;
use App\Models\Template;
use App\Models\User;
use App\Models\Alert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertTest extends TestCase
{
    use RefreshDatabase;

    protected $company;
    protected $campaign;

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
            'credentials' => ['key' => 'test'],
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
            'total_recipients' => 10,
        ]);

        $this->contact = Contact::create([
            'company_id' => $this->company->id,
            'email' => 'test@example.com',
            'status' => 'subscribed',
        ]);
    }

    public function test_critical_bounce_rate_triggers_alert_and_pauses_campaign()
    {
        // Simulate 2 bounces out of 10 recipients = 20% (Critical > 10%)
        for ($i = 0; $i < 2; $i++) {
            $payload = [
                'Type' => 'Notification',
                'Message' => json_encode([
                    'eventType' => 'Bounce',
                    'bounce' => [
                        'bounceType' => 'Permanent',
                        'bouncedRecipients' => [['emailAddress' => "bounced$i@example.com"]],
                    ],
                    'mail' => [
                        'headers' => [
                            ['name' => 'X-Campaign-Id', 'value' => $this->campaign->id],
                            ['name' => 'X-Contact-Id', 'value' => $this->contact->id],
                        ],
                    ]
                ])
            ];
            $this->postJson(route('webhooks.ses'), $payload);
        }

        $this->assertDatabaseHas('alerts', [
            'company_id' => $this->company->id,
            'type' => 'bounce_rate',
            'severity' => 'critical',
        ]);

        $this->campaign->refresh();
        $this->assertEquals('paused', $this->campaign->status);
    }

    public function test_warning_complaint_rate_triggers_alert()
    {
        // Simulate 1 complaint out of 10 recipients = 10% (Warning > 0.1%)
        $payload = [
            'Type' => 'Notification',
            'Message' => json_encode([
                'eventType' => 'Complaint',
                'complaint' => [
                    'complainedRecipients' => [['emailAddress' => 'complainer@example.com']],
                ],
                'mail' => [
                    'headers' => [
                        ['name' => 'X-Campaign-Id', 'value' => $this->campaign->id],
                        ['name' => 'X-Contact-Id', 'value' => $this->contact->id],
                    ],
                ]
            ])
        ];
        $this->postJson(route('webhooks.ses'), $payload);

        $this->assertDatabaseHas('alerts', [
            'company_id' => $this->company->id,
            'type' => 'complaint_rate',
            'severity' => 'critical', // 10% is > 0.5% critical threshold
        ]);
    }
}
