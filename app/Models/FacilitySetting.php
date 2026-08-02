<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilitySetting extends Model
{
    protected $fillable = [
        'facility_id', 'late_days', 'late_fee', 'lockout_days', 'lockout_fee',
        'lien_days', 'lien_fee', 'auction_days', 'proration_mode',
        'disable_partial_payments_when_locked_out',
    ];

    protected function casts(): array
    {
        return [
            'late_fee' => 'decimal:2',
            'lockout_fee' => 'decimal:2',
            'lien_fee' => 'decimal:2',
            'disable_partial_payments_when_locked_out' => 'boolean',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}
