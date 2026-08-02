@extends('layouts.public')

@section('title', 'Waiting List | '.$facility->name)

@section('content')
<section class="hero-plane pt-28 pb-16">
    <div class="mx-auto max-w-6xl px-4">
        <h1 class="font-display text-4xl md:text-5xl font-extrabold text-white">Join the waiting list</h1>
        <p class="mt-3 text-white/85">{{ $unitType->name }} is currently full.</p>
    </div>
</section>
<section class="mx-auto max-w-2xl px-4 py-12">
    <form method="POST" action="{{ route('waitlist.submit', $unitType) }}" class="bg-white border border-slate-200 p-6 md:p-8 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-semibold mb-1">Name</label>
            <input name="name" required class="w-full border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Email</label>
            <input type="email" name="email" class="w-full border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Cell phone</label>
            <input name="phone" class="w-full border border-slate-300 px-3 py-2">
        </div>
        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="texting_consent" value="1"> I consent to texting
        </label>
        <div>
            <label class="block text-sm font-semibold mb-1">Desired move-in</label>
            <input type="date" name="desired_move_in" class="w-full border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Notes</label>
            <textarea name="notes" rows="3" class="w-full border border-slate-300 px-3 py-2"></textarea>
        </div>
        <button class="btn-primary w-full">Join waiting list</button>
    </form>
</section>
@endsection
