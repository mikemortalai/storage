@extends('layouts.admin')
@section('title', 'Collections')
@section('heading', 'Collections')
@section('content')
<div class="bg-white border overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-slate-50 text-left"><tr>
<th class="px-3 py-2">Customer</th><th class="px-3 py-2">Unit</th><th class="px-3 py-2">Status</th><th class="px-3 py-2">Balance</th><th class="px-3 py-2">Days behind</th>
</tr></thead>
<tbody>
@foreach($customers as $customer)
@php $rental = $customer->rentals->whereNull('moved_out_at')->first(); @endphp
<tr class="border-t">
<td class="px-3 py-2"><a href="{{ route('admin.customers.show', $customer) }}" class="text-[#114393] font-semibold">{{ $customer->fullName() }}</a></td>
<td class="px-3 py-2">{{ $rental?->unit?->unit_number ?? '—' }}</td>
<td class="px-3 py-2">{{ $rental?->status ?? $customer->status }}</td>
<td class="px-3 py-2 font-semibold">${{ number_format($customer->balance, 2) }}</td>
<td class="px-3 py-2">{{ $rental?->daysPastDue() ?? 0 }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
