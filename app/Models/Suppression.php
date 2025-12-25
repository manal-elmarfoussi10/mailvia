<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\HasAuditLogs;

class Suppression extends Model
{
    use HasAuditLogs;
    protected $fillable = [
        'company_id',
        'email',
        'reason',
        'suppressed_at'
    ];

    protected $casts = [
        'suppressed_at' => 'datetime'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Check if an email is suppressed for a given company.
     */
    public static function isSuppressed(int $companyId, string $email): bool
    {
        return self::where('company_id', $companyId)
            ->where('email', strtolower($email))
            ->exists();
    }
}
