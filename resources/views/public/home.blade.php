@extends('layouts.public')

@section('title', $facility->name.' — '.$facility->brand_tagline)

@section('content')
<section class="hero-plane min-h-[92vh] flex items-end md:items-center pb-16 pt-28">
    <div class="mx-auto max-w-6xl px-4 w-full">
        <div class="max-w-2xl reveal">
            <p class="font-display text-brand-2 text-lg md:text-xl font-bold tracking-wide mb-3" style="color: var(--brand-2)">{{ $facility->brand_tagline }}</p>
            <h1 class="font-display text-5xl md:text-7xl font-extrabold text-white leading-[0.95] tracking-tight">
                {{ $facility->name }}
            </h1>
            <p class="mt-5 text-lg md:text-xl text-white/90 max-w-xl reveal reveal-delay">
                Climate control with temperature <em>and</em> humidity in Ellijay — paved, secure, and owner-operated.
            </p>
            <div class="mt-8 flex flex-wrap gap-3 reveal reveal-delay">
                <a href="#units" class="btn-primary">Rent a Unit</a>
                <a href="{{ route('contact') }}" class="btn-ghost">Talk to Mike</a>
            </div>
        </div>
    </div>
</section>

<section id="units" class="mx-auto max-w-6xl px-4 py-16 md:py-20">
    <div class="max-w-2xl mb-10">
        <h2 class="font-display text-3xl md:text-4xl font-bold text-brand">Find your size</h2>
        <p class="mt-3 text-slate-600">Live availability from the yard. Rent now when open — or join the waitlist.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        @foreach($unitTypes as $type)
            <article class="border border-slate-200 bg-white p-6 md:p-7 hover:-translate-y-1 transition duration-300" style="box-shadow: 0 1px 0 rgba(16,35,63,.04)">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="font-display text-2xl font-bold">{{ $type->name }}</h3>
                        <p class="text-slate-500 mt-1">{{ $type->dimensionsLabel() }} @if($type->climate_controlled)· Climate @endif</p>
                    </div>
                    <div class="text-right">
                        <div class="font-display text-2xl font-bold text-brand">${{ number_format($type->monthly_rate, 0) }}</div>
                        <div class="text-xs uppercase tracking-wide text-slate-500">/ month</div>
                    </div>
                </div>
                <p class="mt-4 text-slate-600 text-sm">{{ $type->description }}</p>
                <div class="mt-6 flex items-center justify-between gap-3">
                    <span class="text-sm font-semibold {{ $type->available_count > 0 ? 'text-emerald-700' : 'text-amber-700' }}">
                        {{ $type->available_count > 0 ? $type->available_count.' available' : 'Currently full' }}
                    </span>
                    @if($type->available_count > 0)
                        <a href="{{ route('rent.show', $type) }}" class="btn-primary !py-2 !px-4 text-sm">Rent Now</a>
                    @else
                        <a href="{{ route('waitlist.show', $type) }}" class="bg-brand text-white font-bold px-4 py-2 text-sm">Waiting List</a>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-slate-100 via-white to-emerald-50"></div>
    <div class="relative mx-auto max-w-6xl px-4 py-16 grid md:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="font-display text-3xl md:text-4xl font-bold text-brand">Built for Ellijay weather</h2>
            <p class="mt-4 text-slate-600 leading-relaxed">{{ $facility->about }}</p>
        </div>
        <ul class="space-y-3">
            @foreach(($facility->amenities ?? []) as $amenity)
                <li class="flex gap-3 items-start">
                    <span class="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-brand-2"></span>
                    <span class="text-slate-700">{{ $amenity }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endsection
