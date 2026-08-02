@extends('layouts.platform')
@section('title', 'StorageSoftAI — Storage Management Software')

@section('content')
<section class="mx-auto max-w-6xl px-4 pt-16 pb-12">
    <p class="text-[#0edb47] font-bold tracking-wide">StorageSoftAI</p>
    <h1 class="font-display text-5xl md:text-7xl font-extrabold leading-[0.95] mt-3 max-w-4xl">
        Storage management software that rents units while you sleep.
    </h1>
    <p class="mt-6 text-lg text-white/75 max-w-2xl">Multi-tenant PMS + branded website templates + AI copilot. Dogfooded on 282 Storage.</p>
    <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('platform.signup') }}" class="bg-[#0edb47] text-[#07301a] font-bold px-5 py-3">Start 14-day trial</a>
        <a href="{{ route('home') }}" class="border border-white/30 px-5 py-3 font-semibold">See 282 Storage demo site</a>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 grid md:grid-cols-3 gap-4 pb-16">
    @foreach([
        ['Online rent & waitlist', 'Public widgets, e-lease, tenant portal payments'],
        ['Ops that match Easy', 'Units, customers, delinquency ladder, reports, CMS'],
        ['AI that asks permission', 'Briefings, pricing assist, collections drafts — confirm before write'],
    ] as [$t,$d])
    <div class="glow-panel p-6">
        <h2 class="font-display text-xl font-bold">{{ $t }}</h2>
        <p class="mt-3 text-white/70 text-sm leading-relaxed">{{ $d }}</p>
    </div>
    @endforeach
</section>

<section class="mx-auto max-w-6xl px-4 pb-20">
    <h2 class="font-display text-3xl font-bold mb-6">Site templates</h2>
    <div class="grid md:grid-cols-3 gap-4">
        @foreach($templates as $template)
            <div class="glow-panel p-5">
                <div class="font-display text-lg font-bold">{{ $template->name }}</div>
                <p class="text-sm text-white/70 mt-2">{{ $template->tagline }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
