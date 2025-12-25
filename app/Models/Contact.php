<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tags' => 'array',
        'last_opened_at' => 'datetime',
        'last_clicked_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::created(function ($contact) {
            $automations = \App\Models\Automation::where('company_id', $contact->company_id)
                ->where('trigger_event', 'contact_created')
                ->where('is_active', true)
                ->get();

            foreach ($automations as $automation) {
                \App\Jobs\SendAutomationEmailJob::dispatch($automation, $contact);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function lists()
    {
        return $this->belongsToMany(ContactList::class);
    }

    public function campaignSends()
    {
        return $this->hasMany(CampaignSend::class);
    }

    public function campaignEvents()
    {
        return $this->hasMany(CampaignEvent::class);
    }
}
