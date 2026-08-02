<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'facility_id', 'name', 'length_ft', 'width_ft', 'height_ft', 'monthly_rate',
        'description', 'climate_controlled', 'show_on_website', 'is_active',
        'sort_order', 'image_path',
    ];

    protected function casts(): array
    {
        return [
            'monthly_rate' => 'decimal:2',
            'length_ft' => 'decimal:2',
            'width_ft' => 'decimal:2',
            'height_ft' => 'decimal:2',
            'climate_controlled' => 'boolean',
            'show_on_website' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function dimensionsLabel(): string
    {
        $parts = array_filter([
            $this->length_ft ? rtrim(rtrim(number_format((float) $this->length_ft, 0), '0'), '.') : null,
            $this->width_ft ? rtrim(rtrim(number_format((float) $this->width_ft, 0), '0'), '.') : null,
            $this->height_ft ? rtrim(rtrim(number_format((float) $this->height_ft, 0), '0'), '.') : null,
        ]);

        return $parts ? implode(' x ', $parts) : $this->name;
    }

    public function availableCount(): int
    {
        return $this->units()->where('status', 'available')->count();
    }
}
