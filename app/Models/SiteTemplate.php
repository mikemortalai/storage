<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteTemplate extends Model
{
    protected $fillable = [
        'name', 'slug', 'tagline', 'theme_tokens', 'starter_pages', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'theme_tokens' => 'array',
            'starter_pages' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
