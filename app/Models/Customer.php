<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'facility_id', 'first_name', 'last_name', 'email', 'phone',
        'address_line1', 'city', 'state', 'postal_code', 'status', 'balance',
        'autopay_enabled', 'late_fee_exempt', 'tax_exempt', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'autopay_enabled' => 'boolean',
            'late_fee_exempt' => 'boolean',
            'tax_exempt' => 'boolean',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function recalculateBalance(): void
    {
        $balance = (float) $this->invoices()->whereIn('status', ['open', 'partial'])->sum('balance');
        $this->update(['balance' => $balance]);
    }
}
