@extends('layouts.admin')
@section('title', 'Customers')
@section('heading', 'Customers')

@section('content')
<form class="flex flex-wrap gap-2 mb-4">
    <input name="q" value="{{ $q }}" placeholder="Search name, email, phone, unit" class="border px-3 py-2 text-sm min-w-[240px]">
    <select name="status" class="border px-3 py-2 text-sm">
        <option value="">All statuses</option>
        @foreach(['active','late','lockout','archived','lead','waitlist'] as $status)
            <option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>
        @endforeach
    </select>
    <button class="bg-[#114393] text-white px-4 py-2 text-sm font-semibold">Search</button>
    <a href="{{ route('admin.reports.customers-csv') }}" class="border px-4 py-2 text-sm bg-white">Export CSV</a>
</form>

<form method="POST" action="{{ route('admin.customers.store') }}" class="bg-white border p-4 mb-6 grid md:grid-cols-5 gap-3 items-end">
    @csrf
    <input name="first_name" placeholder="First" class="border px-2 py-2 text-sm" required>
    <input name="last_name" placeholder="Last" class="border px-2 py-2 text-sm" required>
    <input name="email" placeholder="Email" class="border px-2 py-2 text-sm">
    <input name="phone" placeholder="Phone" class="border px-2 py-2 text-sm">
    <button class="bg-slate-900 text-white px-3 py-2 text-sm font-semibold">New customer</button>
</form>

<div class="bg-white border overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left"><tr>
            <th class="px-3 py-2">Name</th><th class="px-3 py-2">Contact</th><th class="px-3 py-2">Units</th><th class="px-3 py-2">Status</th><th class="px-3 py-2">Balance</th>
        </tr></thead>
        <tbody>
        @foreach($customers as $customer)
            <tr class="border-t">
                <td class="px-3 py-2"><a class="font-semibold text-[#114393]" href="{{ route('admin.customers.show', $customer) }}">{{ $customer->fullName() }}</a></td>
                <td class="px-3 py-2">{{ $customer->email }}<br><span class="text-slate-500">{{ $customer->phone }}</span></td>
                <td class="px-3 py-2">{{ $customer->rentals->whereNull('moved_out_at')->pluck('unit.unit_number')->filter()->implode(', ') ?: '—' }}</td>
                <td class="px-3 py-2">{{ $customer->status }}</td>
                <td class="px-3 py-2 font-semibold">${{ number_format($customer->balance, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $customers->links() }}</div>
@endsection
