<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') · StorageSoftAI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: Figtree, sans-serif; }
        .font-display { font-family: Syne, sans-serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
<div class="min-h-screen md:grid md:grid-cols-[240px_1fr]">
    <aside class="bg-[#0b2f6b] text-white p-5">
        <div class="font-display text-xl font-extrabold">StorageSoftAI</div>
        <div class="text-xs text-white/70 mt-1">{{ auth()->user()->facility?->name }}</div>
        <nav class="mt-8 space-y-1 text-sm font-semibold">
            <a class="block px-3 py-2 hover:bg-white/10" href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a class="block px-3 py-2 hover:bg-white/10" href="{{ route('admin.units.index') }}">Units</a>
            <a class="block px-3 py-2 hover:bg-white/10" href="{{ route('admin.units.grid') }}">Unit Grid</a>
            <a class="block px-3 py-2 hover:bg-white/10" href="{{ route('admin.customers.index') }}">Customers</a>
            <a class="block px-3 py-2 hover:bg-white/10" href="{{ route('admin.waitlist.index') }}">Waiting List</a>
            <a class="block px-3 py-2 hover:bg-white/10" href="{{ route('admin.reports.index') }}">Reports</a>
            <a class="block px-3 py-2 hover:bg-white/10" href="{{ route('admin.cms.index') }}">Website CMS</a>
            <a class="block px-3 py-2 hover:bg-white/10" href="{{ route('admin.ai.index') }}">AI Copilot</a>
            <a class="block px-3 py-2 hover:bg-white/10" href="{{ route('home') }}" target="_blank">View Site</a>
            <form method="POST" action="{{ route('logout') }}" class="pt-4">@csrf<button class="px-3 py-2 text-left w-full hover:bg-white/10">Log out</button></form>
        </nav>
    </aside>
    <div>
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
            <h1 class="font-display text-xl font-bold text-slate-900">@yield('heading', 'Admin')</h1>
            <div class="text-sm text-slate-600">{{ auth()->user()->name }} · {{ auth()->user()->role }}</div>
        </header>
        @if(session('success') || session('error'))
            <div class="mx-6 mt-4 px-4 py-3 text-sm font-semibold {{ session('success') ? 'bg-emerald-100 text-emerald-900' : 'bg-red-100 text-red-800' }}">
                {{ session('success') ?? session('error') }}
            </div>
        @endif
        <main class="p-6">@yield('content')</main>
    </div>
</div>
</body>
</html>
