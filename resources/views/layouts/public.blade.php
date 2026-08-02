<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', ($facility->name ?? 'Storage').' | '.($facility->brand_tagline ?? 'StorageSoftAI'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand: {{ $facility->primary_color ?? '#114393' }};
            --brand-2: {{ $facility->secondary_color ?? '#0edb47' }};
            --brand-deep: {{ $facility->accent_color ?? '#0b2f6b' }};
            --ink: #10233f;
            --sand: #eef3f8;
        }
        body { font-family: Figtree, sans-serif; color: var(--ink); background: #f7fafc; }
        .font-display { font-family: Syne, sans-serif; }
        .bg-brand { background: var(--brand); }
        .text-brand { color: var(--brand); }
        .bg-brand-2 { background: var(--brand-2); }
        .btn-primary {
            background: var(--brand-2); color: #07301a; font-weight: 700;
            padding: .85rem 1.35rem; display: inline-block; transition: transform .2s ease, box-shadow .2s ease;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(14,219,71,.28); }
        .btn-ghost {
            border: 2px solid rgba(255,255,255,.75); color: #fff; font-weight: 700;
            padding: .8rem 1.25rem; display: inline-block;
        }
        .site-nav a { transition: color .2s ease; }
        .site-nav a:hover { color: var(--brand-2); }
        .reveal { animation: rise .7s ease both; }
        .reveal-delay { animation-delay: .15s; }
        @keyframes rise { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
        .hero-plane {
            background:
                linear-gradient(115deg, rgba(11,47,107,.92) 0%, rgba(17,67,147,.78) 48%, rgba(17,67,147,.35) 100%),
                radial-gradient(circle at 80% 20%, rgba(14,219,71,.28), transparent 40%),
                url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 160 160"><g fill="none" stroke="%23ffffff" stroke-opacity=".08" stroke-width="1"><path d="M0 40h160M0 80h160M0 120h160M40 0v160M80 0v160M120 0v160"/></g></svg>'),
                linear-gradient(160deg, #0b2f6b, #114393 55%, #1a5bb8);
            background-size: cover, cover, 120px 120px, cover;
        }
        .flash { animation: flashIn .4s ease; }
        @keyframes flashIn { from { opacity: 0; transform: translateY(-8px);} to { opacity:1; transform:none; } }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <header class="absolute inset-x-0 top-0 z-20">
        <div class="mx-auto max-w-6xl px-4 py-5 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="font-display text-2xl md:text-3xl font-extrabold tracking-tight text-white drop-shadow">
                {{ $facility->name ?? 'StorageSoftAI' }}
            </a>
            <nav class="site-nav hidden md:flex items-center gap-6 text-sm font-semibold text-white/90">
                <a href="{{ route('pages.show', 'storage') }}">Rent Storage</a>
                <a href="{{ route('map') }}">Map</a>
                <a href="{{ route('pages.show', 'Mike-the-Storage-Guy') }}">{{ $facility->brand_tagline ?? 'Owner' }}</a>
                <a href="{{ route('blog') }}">Blog</a>
                <a href="{{ route('contact') }}">Contact</a>
                <a href="{{ route('login') }}" class="btn-primary text-sm !py-2 !px-3">{{ $facility->login_cta_text ?? 'Login' }}</a>
            </nav>
            <a href="{{ route('login') }}" class="md:hidden btn-primary text-sm !py-2 !px-3">Login</a>
        </div>
    </header>

    @if(session('success') || session('error'))
        <div class="fixed top-4 right-4 z-50 flash max-w-sm rounded-none px-4 py-3 text-sm font-semibold shadow-lg {{ session('success') ? 'bg-brand-2 text-ink' : 'bg-red-600 text-white' }}">
            {{ session('success') ?? session('error') }}
        </div>
    @endif

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-[var(--brand-deep)] text-white mt-auto">
        <div class="mx-auto max-w-6xl px-4 py-12 grid md:grid-cols-3 gap-8">
            <div>
                <div class="font-display text-2xl font-bold">{{ $facility->name }}</div>
                <p class="mt-3 text-white/80 text-sm leading-relaxed">{{ $facility->fullAddress() }}</p>
                <p class="mt-2 text-white/90 font-semibold">{{ $facility->phone }}</p>
            </div>
            <div>
                <div class="font-display font-bold mb-3">Hours</div>
                <ul class="text-sm text-white/80 space-y-1">
                    @foreach(($facility->office_hours ?? []) as $day => $hours)
                        <li><span class="inline-block w-10">{{ $day }}</span> {{ $hours }}</li>
                    @endforeach
                </ul>
            </div>
            <div>
                <div class="font-display font-bold mb-3">Explore</div>
                <div class="flex flex-col gap-2 text-sm text-white/85">
                    <a href="{{ route('blog') }}">Blog</a>
                    <a href="{{ route('pages.show', 'Mike-the-Storage-Guy') }}">{{ $facility->brand_tagline }}</a>
                    <a href="{{ route('contact') }}">Contact</a>
                    <a href="{{ route('platform.home') }}">Powered by StorageSoftAI</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
