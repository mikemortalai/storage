@extends('layouts.admin')
@section('title', 'Units')
@section('heading', 'Units')

@section('content')
<div class="flex flex-wrap gap-2 mb-4 text-xs">
    @foreach($statusCounts as $status => $count)
        <a href="?status={{ $status }}" class="px-2 py-1 border border-slate-300 bg-white">{{ str_replace('_',' ', $status) }}: {{ $count }}</a>
    @endforeach
</div>

<form method="POST" action="{{ route('admin.units.store') }}" class="bg-white border border-slate-200 p-4 mb-6 grid md:grid-cols-5 gap-3 items-end">
    @csrf
    <div>
        <label class="text-xs font-semibold">Unit type</label>
        <select name="unit_type_id" class="w-full border px-2 py-2 text-sm" required>
            @foreach($unitTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach
        </select>
    </div>
    <div>
        <label class="text-xs font-semibold">Unit #</label>
        <input name="unit_number" class="w-full border px-2 py-2 text-sm" required>
    </div>
    <div>
        <label class="text-xs font-semibold">Rate</label>
        <input name="monthly_rate" type="number" step="0.01" class="w-full border px-2 py-2 text-sm">
    </div>
    <div>
        <label class="text-xs font-semibold">Status</label>
        <select name="status" class="w-full border px-2 py-2 text-sm">
            <option value="available">available</option>
            <option value="unavailable">unavailable</option>
        </select>
    </div>
    <button class="bg-[#114393] text-white px-3 py-2 text-sm font-semibold">Add unit</button>
</form>

<div class="bg-white border border-slate-200 overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left">
            <tr>
                <th class="px-3 py-2">Unit</th>
                <th class="px-3 py-2">Type</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Tenant</th>
                <th class="px-3 py-2">Rate</th>
                <th class="px-3 py-2">Gate</th>
                <th class="px-3 py-2"></th>
            </tr>
        </thead>
        <tbody>
        @foreach($units as $unit)
            <tr class="border-t border-slate-100">
                <td class="px-3 py-2 font-semibold">{{ $unit->unit_number }}</td>
                <td class="px-3 py-2">{{ $unit->unitType->name }}</td>
                <td class="px-3 py-2"><span class="inline-block px-2 py-0.5 text-xs font-semibold" style="background:{{ $unit->statusColor() }}">{{ $unit->status }}</span></td>
                <td class="px-3 py-2">{{ $unit->activeRental?->customer?->fullName() ?? '—' }}</td>
                <td class="px-3 py-2">${{ number_format($unit->effectiveRate(), 2) }}</td>
                <td class="px-3 py-2">{{ $unit->gate_code ?? '—' }}</td>
                <td class="px-3 py-2">
                    <form method="POST" action="{{ route('admin.units.update', $unit) }}" class="flex gap-1">
                        @csrf @method('PATCH')
                        <select name="status" class="border text-xs px-1 py-1">
                            @foreach(array_keys(\App\Models\Unit::STATUS_COLORS) as $status)
                                <option value="{{ $status }}" @selected($unit->status===$status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="monthly_rate" value="{{ $unit->monthly_rate }}">
                        <input type="hidden" name="gate_code" value="{{ $unit->gate_code }}">
                        <button class="text-xs bg-slate-800 text-white px-2">Save</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $units->links() }}</div>
@endsection
