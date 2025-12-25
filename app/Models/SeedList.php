<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeedList extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_id'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(SeedListEmail::class);
    }
}
