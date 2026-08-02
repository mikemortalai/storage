@extends('layouts.public')

@section('title', 'Contact | '.$facility->name)

@section('content')
<section class="hero-plane pt-28 pb-16">
    <div class="mx-auto max-w-6xl px-4">
        <h1 class="font-display text-4xl md:text-5xl font-extrabold text-white">Contact Us</h1>
        <p class="mt-3 text-white/85">A real person answers — call {{ $facility->phone }}.</p>
    </div>
</section>
<section class="mx-auto max-w-2xl px-4 py-12">
    <form method="POST" action="{{ route('contact.submit') }}" class="bg-white border border-slate-200 p-6 md:p-8 space-y-4">
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
            <label class="block text-sm font-semibold mb-1">Phone</label>
            <input name="phone" class="w-full border border-slate-300 px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Message</label>
            <textarea name="message" rows="5" required class="w-full border border-slate-300 px-3 py-2"></textarea>
        </div>
        <button class="btn-primary w-full">Send message</button>
    </form>
</section>
@endsection
