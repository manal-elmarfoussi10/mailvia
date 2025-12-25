<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasAuditLogs;
use Illuminate\Support\Str;

class Domain extends Model
{
    use HasAuditLogs;

    protected $fillable = [
        'company_id',
        'domain',
        'verification_token',
        'status',
        'spf_verified',
        'dkim_verified',
        'dmarc_verified',
    ];

    protected $casts = [
        'spf_verified' => 'boolean',
        'dkim_verified' => 'boolean',
        'dmarc_verified' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($domain) {
            $domain->verification_token = 'mailvia-verify-' . Str::random(32);
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function senders()
    {
        return $this->hasMany(Sender::class);
    }
}
