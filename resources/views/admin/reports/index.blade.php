@extends('layouts.admin')
@section('title', 'Reports')
@section('heading', 'Reports')

@section('content')
<div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach([
        ['Occupancy', route('admin.reports.occupancy'), 'By unit type with economic occupancy'],
        ['Collections', route('admin.reports.collections'), 'Balances and days behind'],
        ['Rent Roll', route('admin.reports.rent-roll'), 'Active rentals, rates, paid-through'],
        ['Unit Status', route('admin.reports.unit-status'), 'Counts by status'],
        ['Monthly Deposits', route('admin.reports.deposits'), 'Completed payments this month'],
        ['Customer CSV', route('admin.reports.customers-csv'), 'Export tenants'],
    ] as [$title, $url, $desc])
        <a href="{{ $url }}" class="bg-white border border-slate-200 p-5 hover:border-[#114393] transition">
            <div class="font-display text-lg font-bold">{{ $title }}</div>
            <p class="text-sm text-slate-600 mt-2">{{ $desc }}</p>
        </a>
    @endforeach
</div>
@endsection
