@extends('layouts.tenant')
@section('title', 'My Account')

@section('content')
<div class="grid md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white border p-5 md:col-span-1">
        <div class="text-xs uppercase text-slate-500">Balance due</div>
        <div class="font-display text-4xl font-extrabold mt-2">${{ number_format($customer->balance, 2) }}</div>
        <div class="text-sm text-slate-500 mt-1">Status: {{ $customer->status }}</div>
    </div>
    <form method="POST" action="{{ route('tenant.pay') }}" class="bg-white border p-5 md:col-span-2 space-y-3">
        @csrf
        <h2 class="font-display font-bold text-lg">Make a payment</h2>
        <div class="grid sm:grid-cols-2 gap-3">
            <input type="number" step="0.01" name="amount" value="{{ $customer->balance > 0 ? $customer->balance : ($customer->rentals->first()->monthly_rate ?? 0) }}" class="border px-3 py-2" required>
            <select name="method" class="border px-3 py-2"><option value="card">Card</option><option value="ach">ACH</option></select>
        </div>
        <p class="text-xs text-slate-500">Demo processor — no real charges. PCI-ready architecture for hosted fields later.</p>
        <button class="bg-[#0edb47] text-[#07301a] font-bold px-4 py-2">Pay now</button>
    </form>
</div>

<section class="bg-white border p-5 mb-6">
    <h2 class="font-display font-bold text-lg">My units</h2>
    <ul class="mt-4 space-y-3 text-sm">
        @forelse($customer->rentals->whereNull('moved_out_at') as $rental)
            <li class="border border-slate-100 p-3">
                <div class="font-semibold">Unit {{ $rental->unit->unit_number }} — {{ $rental->unit->unitType->name }}</div>
                <div class="text-slate-600 mt-1">${{ number_format($rental->monthly_rate, 2) }}/mo · {{ $rental->status }} · paid through {{ optional($rental->paid_through)->format('m/d/Y') ?: '—' }}</div>
                <div class="text-slate-600">Gate code: <span class="font-mono font-semibold">{{ $rental->unit->gate_code ?? 'Pending' }}</span></div>
                @if($customer->facility->customers_can_schedule_move_outs)
                <form method="POST" action="{{ route('tenant.move-out') }}" class="mt-3 flex flex-wrap gap-2 items-center">
                    @csrf
                    <input type="hidden" name="rental_id" value="{{ $rental->id }}">
                    <input type="date" name="scheduled_move_out" class="border px-2 py-1" required>
                    <button class="text-xs bg-slate-800 text-white px-2 py-1">Schedule move-out</button>
                </form>
                @endif
            </li>
        @empty
            <li class="text-slate-500">No active rentals.</li>
        @endforelse
    </ul>
</section>

<section class="bg-white border p-5">
    <h2 class="font-display font-bold text-lg">Invoices & payments</h2>
    <div class="grid md:grid-cols-2 gap-6 mt-4 text-sm">
        <ul class="space-y-2">
            @foreach($customer->invoices->take(8) as $invoice)
                <li class="flex justify-between border-b pb-2"><span>{{ $invoice->invoice_number }}</span><span>{{ $invoice->status }} · ${{ number_format($invoice->balance, 2) }}</span></li>
            @endforeach
        </ul>
        <ul class="space-y-2">
            @foreach($customer->payments->take(8) as $payment)
                <li class="flex justify-between border-b pb-2"><span>{{ optional($payment->paid_at)->format('m/d/Y') }} · {{ strtoupper($payment->method) }}</span><span>${{ number_format($payment->amount, 2) }}</span></li>
            @endforeach
        </ul>
    </div>
</section>
@endsection
