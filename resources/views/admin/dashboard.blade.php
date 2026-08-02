@extends('layouts.admin')
@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
    @foreach([
        ['Occupancy', $occupied.'/'.$totalUnits, round($totalUnits ? ($occupied/$totalUnits)*100 : 0, 1).'%'],
        ['Available', $available, 'units'],
        ['Delinquent', $delinquent, 'rentals'],
        ['AR Balance', '$'.number_format($balance, 2), $waitlist.' waitlist · '.$leads.' leads'],
    ] as [$label, $value, $sub])
        <div class="bg-white border border-slate-200 p-5">
            <div class="text-xs uppercase tracking-wide text-slate-500">{{ $label }}</div>
            <div class="font-display text-3xl font-bold mt-2">{{ $value }}</div>
            <div class="text-sm text-slate-500 mt-1">{{ $sub }}</div>
        </div>
    @endforeach
</div>

<div class="grid lg:grid-cols-2 gap-6 mt-6">
    <section class="bg-white border border-slate-200 p-5">
        <h2 class="font-display font-bold text-lg">Open tasks</h2>
        <ul class="mt-4 space-y-3 text-sm">
            @forelse($tasks as $task)
                <li class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                    <span>{{ $task->title }}</span>
                    <span class="text-slate-500">{{ optional($task->due_date)->format('m/d') }}</span>
                </li>
            @empty
                <li class="text-slate-500">No open tasks.</li>
            @endforelse
        </ul>
    </section>
    <section class="bg-white border border-slate-200 p-5">
        <h2 class="font-display font-bold text-lg">Recent payments</h2>
        <ul class="mt-4 space-y-3 text-sm">
            @forelse($recentPayments as $payment)
                <li class="flex justify-between gap-3 border-b border-slate-100 pb-2">
                    <span>{{ $payment->customer?->fullName() }} · {{ strtoupper($payment->method) }}</span>
                    <span class="font-semibold">${{ number_format($payment->amount, 2) }}</span>
                </li>
            @empty
                <li class="text-slate-500">No payments yet.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
