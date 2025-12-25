<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_is_created_when_provider_is_updated()
    {
        $user = User::factory()->create();
        $company = Company::create(['name' => 'Test Company']);
        $user->companies()->attach($company);
        
        $provider = Provider::create([
            'company_id' => $company->id,
            'name' => 'Original Name',
            'type' => 'ses',
            'credentials' => ['key' => 'test'],
        ]);

        $this->actingAs($user);
        session(['company_id' => $company->id]);

        $provider->update(['name' => 'New Name']);

        $this->assertDatabaseHas('audit_logs', [
            'company_id' => $company->id,
            'user_id' => $user->id,
            'action' => 'updated',
            'auditable_type' => Provider::class,
            'auditable_id' => $provider->id,
        ]);

        $log = AuditLog::where('action', 'updated')->first();
        $this->assertEquals('Original Name', $log->metadata['old']['name']);
        $this->assertEquals('New Name', $log->metadata['new']['name']);
    }

    public function test_audit_logs_index_page_loads()
    {
        $user = User::factory()->create();
        $company = Company::create(['name' => 'Test Company']);
        $user->companies()->attach($company);

        $this->actingAs($user);
        session(['company_id' => $company->id]);

        $response = $this->get(route('audit-logs.index'));
        $response->assertStatus(200);
        $response->assertViewHas('logs');
    }
}
