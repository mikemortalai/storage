@extends('layouts.admin')
@section('title', 'Rent Roll')
@section('heading', 'Rent Roll')
@section('content')
<div class="bg-white border overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-slate-50 text-left"><tr>
<th class="px-3 py-2">Unit</th><th class="px-3 py-2">Type</th><th class="px-3 py-2">Tenant</th>
<th class="px-3 py-2">Rate</th><th class="px-3 py-2">Paid through</th><th class="px-3 py-2">Status</th>
</tr></thead>
<tbody>
@foreach($rentals as $rental)
<tr class="border-t">
<td class="px-3 py-2">{{ $rental->unit->unit_number }}</td>
<td class="px-3 py-2">{{ $rental->unit->unitType->name }}</td>
<td class="px-3 py-2">{{ $rental->customer->fullName() }}</td>
<td class="px-3 py-2">${{ number_format($rental->monthly_rate, 2) }}</td>
<td class="px-3 py-2">{{ optional($rental->paid_through)->format('m/d/Y') }}</td>
<td class="px-3 py-2">{{ $rental->status }}</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
