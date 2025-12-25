<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $guarded = [];
    protected $casts = [
        'settings' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function providers()
    {
        return $this->hasMany(Provider::class);
    }

    public function senders()
    {
        return $this->hasMany(Sender::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function templates()
    {
        return $this->hasMany(Template::class);
    }

    public function lists()
    {
        return $this->hasMany(ContactList::class);
    }

    public function segments()
    {
        return $this->hasMany(Segment::class);
    }

    public function imports()
    {
        return $this->hasMany(Import::class);
    }

    public function inboxTests()
    {
        return $this->hasMany(InboxTest::class);
    }

    public function suppressions()
    {
        return $this->hasMany(Suppression::class);
    }

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    public function getSetting($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    public function seedLists()
    {
        return $this->hasMany(SeedList::class);
    }
}
