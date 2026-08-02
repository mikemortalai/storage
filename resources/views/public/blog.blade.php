@extends('layouts.public')

@section('title', 'Blog | '.$facility->name)

@section('content')
<section class="hero-plane pt-28 pb-16">
    <div class="mx-auto max-w-6xl px-4">
        <h1 class="font-display text-4xl md:text-5xl font-extrabold text-white">282 Storage Blog</h1>
        <p class="mt-3 text-white/85">Tips from Mike the Storage Guy.</p>
    </div>
</section>
<section class="mx-auto max-w-3xl px-4 py-12 space-y-6">
    @foreach($posts as $post)
        <a href="{{ route('pages.show', $post->slug) }}" class="block border border-slate-200 bg-white p-6 hover:-translate-y-0.5 transition">
            <h2 class="font-display text-2xl font-bold text-brand">{{ $post->title }}</h2>
            <p class="mt-2 text-slate-600 text-sm">{{ \Illuminate\Support\Str::limit(strip_tags($post->body), 140) }}</p>
        </a>
    @endforeach
</section>
@endsection
