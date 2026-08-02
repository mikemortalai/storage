@extends('layouts.platform')
@section('title', 'Pricing · StorageSoftAI')
@section('content')
<section class="mx-auto max-w-6xl px-4 py-16">
    <h1 class="font-display text-4xl md:text-5xl font-extrabold">Simple plans for facility owners</h1>
    <div class="grid md:grid-cols-3 gap-4 mt-10">
        @foreach([
            ['Starter', '$79', ['1 facility', 'Templated site + domain', 'Online rent/waitlist', 'Tenant pay portal', 'Core reports']],
            ['Growth', '$149', ['Delinquency automations', 'SMS/email templates', 'Gate hooks', 'Retail/products', 'Advanced reports', 'Multi-user roles']],
            ['Pro / AI', '$249', ['Everything in Growth', 'AI copilot', 'Pricing assist', 'Delinquency prediction', 'Tenant chat', 'Content assist']],
        ] as [$name, $price, $features])
        <div class="glow-panel p-6">
            <div class="font-display text-2xl font-bold">{{ $name }}</div>
            <div class="text-3xl font-extrabold mt-3">{{ $price }}<span class="text-base font-medium text-white/60">/mo</span></div>
            <ul class="mt-5 space-y-2 text-sm text-white/75">
                @foreach($features as $f)<li>• {{ $f }}</li>@endforeach
            </ul>
            <a href="{{ route('platform.signup') }}" class="inline-block mt-6 bg-[#0edb47] text-[#07301a] font-bold px-4 py-2">Start trial</a>
        </div>
        @endforeach
    </div>
</section>
@endsection
