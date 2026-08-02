<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rental extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'facility_id', 'customer_id', 'unit_id', 'move_in_date', 'paid_through',
        'next_bill_date', 'scheduled_move_out', 'moved_out_at', 'monthly_rate',
        'status', 'lease_signed', 'lease_signed_at',
    ];

    protected function casts(): array
    {
        return [
            'move_in_date' => 'date',
            'paid_through' => 'date',
            'next_bill_date' => 'date',
            'scheduled_move_out' => 'date',
            'moved_out_at' => 'date',
            'lease_signed_at' => 'datetime',
            'monthly_rate' => 'decimal:2',
            'lease_signed' => 'boolean',
        ];
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function daysPastDue(): int
    {
        if (! $this->paid_through) {
            return 0;
        }

        return max(0, now()->startOfDay()->diffInDays($this->paid_through, false) * -1);
    }
}
