<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSuggestion extends Model
{
    protected $fillable = [
        'facility_id', 'user_id', 'type', 'prompt', 'suggestion', 'status', 'meta', 'acted_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'acted_at' => 'datetime',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}
