<?php

namespace App\Services;

use App\Models\Provider;
use Illuminate\Support\Facades\Config;

class MailService
{
    /**
     * Configure a dynamic mailer based on the provider settings.
     *
     * @param Provider $provider
     * @return string The mailer driver name to use (e.g., 'provider_1')
     */
    public static function configureMailer(Provider $provider): string
    {
        $credentials = $provider->credentials;
        $driver = $provider->type;
        
        // Use unique mailer name per provider to avoid conflicts
        $mailerName = "provider_{$provider->id}";
        $configKey = "mail.mailers.{$mailerName}";

        switch ($driver) {
            case 'ses':
            case 'amazon_ses':
                // Amazon SES via SMTP (not AWS SDK)
                // This allows using SES SMTP credentials instead of AWS API keys
                $region = $credentials['region'] ?? 'us-east-1';
                $host = $credentials['host'] ?? "email-smtp.{$region}.amazonaws.com";
                
                Config::set($configKey, [
                    'transport' => 'smtp',
                    'host' => $host,
                    'port' => (int)($credentials['port'] ?? 587),
                    'encryption' => $credentials['encryption'] ?? 'tls',
                    'username' => $credentials['username'] ?? '',
                    'password' => $credentials['password'] ?? '',
                    'timeout' => 30,
                    'local_domain' => env('MAIL_EHLO_DOMAIN'),
                ]);
                break;

            case 'smtp':
                $port = $credentials['port'] ?? 587;
                $encryption = $credentials['encryption'] ?? null;
                
                // Auto-detect encryption if not set
                if (!$encryption) {
                    $encryption = ($port == 465) ? 'ssl' : 'tls';
                }

                Config::set($configKey, [
                    'transport' => 'smtp',
                    'host' => $credentials['host'] ?? '',
                    'port' => $port,
                    'encryption' => $encryption,
                    'username' => $credentials['username'] ?? '',
                    'password' => $credentials['password'] ?? '',
                    'timeout' => null,
                    'local_domain' => env('MAIL_EHLO_DOMAIN'),
                ]);
                break;

            case 'mailgun':
                Config::set($configKey, [
                    'transport' => 'mailgun',
                    'domain' => $credentials['domain'] ?? '',
                    'secret' => $credentials['secret'] ?? '',
                    'endpoint' => $credentials['endpoint'] ?? 'api.mailgun.net',
                ]);
                // Laravel mailgun driver expects services.mailgun
                Config::set('services.mailgun', [
                    'domain' => $credentials['domain'] ?? '',
                    'secret' => $credentials['secret'] ?? '',
                    'endpoint' => $credentials['endpoint'] ?? 'api.mailgun.net',
                ]);
                break;
                
            case 'postmark':
                Config::set($configKey, [
                    'transport' => 'postmark',
                    'token' => $credentials['token'] ?? '',
                ]);
                Config::set('services.postmark', [
                    'token' => $credentials['token'] ?? '',
                ]);
                break;

            default:
                // Fallback to default mailer
                return Config::get('mail.default');
        }

        return $mailerName;
    }
}
