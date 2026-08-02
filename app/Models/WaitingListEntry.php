<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaitingListEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'facility_id', 'unit_type_id', 'name', 'email', 'phone',
        'texting_consent', 'desired_move_in', 'notes', 'status',
    ];

    protected function casts(): array
    {
        return [
            'texting_consent' => 'boolean',
            'desired_move_in' => 'date',
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
}
