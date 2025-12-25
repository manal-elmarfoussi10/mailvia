<?php

namespace App\Models;

use App\Models\Company; // Added for the relationship
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $guarded = [];

    protected $casts = [
        'mapping' => 'array',
        'errors' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
