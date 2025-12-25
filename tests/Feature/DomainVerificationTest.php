<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Domain;
use App\Models\Sender;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainVerificationTest extends TestCase
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
        
        // Mock session
        session(['company_id' => $this->company->id]);
    }

    public function test_can_add_domain()
    {
        $response = $this->post(route('domains.store'), [
            'domain' => 'example.com'
        ]);

        $response->assertRedirect(route('domains.index'));
        $this->assertDatabaseHas('domains', [
            'domain' => 'example.com',
            'company_id' => $this->company->id,
            'status' => 'unverified'
        ]);
        
        $domain = Domain::first();
        $this->assertNotNull($domain->verification_token);
        $this->assertStringStartsWith('mailvia-verify-', $domain->verification_token);
    }

    public function test_verification_logic_executes()
    {
        $domain = $this->company->domains()->create([
            'domain' => 'this-domain-should-not-exist-ever-12345.com',
            'verification_token' => 'mailvia-verify-123'
        ]);

        // Note: dns_get_record is hard to mock without extra packages, 
        // but it will likely fail in test env and return false, 
        // which should result in 'failed' status if ownership not found.
        
        $response = $this->post(route('domains.verify', $domain));
        $response->assertStatus(302);
        
        $domain->refresh();
        if ($domain->status === 'unverified') {
            dump($response->getSession()->get('errors'));
        }
        $this->assertEquals('failed', $domain->status);
    }

    public function test_can_link_sender_to_domain()
    {
        $domain = $this->company->domains()->create([
            'domain' => 'example.com',
            'status' => 'verified',
            'verification_token' => 'token'
        ]);

        $response = $this->post(route('senders.store'), [
            'name' => 'Sender',
            'email' => 'info@example.com',
            'domain_id' => $domain->id
        ]);

        $this->assertDatabaseHas('senders', [
            'email' => 'info@example.com',
            'domain_id' => $domain->id
        ]);
    }
}
