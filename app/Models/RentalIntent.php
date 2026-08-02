<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalIntent extends Model
{
    protected $fillable = [
        'facility_id', 'unit_type_id', 'unit_id', 'name', 'email', 'phone',
        'desired_move_in', 'status', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'desired_move_in' => 'date',
            'meta' => 'array',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function unitType(): BelongsTo
    {
        return $this->belongsTo(UnitType::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
