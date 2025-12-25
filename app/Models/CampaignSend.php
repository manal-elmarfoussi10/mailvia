<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignSend extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'contact_id',
        'status',
        'sent_at',
        'delivered_at',
        'bounced_at',
        'failed_at',
        'error_message',
        'attempts',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'bounced_at' => 'datetime',
        'failed_at' => 'datetime',
        'attempts' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'attempts' => $this->attempts + 1,
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markAsBounced(string $reason = null): void
    {
        $this->update([
            'status' => 'bounced',
            'bounced_at' => now(),
            'error_message' => $reason,
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'error_message' => $error,
            'attempts' => $this->attempts + 1,
        ]);
    }
}
