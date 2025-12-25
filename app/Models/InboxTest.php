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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function getPlacementStats(): array
    {
        $results = $this->results ?? [];
        $total = count($this->seed_emails ?? []);
        
        if ($total === 0) return ['inbox' => 0, 'spam' => 0, 'missing' => 0];

        $inbox = 0;
        $spam = 0;
        
        foreach ($results as $email => $placement) {
            if ($placement === 'inbox') $inbox++;
            elseif ($placement === 'spam') $spam++;
        }

        $missing = $total - ($inbox + $spam);

        return [
            'inbox' => round(($inbox / $total) * 100),
            'spam' => round(($spam / $total) * 100),
            'missing' => round(($missing / $total) * 100),
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
            $domain = substr(strrchr($email, "@"), 1);
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
