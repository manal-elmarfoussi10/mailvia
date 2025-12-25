<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'criteria' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Apply the segment criteria to a Contact query builder.
     */
    public function apply($query)
    {
        $criteria = $this->criteria ?? [];

        foreach ($criteria as $criterion) {
            $field = $criterion['field'] ?? null;
            $operator = $criterion['operator'] ?? '=';
            $value = $criterion['value'] ?? null;

            if (!$field) continue;

            switch ($field) {
                case 'status':
                    $query->where('status', $operator, $value);
                    break;
                case 'tags':
                    // Assuming tags are stored as JSON or in a related table. 
                    // For now, let's assume a 'tags' JSON column on contacts.
                    if ($operator === 'contains') {
                        $query->whereJsonContains('tags', $value);
                    }
                    break;
                case 'last_opened_at':
                    $query->where('last_opened_at', $operator, $value);
                    break;
                case 'created_at':
                    $query->where($field, $operator ?: '>', $value);
                    break;
                case 'opens_count':
                    $query->whereHas('campaignEvents', function($q) {
                        $q->where('type', 'opened');
                    }, $operator ?: '>', $value);
                    break;
                case 'clicks_count':
                    $query->whereHas('campaignEvents', function($q) {
                        $q->where('type', 'clicked');
                    }, $operator ?: '>', $value);
                    break;
                case 'country':
                    $query->where('country', $operator, $value);
                    break;
            }
        }

        return $query;
    }

    public function getContactCountAttribute()
    {
        return $this->apply(Contact::where('company_id', $this->company_id))->count();
    }
}
