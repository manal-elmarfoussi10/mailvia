<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasAuditLogs;

class Sender extends Model
{
    use HasAuditLogs;
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function checkAuth(): array
    {
        // If linked to a Domain model, use its cached verification status
        if ($this->domain_id && $this->domain) {
            return [
                'spf' => $this->domain->spf_verified,
                'dkim' => $this->domain->dkim_verified,
                'dmarc' => $this->domain->dmarc_verified,
                'ssl' => true, // Assuming SSL is standard
            ];
        }

        // Otherwise, perform real-time check (DKIM is impossible without selector)
        $domain = substr(strrchr($this->email, "@"), 1);
        $spf = false;
        $dmarc = false;

        // SPF Check
        $txtRecords = @dns_get_record($domain, DNS_TXT);
        if ($txtRecords) {
            foreach ($txtRecords as $record) {
                if (isset($record['txt']) && str_contains($record['txt'], 'v=spf1')) {
                    $spf = true;
                    break;
                }
            }
        }

        // DMARC Check
        $dmarcRecords = @dns_get_record("_dmarc.$domain", DNS_TXT);
        if ($dmarcRecords) {
            foreach ($dmarcRecords as $record) {
                 if (isset($record['txt']) && str_contains($record['txt'], 'v=DMARC1')) {
                    $dmarc = true;
                    break;
                }
            }
        }

        return [
            'spf' => $spf,
            'dkim' => null, // Cannot verify without selector
            'dmarc' => $dmarc,
            'ssl' => true,
        ];
    }
}
