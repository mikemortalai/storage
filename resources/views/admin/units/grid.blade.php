@extends('layouts.admin')
@section('title', 'Unit Grid')
@section('heading', 'Unit Grid')

@section('content')
<div class="flex flex-wrap gap-2 mb-4 text-xs">
    @foreach(\App\Models\Unit::STATUS_COLORS as $status => $color)
        <span class="inline-flex items-center gap-1"><span class="h-3 w-3" style="background:{{ $color }}"></span>{{ $status }}</span>
    @endforeach
</div>
<div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 gap-2">
    @foreach($units as $unit)
        <div class="p-2 text-center text-xs font-semibold border border-black/10" style="background:{{ $unit->statusColor() }}" title="{{ $unit->unitType->name }} · {{ $unit->status }}">
            {{ $unit->unit_number }}
        </div>
    @endforeach
</div>
@endsection
