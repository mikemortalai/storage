@extends('layouts.admin')
@section('title', $customer->fullName())
@section('heading', $customer->fullName())

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <section class="bg-white border p-5 lg:col-span-2 space-y-4">
        <div class="grid sm:grid-cols-2 gap-3 text-sm">
            <div><span class="text-slate-500">Email</span><div class="font-semibold">{{ $customer->email ?: '—' }}</div></div>
            <div><span class="text-slate-500">Phone</span><div class="font-semibold">{{ $customer->phone ?: '—' }}</div></div>
            <div><span class="text-slate-500">Status</span><div class="font-semibold">{{ $customer->status }}</div></div>
            <div><span class="text-slate-500">Balance</span><div class="font-semibold">${{ number_format($customer->balance, 2) }}</div></div>
        </div>

        <h3 class="font-display font-bold mt-6">Rentals</h3>
        <ul class="text-sm space-y-2">
            @foreach($customer->rentals as $rental)
                <li class="border border-slate-100 p-3">
                    Unit {{ $rental->unit->unit_number }} · {{ $rental->unit->unitType->name }} · {{ $rental->status }}
                    · ${{ number_format($rental->monthly_rate, 2) }}/mo
                    · paid thru {{ optional($rental->paid_through)->format('m/d/Y') ?: '—' }}
                </li>
            @endforeach
        </ul>

        <h3 class="font-display font-bold mt-6">Invoices</h3>
        <ul class="text-sm space-y-2">
            @foreach($customer->invoices as $invoice)
                <li class="flex justify-between border-b pb-2">
                    <span>{{ $invoice->invoice_number }} · {{ $invoice->description }}</span>
                    <span>{{ $invoice->status }} · ${{ number_format($invoice->balance, 2) }}</span>
                </li>
            @endforeach
        </ul>
    </section>

    <aside class="space-y-4">
        <form method="POST" action="{{ route('admin.customers.pay', $customer) }}" class="bg-white border p-4 space-y-3">
            @csrf
            <h3 class="font-display font-bold">Make payment</h3>
            <input type="number" step="0.01" name="amount" value="{{ $customer->balance }}" class="w-full border px-2 py-2 text-sm" required>
            <select name="method" class="w-full border px-2 py-2 text-sm">
                @foreach(['card','ach','cash','check','other'] as $m)<option value="{{ $m }}">{{ strtoupper($m) }}</option>@endforeach
            </select>
            <input name="notes" placeholder="Notes" class="w-full border px-2 py-2 text-sm">
            <button class="w-full bg-emerald-600 text-white py-2 text-sm font-semibold">Collect</button>
        </form>

        <form method="POST" action="{{ route('admin.customers.rent', $customer) }}" class="bg-white border p-4 space-y-3">
            @csrf
            <h3 class="font-display font-bold">Rent unit</h3>
            <select name="unit_id" class="w-full border px-2 py-2 text-sm" required>
                @foreach($availableUnits as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->unit_number }} — {{ $unit->unitType->name }}</option>
                @endforeach
            </select>
            <label class="text-sm flex gap-2 items-center"><input type="checkbox" name="collect_payment" value="1" checked> Collect first month</label>
            <button class="w-full bg-[#114393] text-white py-2 text-sm font-semibold">Rent</button>
        </form>
    </aside>
</div>
@endsection
