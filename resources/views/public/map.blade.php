@extends('layouts.public')

@section('title', 'Site Map | '.$facility->name)

@section('content')
<section class="hero-plane pt-28 pb-16">
    <div class="mx-auto max-w-6xl px-4">
        <h1 class="font-display text-4xl md:text-5xl font-extrabold text-white">Facility Map</h1>
        <p class="mt-3 text-white/85">{{ $facility->fullAddress() }}</p>
    </div>
</section>
<section class="mx-auto max-w-6xl px-4 py-12">
    <div class="relative h-[420px] border border-slate-300 bg-gradient-to-br from-slate-100 to-slate-200 overflow-hidden">
        @foreach($units as $unit)
            <div title="Unit {{ $unit->unit_number }} — {{ $unit->status }}"
                 class="absolute h-3 w-3 rounded-sm"
                 style="left: {{ $unit->map_x }}%; top: {{ $unit->map_y }}%; background: {{ $unit->statusColor() }};">
            </div>
        @endforeach
    </div>
    <div class="mt-6 flex flex-wrap gap-3 text-xs">
        @foreach(\App\Models\Unit::STATUS_COLORS as $status => $color)
            <span class="inline-flex items-center gap-2"><span class="h-3 w-3" style="background:{{ $color }}"></span>{{ str_replace('_',' ', $status) }}</span>
        @endforeach
    </div>
</section>
@endsection
