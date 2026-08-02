@extends('layouts.admin')
@section('title', 'AI Copilot')
@section('heading', 'AI Copilot')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 space-y-4">
        <form method="POST" action="{{ route('admin.ai.ask') }}" class="bg-white border p-5 space-y-3">
            @csrf
            <label class="font-display font-bold">Ask about your facility</label>
            <input name="question" placeholder="Who is 30+ days late? What’s my occupancy by unit type?" class="w-full border px-3 py-2" required>
            <p class="text-xs text-slate-500">Read-only by default. AI never mutates data without confirmation.</p>
            <button class="bg-[#114393] text-white px-4 py-2 text-sm font-semibold">Ask</button>
        </form>

        <div class="flex flex-wrap gap-2">
            <form method="POST" action="{{ route('admin.ai.briefing') }}">@csrf<button class="bg-slate-900 text-white px-3 py-2 text-sm">Generate daily briefing</button></form>
            <form method="POST" action="{{ route('admin.ai.pricing') }}">@csrf<button class="bg-slate-900 text-white px-3 py-2 text-sm">Pricing assist</button></form>
            <form method="POST" action="{{ route('admin.ai.delinquency') }}">@csrf<button class="bg-amber-700 text-white px-3 py-2 text-sm">Run delinquency ladder</button></form>
        </div>

        <div class="space-y-3">
            @foreach($suggestions as $suggestion)
                <article class="bg-white border p-4">
                    <div class="text-xs uppercase tracking-wide text-slate-500">{{ $suggestion->type }} · {{ $suggestion->status }} · {{ $suggestion->created_at->diffForHumans() }}</div>
                    @if($suggestion->prompt)
                        <div class="text-sm font-semibold mt-1">Q: {{ $suggestion->prompt }}</div>
                    @endif
                    <pre class="mt-2 text-sm whitespace-pre-wrap font-sans text-slate-700">{{ $suggestion->suggestion }}</pre>
                    @if($suggestion->status === 'pending')
                        <div class="mt-3 flex gap-2">
                            <form method="POST" action="{{ route('admin.ai.suggestion', $suggestion) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="accepted"><button class="text-xs bg-emerald-600 text-white px-2 py-1">Accept</button></form>
                            <form method="POST" action="{{ route('admin.ai.suggestion', $suggestion) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="dismissed"><button class="text-xs bg-slate-500 text-white px-2 py-1">Dismiss</button></form>
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    </section>
    <aside class="bg-[#0b2f6b] text-white p-5">
        <h2 class="font-display text-xl font-bold">Owner copilot</h2>
        <p class="text-sm text-white/80 mt-3 leading-relaxed">Natural-language questions over live occupancy, delinquencies, waitlist, and pricing. Suggestions require explicit approval before any write.</p>
        <ul class="mt-4 text-sm space-y-2 text-white/90">
            <li>• Daily briefing</li>
            <li>• Pricing recommendations</li>
            <li>• Delinquency ladder automation</li>
            <li>• Full audit trail of AI actions</li>
        </ul>
    </aside>
</div>
@endsection
