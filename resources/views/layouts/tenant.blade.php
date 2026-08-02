<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Tenant Portal')</title>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body{font-family:Figtree,sans-serif}.font-display{font-family:Syne,sans-serif}</style>
</head>
<body class="bg-slate-50 min-h-screen">
<header class="bg-[#114393] text-white">
    <div class="mx-auto max-w-4xl px-4 py-4 flex justify-between items-center">
        <div>
            <div class="font-display text-xl font-extrabold">{{ auth()->user()->facility?->name ?? 'Tenant Portal' }}</div>
            <div class="text-xs text-white/70">Make a Payment / Account</div>
        </div>
        <nav class="flex gap-4 text-sm font-semibold">
            <a href="{{ route('tenant.dashboard') }}">Dashboard</a>
            <a href="{{ route('tenant.profile') }}">Profile</a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button>Log out</button></form>
        </nav>
    </div>
</header>
@if(session('success') || session('error'))
<div class="mx-auto max-w-4xl px-4 mt-4"><div class="px-4 py-3 text-sm font-semibold {{ session('success') ? 'bg-emerald-100 text-emerald-900' : 'bg-red-100 text-red-800' }}">{{ session('success') ?? session('error') }}</div></div>
@endif
<main class="mx-auto max-w-4xl px-4 py-8">@yield('content')</main>
</body>
</html>
