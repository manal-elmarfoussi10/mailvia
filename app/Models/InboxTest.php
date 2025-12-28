<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboxTest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'seed_emails' => 'array',
        'results' => 'array',
        'sent_at' => 'datetime',
    ];

    protected function serializeSeedEmails($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }
        return $value;
    }

    protected function unserializeSeedEmails($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    public function setSeedEmailsAttribute($value)
    {
        $this->attributes['seed_emails'] = $this->serializeSeedEmails($value);
    }

    public function getSeedEmailsAttribute($value)
    {
        return $this->unserializeSeedEmails($value);
    }

    public function setResultsAttribute($value)
    {
        $this->attributes['results'] = $this->serializeResults($value);
    }

    public function getResultsAttribute($value)
    {
        return $this->unserializeResults($value);
    }

    protected function serializeResults($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        }
        return $value;
    }

    protected function unserializeResults($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        return is_array($value) ? $value : [];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sender()
    {
        return $this->belongsTo(Sender::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function getPlacementStats(): array
    {
        $results = is_array($this->results) ? $this->results : [];
        $seedEmails = is_array($this->seed_emails) ? $this->seed_emails : [];
        $total = count($seedEmails);

        if ($total === 0) return ['inbox' => 0, 'spam' => 0, 'missing' => 0, 'counts' => ['inbox' => 0, 'spam' => 0, 'missing' => 0, 'total' => 0]];

        $inbox = 0;
        $spam = 0;

        foreach ($results as $email => $placement) {
            if ($placement === 'inbox') $inbox++;
            elseif ($placement === 'spam') $spam++;
        }

        $missing = $total - ($inbox + $spam);

        return [
            'inbox' => $total > 0 ? round(($inbox / $total) * 100) : 0,
            'spam' => $total > 0 ? round(($spam / $total) * 100) : 0,
            'missing' => $total > 0 ? round(($missing / $total) * 100) : 0,
            'counts' => [
                'inbox' => $inbox,
                'spam' => $spam,
                'missing' => $missing,
                'total' => $total
            ]
        ];
    }

    public function getProviderStats(): array
    {
        // This assumes results or seed_emails contains provider info or we can infer it
        // For simplicity, let's group by domain of the seed email
        $stats = [];
        $results = $this->results ?? [];

        foreach ($this->seed_emails ?? [] as $email) {
            $atPos = strrpos($email, "@");
            $domain = $atPos !== false ? substr($email, $atPos + 1) : 'invalid';
            $provider = match (true) {
                str_contains($domain, 'gmail') => 'Gmail',
                str_contains($domain, 'outlook') || str_contains($domain, 'hotmail') => 'Microsoft',
                str_contains($domain, 'yahoo') => 'Yahoo',
                default => 'Others',
            };

            if (!isset($stats[$provider])) {
                $stats[$provider] = ['total' => 0, 'inbox' => 0, 'spam' => 0, 'missing' => 0];
            }

            $stats[$provider]['total']++;
            $placement = $results[$email] ?? 'missing';
            if ($placement === 'inbox') $stats[$provider]['inbox']++;
            elseif ($placement === 'spam') $stats[$provider]['spam']++;
            else $stats[$provider]['missing']++;
        }

        return $stats;
    }
}
