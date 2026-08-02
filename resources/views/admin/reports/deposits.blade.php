@extends('layouts.admin')
@section('title', 'Deposits')
@section('heading', 'Monthly Deposits')
@section('content')
<form class="mb-4"><input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" class="border px-3 py-2 text-sm"> <button class="bg-[#114393] text-white px-3 py-2 text-sm">Filter</button></form>
<div class="bg-white border overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-slate-50 text-left"><tr>
<th class="px-3 py-2">Date</th><th class="px-3 py-2">Customer</th><th class="px-3 py-2">Method</th><th class="px-3 py-2">Reference</th><th class="px-3 py-2">Amount</th>
</tr></thead>
<tbody>
@foreach($payments as $payment)
<tr class="border-t">
<td class="px-3 py-2">{{ optional($payment->paid_at)->format('m/d/Y') }}</td>
<td class="px-3 py-2">{{ $payment->customer?->fullName() }}</td>
<td class="px-3 py-2">{{ strtoupper($payment->method) }}</td>
<td class="px-3 py-2">{{ $payment->reference }}</td>
<td class="px-3 py-2 font-semibold">${{ number_format($payment->amount, 2) }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
<div class="mt-4 font-display text-xl font-bold">Total: ${{ number_format($payments->sum('amount'), 2) }}</div>
@endsection
