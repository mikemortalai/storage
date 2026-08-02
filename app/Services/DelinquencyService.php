<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Rental;
use Illuminate\Support\Str;

class DelinquencyService
{
    public function run(Facility $facility): array
    {
        $settings = $facility->settings;
        $summary = ['late' => 0, 'locked_out' => 0, 'lien' => 0, 'auction' => 0, 'fees' => 0];

        $rentals = Rental::with(['customer', 'unit'])
            ->where('facility_id', $facility->id)
            ->whereNull('moved_out_at')
            ->whereIn('status', ['active', 'late', 'locked_out', 'pre_lien', 'lien'])
            ->get();

        foreach ($rentals as $rental) {
            $days = $rental->daysPastDue();
            if ($days <= 0) {
                continue;
            }

            if ($days >= $settings->auction_days) {
                $this->applyStatus($rental, 'auction', $summary);
            } elseif ($days >= $settings->lien_days) {
                $this->applyStatus($rental, 'lien', $summary);
                $summary['fees'] += $this->ensureFee($rental, 'lien_fee', 'Lien Fee', (float) $settings->lien_fee);
            } elseif ($days >= $settings->lockout_days) {
                $this->applyStatus($rental, 'locked_out', $summary);
                $summary['fees'] += $this->ensureFee($rental, 'lockout_fee', 'Lock Out Fee', (float) $settings->lockout_fee);
            } elseif ($days >= $settings->late_days) {
                $this->applyStatus($rental, 'late', $summary);
                if (! $rental->customer->late_fee_exempt) {
                    $summary['fees'] += $this->ensureFee($rental, 'late_fee', 'Late Fee', (float) $settings->late_fee);
                }
            }
        }

        return $summary;
    }

    protected function applyStatus(Rental $rental, string $status, array &$summary): void
    {
        if ($rental->status !== $status) {
            $rental->update(['status' => $status]);
            $rental->unit->update(['status' => $status === 'active' ? 'rented' : $status]);
            $customerStatus = match ($status) {
                'locked_out', 'lien', 'auction' => 'lockout',
                'late' => 'late',
                default => 'active',
            };
            $rental->customer->update(['status' => $customerStatus]);
        }

        $summary[$status] = ($summary[$status] ?? 0) + 1;
    }

    protected function ensureFee(Rental $rental, string $category, string $label, float $amount): float
    {
        $exists = InvoiceLine::query()
            ->where('category', $category)
            ->whereHas('invoice', function ($q) use ($rental) {
                $q->where('rental_id', $rental->id)
                    ->where('created_at', '>=', now()->subDays(35));
            })
            ->exists();

        if ($exists || $amount <= 0) {
            return 0;
        }

        $invoice = Invoice::create([
            'facility_id' => $rental->facility_id,
            'customer_id' => $rental->customer_id,
            'rental_id' => $rental->id,
            'invoice_number' => 'FEE-'.strtoupper(Str::random(8)),
            'due_date' => now()->toDateString(),
            'subtotal' => $amount,
            'tax' => 0,
            'total' => $amount,
            'amount_paid' => 0,
            'balance' => $amount,
            'status' => 'open',
            'description' => $label.' - Unit '.$rental->unit->unit_number,
        ]);

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => $label,
            'category' => $category,
            'quantity' => 1,
            'unit_amount' => $amount,
            'amount' => $amount,
        ]);

        $rental->customer->recalculateBalance();

        return $amount;
    }
}
