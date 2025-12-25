<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company; // Added this use statement for Company model
use App\Models\Campaign; // Added this use statement for Campaign model

class Template extends Model
{
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    // Helper to interpolate variables in content
    public function interpolate(array $variables): array
    {
        $html = $this->content_html;
        $text = $this->content_text;
        $subject = $this->subject;

        foreach ($variables as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $html = str_replace($placeholder, $value, $html);
            $text = str_replace($placeholder, $value, $text);
            $subject = str_replace($placeholder, $value, $subject);
        }

        return [
            'html' => $html,
            'text' => $text,
            'subject' => $subject,
        ];
    }
}
