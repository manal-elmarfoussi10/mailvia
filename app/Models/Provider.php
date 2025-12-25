<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasAuditLogs;

class Provider extends Model
{
    use HasAuditLogs;
    protected $guarded = [];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'last_tested_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
