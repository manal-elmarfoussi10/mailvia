<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Company;
use App\Models\User;
use App\Models\CampaignEvent;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CampaignProgressTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Company $company;
    protected Campaign $campaign;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->company = Company::create([
            'name' => 'Test Company',
        ]);
        $this->user->companies()->attach($this->company);
        
        $this->campaign = Campaign::create([
            'company_id' => $this->company->id,
            'name' => 'Test Campaign',
            'status' => Campaign::STATUS_SENDING,
            'subject' => 'Test Subject',
            'audience' => ['type' => 'all'],
            'total_recipients' => 100,
            'sent_count' => 50,
            'delivered_count' => 45,
            'bounced_count' => 5,
            'failed_count' => 0,
            'open_count' => 20,
            'click_count' => 10,
            'complaint_count' => 1,
        ]);
    }

    /** @test */
    public function authenticated_user_can_access_their_campaign_progress()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'progress_percentage',
                'metrics' => [
                    'total_recipients',
                    'sent_count',
                    'delivered_count',
                    'bounced_count',
                    'failed_count',
                    'open_count',
                    'click_count',
                    'complaint_count',
                ],
                'rates' => [
                    'open_rate',
                    'click_rate',
                    'bounce_rate',
                    'complaint_rate',
                ],
                'recent_events',
                'timeline' => [
                    'hours',
                    'opens',
                    'clicks',
                ],
                'is_active',
                'timestamp',
            ]);
    }

    /** @test */
    public function user_cannot_access_another_companys_campaign_progress()
    {
        $otherCompany = Company::create([
            'name' => 'Other Company',
        ]);
        $otherCampaign = Campaign::create([
            'company_id' => $otherCompany->id,
            'name' => 'Other Campaign',
            'status' => Campaign::STATUS_SENDING,
            'subject' => 'Test Subject',
            'audience' => ['type' => 'all'],
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $otherCampaign));

        $response->assertStatus(403);
    }

    /** @test */
    public function progress_data_includes_correct_metrics()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));

        $response->assertStatus(200)
            ->assertJson([
                'status' => Campaign::STATUS_SENDING,
                'progress_percentage' => 50.0, // 50/100
                'metrics' => [
                    'total_recipients' => 100,
                    'sent_count' => 50,
                    'delivered_count' => 45,
                    'bounced_count' => 5,
                    'open_count' => 20,
                    'click_count' => 10,
                    'complaint_count' => 1,
                ],
            ]);
    }

    /** @test */
    public function progress_data_includes_calculated_rates()
    {
        $response = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));

        $data = $response->json();

        // Open rate: 20/45 * 100 = 44.4%
        $this->assertEquals(44.4, $data['rates']['open_rate']);
        
        // Click rate: 10/45 * 100 = 22.2%
        $this->assertEquals(22.2, $data['rates']['click_rate']);
        
        // Bounce rate: 5/50 * 100 = 10%
        $this->assertEquals(10.0, $data['rates']['bounce_rate']);
        
        // Complaint rate: 1/45 * 100 = 2.22%
        $this->assertEquals(2.22, $data['rates']['complaint_rate']);
    }

    /** @test */
    public function progress_data_includes_recent_events()
    {
        $contact = Contact::create([
            'company_id' => $this->company->id,
            'email' => 'test@example.com',
            'status' => 'subscribed',
        ]);

        CampaignEvent::create([
            'campaign_id' => $this->campaign->id,
            'contact_id' => $contact->id,
            'type' => 'opened',
        ]);

        CampaignEvent::create([
            'campaign_id' => $this->campaign->id,
            'contact_id' => $contact->id,
            'type' => 'clicked',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));

        $data = $response->json();

        $this->assertCount(2, $data['recent_events']);
        $this->assertEquals('test@example.com', $data['recent_events'][0]['email']);
        $this->assertArrayHasKey('type', $data['recent_events'][0]);
        $this->assertArrayHasKey('created_at', $data['recent_events'][0]);
    }

    /** @test */
    public function progress_data_is_cached()
    {
        Cache::flush();

        // First request - should cache
        $response1 = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));

        $this->assertTrue(Cache::has("campaign_progress_{$this->campaign->id}"));

        // Update campaign metrics
        $this->campaign->update(['sent_count' => 60]);

        // Second request within cache TTL - should return cached data
        $response2 = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));

        // Should still show old cached value
        $this->assertEquals(50, $response2->json('metrics.sent_count'));

        // Wait for cache to expire (2 seconds)
        sleep(3);

        // Third request after cache expiry - should show new data
        $response3 = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));

        $this->assertEquals(60, $response3->json('metrics.sent_count'));
    }

    /** @test */
    public function is_active_flag_is_correct_for_different_statuses()
    {
        // Sending campaign
        Cache::flush();
        $this->campaign->update(['status' => Campaign::STATUS_SENDING]);
        $response = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));
        $this->assertTrue($response->json('is_active'));

        // Paused campaign
        Cache::flush();
        $this->campaign->update(['status' => Campaign::STATUS_PAUSED]);
        $response = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));
        $this->assertTrue($response->json('is_active'));

        // Completed campaign
        Cache::flush();
        $this->campaign->update(['status' => Campaign::STATUS_COMPLETED]);
        $response = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));
        $this->assertFalse($response->json('is_active'));

        // Stopped campaign
        Cache::flush();
        $this->campaign->update(['status' => Campaign::STATUS_STOPPED]);
        $response = $this->actingAs($this->user)
            ->getJson(route('campaigns.progress', $this->campaign));
        $this->assertFalse($response->json('is_active'));
    }

    /** @test */
    public function unauthenticated_user_cannot_access_progress()
    {
        $response = $this->getJson(route('campaigns.progress', $this->campaign));

        $response->assertStatus(401);
    }
}
