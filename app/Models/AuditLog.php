<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'metadata',
        'ip_address',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }
}
