@extends('layouts.platform')
@section('title', 'Start trial · StorageSoftAI')
@section('content')
<section class="mx-auto max-w-xl px-4 py-16">
    <h1 class="font-display text-4xl font-extrabold">Start your trial</h1>
    <form method="POST" action="{{ route('platform.signup.store') }}" class="glow-panel p-6 mt-8 space-y-4">
        @csrf
        <input name="organization_name" placeholder="Company / organization" class="w-full bg-black/20 border border-white/20 px-3 py-2" required>
        <input name="facility_name" placeholder="Facility name" class="w-full bg-black/20 border border-white/20 px-3 py-2" required>
        <input name="owner_name" placeholder="Your name" class="w-full bg-black/20 border border-white/20 px-3 py-2" required>
        <input type="email" name="email" placeholder="Email" class="w-full bg-black/20 border border-white/20 px-3 py-2" required>
        <input name="phone" placeholder="Phone" class="w-full bg-black/20 border border-white/20 px-3 py-2">
        <div class="grid grid-cols-2 gap-3">
            <input name="city" placeholder="City" class="w-full bg-black/20 border border-white/20 px-3 py-2">
            <input name="state" placeholder="State" class="w-full bg-black/20 border border-white/20 px-3 py-2">
        </div>
        <select name="plan" class="w-full bg-black/20 border border-white/20 px-3 py-2">
            <option value="starter">Starter</option>
            <option value="growth">Growth</option>
            <option value="pro_ai" selected>Pro / AI</option>
        </select>
        <select name="site_template_id" class="w-full bg-black/20 border border-white/20 px-3 py-2">
            <option value="">Default template</option>
            @foreach($templates as $template)
                <option value="{{ $template->id }}">{{ $template->name }}</option>
            @endforeach
        </select>
        <input type="password" name="password" placeholder="Password" class="w-full bg-black/20 border border-white/20 px-3 py-2" required>
        <input type="password" name="password_confirmation" placeholder="Confirm password" class="w-full bg-black/20 border border-white/20 px-3 py-2" required>
        @if($errors->any())<div class="text-red-300 text-sm">{{ $errors->first() }}</div>@endif
        <button class="w-full bg-[#0edb47] text-[#07301a] font-bold py-3">Create facility</button>
    </form>
</section>
@endsection
