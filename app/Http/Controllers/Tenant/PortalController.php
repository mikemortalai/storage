<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\BillingService;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    protected function customer(Request $request): Customer
    {
        $customer = Customer::with(['rentals.unit.unitType', 'invoices', 'payments'])
            ->findOrFail($request->user()->customer_id);

        return $customer;
    }

    public function dashboard(Request $request)
    {
        $customer = $this->customer($request);

        return view('tenant.dashboard', compact('customer'));
    }

    public function pay(Request $request, BillingService $billing)
    {
        $customer = $this->customer($request);
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:card,ach',
        ]);

        if ($customer->status === 'lockout' && $customer->facility->settings?->disable_partial_payments_when_locked_out) {
            $due = (float) $customer->balance;
            if ((float) $data['amount'] + 0.001 < $due) {
                return back()->with('error', 'Partial payments are disabled while locked out. Pay the full balance of $'.number_format($due, 2).'.');
            }
        }

        $billing->recordPayment($customer, (float) $data['amount'], $data['method'], null, null, 'Tenant portal payment');

        return back()->with('success', 'Payment received. Thank you!');
    }

    public function profile(Request $request)
    {
        $customer = $this->customer($request);

        return view('tenant.profile', compact('customer'));
    }

    public function updateProfile(Request $request)
    {
        $customer = $this->customer($request);
        abort_unless($customer->facility->customers_can_edit_profile, 403);

        $data = $request->validate([
            'phone' => 'nullable|string|max:40',
            'address_line1' => 'nullable|string|max:160',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:32',
            'postal_code' => 'nullable|string|max:20',
            'autopay_enabled' => 'nullable|boolean',
        ]);

        $customer->update([
            ...$data,
            'autopay_enabled' => (bool) ($data['autopay_enabled'] ?? false),
        ]);

        return back()->with('success', 'Profile updated.');
    }

    public function scheduleMoveOut(Request $request)
    {
        $customer = $this->customer($request);
        abort_unless($customer->facility->customers_can_schedule_move_outs, 403);

        $data = $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'scheduled_move_out' => 'required|date|after:today',
        ]);

        $rental = $customer->rentals()->where('id', $data['rental_id'])->whereNull('moved_out_at')->firstOrFail();
        $minDate = now()->addDays($customer->facility->move_out_days_restriction)->startOfDay();
        if (now()->parse($data['scheduled_move_out'])->lt($minDate)) {
            return back()->with('error', 'Move-out must be at least '.$customer->facility->move_out_days_restriction.' days out.');
        }

        $rental->update([
            'scheduled_move_out' => $data['scheduled_move_out'],
            'status' => 'moving_out',
        ]);
        $rental->unit->update(['status' => 'moving_out']);

        return back()->with('success', 'Move-out scheduled.');
    }
}
