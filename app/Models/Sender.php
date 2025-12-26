<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasAuditLogs;

class Sender extends Model
{
    use HasAuditLogs;
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
