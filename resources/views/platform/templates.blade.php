@extends('layouts.platform')
@section('title', 'Templates · StorageSoftAI')
@section('content')
<section class="mx-auto max-w-6xl px-4 py-16">
    <h1 class="font-display text-4xl font-extrabold">Website template gallery</h1>
    <p class="mt-3 text-white/70 max-w-2xl">Launch a branded marketing site in minutes. 282 Storage is the owner-operated climate reference.</p>
    <div class="grid md:grid-cols-3 gap-4 mt-10">
        @foreach($templates as $template)
            <div class="glow-panel p-6">
                <div class="h-28 mb-4" style="background: linear-gradient(135deg, {{ $template->theme_tokens['primary'] ?? '#114393' }}, {{ $template->theme_tokens['secondary'] ?? '#0edb47' }})"></div>
                <div class="font-display text-xl font-bold">{{ $template->name }}</div>
                <p class="text-sm text-white/70 mt-2">{{ $template->tagline }}</p>
            </div>
        @endforeach
    </div>
</section>
@endsection
