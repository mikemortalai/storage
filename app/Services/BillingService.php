<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingService
{
    public function createRentInvoice(Rental $rental, ?string $description = null): Invoice
    {
        $invoice = Invoice::create([
            'facility_id' => $rental->facility_id,
            'customer_id' => $rental->customer_id,
            'rental_id' => $rental->id,
            'invoice_number' => 'INV-'.strtoupper(Str::random(8)),
            'due_date' => now()->toDateString(),
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
            'subtotal' => $rental->monthly_rate,
            'tax' => 0,
            'total' => $rental->monthly_rate,
            'amount_paid' => 0,
            'balance' => $rental->monthly_rate,
            'status' => 'open',
            'description' => $description ?? 'Monthly rent - Unit '.$rental->unit->unit_number,
        ]);

        InvoiceLine::create([
            'invoice_id' => $invoice->id,
            'description' => $invoice->description,
            'category' => 'rent',
            'quantity' => 1,
            'unit_amount' => $rental->monthly_rate,
            'amount' => $rental->monthly_rate,
        ]);

        $rental->customer->recalculateBalance();

        return $invoice;
    }

    public function recordPayment(
        Customer $customer,
        float $amount,
        string $method = 'card',
        ?Invoice $invoice = null,
        ?User $receivedBy = null,
        ?string $notes = null,
    ): Payment {
        return DB::transaction(function () use ($customer, $amount, $method, $invoice, $receivedBy, $notes) {
            if (! $invoice) {
                $invoice = $customer->invoices()
                    ->whereIn('status', ['open', 'partial'])
                    ->orderBy('due_date')
                    ->first();
            }

            $payment = Payment::create([
                'facility_id' => $customer->facility_id,
                'customer_id' => $customer->id,
                'invoice_id' => $invoice?->id,
                'amount' => $amount,
                'method' => $method,
                'status' => 'completed',
                'reference' => 'PAY-'.strtoupper(Str::random(10)),
                'processor' => 'demo',
                'notes' => $notes,
                'received_by' => $receivedBy?->id,
                'paid_at' => now(),
            ]);

            if ($invoice) {
                $paid = min((float) $invoice->balance, $amount);
                $invoice->amount_paid = (float) $invoice->amount_paid + $paid;
                $invoice->balance = max(0, (float) $invoice->total - (float) $invoice->amount_paid);
                $invoice->status = $invoice->balance <= 0 ? 'paid' : 'partial';
                $invoice->save();

                if ($invoice->rental && $invoice->status === 'paid') {
                    $rental = $invoice->rental;
                    $rental->paid_through = now()->endOfMonth()->toDateString();
                    $rental->next_bill_date = now()->addMonth()->startOfMonth()->toDateString();
                    if (in_array($rental->status, ['late', 'locked_out', 'pre_lien'], true)) {
                        $rental->status = 'active';
                        $rental->unit->update(['status' => 'rented']);
                        $customer->update(['status' => 'active']);
                    }
                    $rental->save();
                }
            }

            $customer->recalculateBalance();

            return $payment;
        });
    }

    public function rentUnit(
        Unit $unit,
        Customer $customer,
        ?User $staff = null,
        bool $collectFirstMonth = true,
    ): Rental {
        return DB::transaction(function () use ($unit, $customer, $collectFirstMonth) {
            abort_unless($unit->status === 'available', 422, 'Unit is not available.');

            $rate = $unit->effectiveRate();

            $rental = Rental::create([
                'facility_id' => $unit->facility_id,
                'customer_id' => $customer->id,
                'unit_id' => $unit->id,
                'move_in_date' => now()->toDateString(),
                'paid_through' => null,
                'next_bill_date' => now()->toDateString(),
                'monthly_rate' => $rate,
                'status' => 'active',
                'lease_signed' => true,
                'lease_signed_at' => now(),
            ]);

            $unit->update([
                'status' => 'rented',
                'gate_code' => $unit->gate_code ?: (string) random_int(1000, 9999),
            ]);

            $customer->update(['status' => 'active']);

            $invoice = $this->createRentInvoice($rental, 'Move-in rent - Unit '.$unit->unit_number);

            if ($collectFirstMonth) {
                $this->recordPayment($customer, (float) $invoice->total, 'card', $invoice, null, 'Online / move-in payment');
            }

            return $rental->fresh(['unit', 'customer', 'invoices']);
        });
    }
}
