@extends('layouts.admin')
@section('title', 'Occupancy')
@section('heading', 'Occupancy Report')
@section('content')
<div class="bg-white border overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-slate-50 text-left"><tr>
<th class="px-3 py-2">Unit type</th><th class="px-3 py-2">Units</th><th class="px-3 py-2">Occupied</th>
<th class="px-3 py-2">Occ %</th><th class="px-3 py-2">Rate</th><th class="px-3 py-2">Sq Ft</th><th class="px-3 py-2">Econ Occ</th>
</tr></thead>
<tbody>
@foreach($rows as $row)
<tr class="border-t">
<td class="px-3 py-2">{{ $row['name'] }}</td>
<td class="px-3 py-2">{{ $row['units'] }}</td>
<td class="px-3 py-2">{{ $row['occupied'] }}</td>
<td class="px-3 py-2">{{ $row['occ_pct'] }}%</td>
<td class="px-3 py-2">${{ number_format($row['monthly_rate'], 2) }}</td>
<td class="px-3 py-2">{{ number_format($row['sqft'], 0) }}</td>
<td class="px-3 py-2">{{ $row['economic_occ'] }}%</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
