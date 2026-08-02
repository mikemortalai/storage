@extends('layouts.public')

@section('title', 'Rent '.$unitType->name.' | '.$facility->name)

@section('content')
<section class="hero-plane pt-28 pb-16">
    <div class="mx-auto max-w-6xl px-4">
        <h1 class="font-display text-4xl md:text-5xl font-extrabold text-white">Rent {{ $unitType->name }}</h1>
        <p class="mt-3 text-white/85">${{ number_format($unitType->monthly_rate, 0) }}/month · {{ $units->count() }} available</p>
    </div>
</section>

<section class="mx-auto max-w-2xl px-4 py-12">
    <form method="POST" action="{{ route('rent.start', $unitType) }}" class="bg-white border border-slate-200 p-6 md:p-8 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold mb-1">Full name</label>
            <input name="name" value="{{ old('name') }}" required class="w-full border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Phone</label>
            <input name="phone" value="{{ old('phone') }}" class="w-full border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Unit</label>
            <select name="unit_id" class="w-full border border-slate-300 px-3 py-2">
                <option value="">Automatically select</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}">Unit {{ $unit->unit_number }} — ${{ number_format($unit->effectiveRate(), 0) }}/mo</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Desired move-in</label>
            <input type="date" name="desired_move_in" value="{{ old('desired_move_in', now()->toDateString()) }}" class="w-full border border-slate-300 px-3 py-2">
        </div>
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Portal password</label>
                <input type="password" name="password" required class="w-full border border-slate-300 px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Confirm password</label>
                <input type="password" name="password_confirmation" required class="w-full border border-slate-300 px-3 py-2">
            </div>
        </div>
        <p class="text-xs text-slate-500">Demo payments use a simulated processor. E-lease is marked signed on completion.</p>
        @if($errors->any())
            <div class="text-red-600 text-sm">{{ $errors->first() }}</div>
        @endif
        <button class="btn-primary w-full text-center">Complete rental & pay first month</button>
    </form>
</section>
@endsection
