<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'StorageSoftAI')</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body{font-family:Figtree,sans-serif;background:#071828;color:#e8eef7}
        .font-display{font-family:Syne,sans-serif}
        .glow-panel{background:linear-gradient(160deg,#0d2a4a,#113d66 55%,#0a223a);border:1px solid rgba(255,255,255,.08)}
    </style>
</head>
<body>
<header class="border-b border-white/10">
    <div class="mx-auto max-w-6xl px-4 py-5 flex justify-between items-center">
        <a href="{{ route('platform.home') }}" class="font-display text-2xl font-extrabold">StorageSoftAI</a>
        <nav class="flex gap-5 text-sm font-semibold text-white/85">
            <a href="{{ route('platform.pricing') }}">Pricing</a>
            <a href="{{ route('platform.templates') }}">Templates</a>
            <a href="{{ route('platform.signup') }}" class="bg-[#0edb47] text-[#07301a] px-3 py-1.5 font-bold">Start free trial</a>
        </nav>
    </div>
</header>
<main>@yield('content')</main>
<footer class="border-t border-white/10 mt-16">
    <div class="mx-auto max-w-6xl px-4 py-8 text-sm text-white/60">Storage Management Software with AI — built for owner-operators. 282 Storage is our reference tenant.</div>
</footer>
</body>
</html>
