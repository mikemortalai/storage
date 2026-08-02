@extends('layouts.public')

@section('title', ($page->meta_title ?: $page->title).' | '.$facility->name)

@section('content')
<section class="hero-plane pt-28 pb-16">
    <div class="mx-auto max-w-6xl px-4">
        <h1 class="font-display text-4xl md:text-5xl font-extrabold text-white">{{ $page->title }}</h1>
    </div>
</section>
<section class="mx-auto max-w-3xl px-4 py-12 prose prose-slate">
    {!! $page->body !!}
</section>
@endsection
