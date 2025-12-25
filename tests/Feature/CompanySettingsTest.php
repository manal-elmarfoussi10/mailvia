<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Provider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->company = Company::create(['name' => 'Test Company']);
        $this->user->companies()->attach($this->company);
        $this->actingAs($this->user);
        
        session(['company_id' => $this->company->id]);
    }

    public function test_can_view_settings_page()
    {
        $response = $this->get(route('settings.edit'));
        $response->assertStatus(200);
        $response->assertSee('Global Company Settings');
    }

    public function test_can_update_settings()
    {
        $provider = Provider::create([
            'company_id' => $this->company->id,
            'name' => 'SES',
            'type' => 'ses',
            'credentials' => ['key' => '123']
        ]);

        $response = $this->put(route('settings.update'), [
            'name' => 'Updated Company Name',
            'settings' => [
                'default_provider_id' => $provider->id,
                'hourly_limit' => 100,
                'tracking_enabled' => true,
                'branding_footer' => '<p>Footer</p>'
            ]
        ]);

        $response->assertRedirect();
        $this->company->refresh();

        $this->assertEquals('Updated Company Name', $this->company->name);
        $this->assertEquals($provider->id, $this->company->settings['default_provider_id']);
        $this->assertEquals(100, $this->company->settings['hourly_limit']);
        $this->assertTrue($this->company->settings['tracking_enabled']);
        $this->assertEquals('<p>Footer</p>', $this->company->settings['branding_footer']);
    }
}
