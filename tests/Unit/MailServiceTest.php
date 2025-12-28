<?php

namespace Tests\Unit;

use App\Models\Provider;
use App\Services\MailService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class MailServiceTest extends TestCase
{
    /** @test */
    public function it_configures_ses_smtp_mailer()
    {
        // Create a fake provider with SES type and credentials
        $provider = new Provider([
            'type' => 'ses',
            'credentials' => [
                'region' => 'us-east-1',
                'host' => 'email-smtp.us-east-1.amazonaws.com',
                'port' => 587,
                'encryption' => 'tls',
                'username' => 'test_user',
                'password' => 'test_pass',
            ],
        ]);

        $mailerName = MailService::configureMailer($provider);

        $configKey = "mail.mailers.{$mailerName}";
        $this->assertTrue(Config::has($configKey), "Mailer config {$configKey} should exist");
        $mailerConfig = Config::get($configKey);
        $this->assertEquals('smtp', $mailerConfig['transport']);
        $this->assertEquals('email-smtp.us-east-1.amazonaws.com', $mailerConfig['host']);
        $this->assertEquals(587, $mailerConfig['port']);
        $this->assertEquals('tls', $mailerConfig['encryption']);
        $this->assertEquals('test_user', $mailerConfig['username']);
        $this->assertEquals('test_pass', $mailerConfig['password']);
    }
}
