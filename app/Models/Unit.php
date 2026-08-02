<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    public const STATUS_COLORS = [
        'auction' => '#FFEA5E',
        'available' => '#139771',
        'late' => '#D13500',
        'lien' => '#FFC153',
        'locked_out' => '#916397',
        'moving_out' => '#97524F',
        'pending' => '#BCD191',
        'pre_lien' => '#F28500',
        'rented' => '#4A6197',
        'reserved' => '#81CDD1',
        'unavailable' => '#959790',
    ];

    protected $fillable = [
        'facility_id', 'unit_type_id', 'unit_number', 'status', 'monthly_rate',
        'gate_code', 'map_x', 'map_y', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'monthly_rate' => 'decimal:2',
            'map_x' => 'decimal:2',
            'map_y' => 'decimal:2',
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

    public function activeRental(): HasOne
    {
        return $this->hasOne(Rental::class)->whereNull('moved_out_at')->latestOfMany();
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? '#959790';
    }

    public function effectiveRate(): float
    {
        return (float) ($this->monthly_rate ?? $this->unitType?->monthly_rate ?? 0);
    }
}
