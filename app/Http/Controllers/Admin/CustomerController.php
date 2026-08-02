<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Unit;
use App\Services\BillingService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected function facilityId(Request $request): int
    {
        return (int) ($request->user()->facility_id ?: abort(403));
    }

    public function index(Request $request)
    {
        $facilityId = $this->facilityId($request);
        $q = trim((string) $request->get('q', ''));

        $customers = Customer::with(['rentals.unit'])
            ->where('facility_id', $facilityId)
            ->when($request->status, fn ($query) => $query->where('status', $request->status))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhereHas('rentals.unit', fn ($u) => $u->where('unit_number', 'like', "%{$q}%"));
                });
            })
            ->orderBy('last_name')
            ->paginate(40)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'q'));
    }

    public function show(Request $request, Customer $customer)
    {
        abort_unless($customer->facility_id === $this->facilityId($request), 403);
        $customer->load(['rentals.unit.unitType', 'invoices.lines', 'payments']);
        $availableUnits = Unit::where('facility_id', $customer->facility_id)->where('status', 'available')->with('unitType')->orderBy('unit_number')->get();

        return view('admin.customers.show', compact('customer', 'availableUnits'));
    }

    public function store(Request $request)
    {
        $facilityId = $this->facilityId($request);
        $data = $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'email' => 'nullable|email|max:160',
            'phone' => 'nullable|string|max:40',
        ]);

        Customer::create([...$data, 'facility_id' => $facilityId, 'status' => 'active']);

        return back()->with('success', 'Customer created.');
    }

    public function rentUnit(Request $request, Customer $customer, BillingService $billing)
    {
        abort_unless($customer->facility_id === $this->facilityId($request), 403);
        $data = $request->validate([
            'unit_id' => 'required|exists:units,id',
            'collect_payment' => 'nullable|boolean',
        ]);

        $unit = Unit::where('id', $data['unit_id'])->where('facility_id', $customer->facility_id)->firstOrFail();
        $billing->rentUnit($unit, $customer, $request->user(), (bool) ($data['collect_payment'] ?? true));

        return back()->with('success', 'Unit rented to customer.');
    }

    public function collectPayment(Request $request, Customer $customer, BillingService $billing)
    {
        abort_unless($customer->facility_id === $this->facilityId($request), 403);
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|in:card,ach,cash,check,other',
            'notes' => 'nullable|string|max:1000',
        ]);

        $billing->recordPayment($customer, (float) $data['amount'], $data['method'], null, $request->user(), $data['notes'] ?? null);

        return back()->with('success', 'Payment recorded.');
    }
}
