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
     * @return string The mailer driver name to use (e.g., 'dynamic_provider')
     */
    public static function configureMailer(Provider $provider): string
    {
        $credentials = $provider->credentials;
        $driver = $provider->type;
        $configKey = 'mail.mailers.dynamic_provider';

        switch ($driver) {
            case 'ses':
                Config::set($configKey, [
                    'transport' => 'ses',
                    'key' => $credentials['key'] ?? '',
                    'secret' => $credentials['secret'] ?? '',
                    'region' => $credentials['region'] ?? 'us-east-1',
                ]);
                // Ensure AWS service config is also set if using standard Laravel SES driver
                Config::set('services.ses', [
                    'key' => $credentials['key'] ?? '',
                    'secret' => $credentials['secret'] ?? '',
                    'region' => $credentials['region'] ?? 'us-east-1',
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
                // Fallback or throw error? For now, fallback to default 'smtp'
                return Config::get('mail.default');
        }

        return 'dynamic_provider';
    }
}
