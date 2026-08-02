@extends('layouts.tenant')
@section('title', 'Profile')

@section('content')
<form method="POST" action="{{ route('tenant.profile.update') }}" class="bg-white border p-6 max-w-xl space-y-4">
@csrf @method('PATCH')
<h1 class="font-display text-2xl font-bold">Profile</h1>
<div><label class="text-sm font-semibold">Name</label><input value="{{ $customer->fullName() }}" class="w-full border px-3 py-2 bg-slate-50" disabled></div>
<div><label class="text-sm font-semibold">Email</label><input value="{{ $customer->email }}" class="w-full border px-3 py-2 bg-slate-50" disabled></div>
<div><label class="text-sm font-semibold">Phone</label><input name="phone" value="{{ $customer->phone }}" class="w-full border px-3 py-2"></div>
<div><label class="text-sm font-semibold">Address</label><input name="address_line1" value="{{ $customer->address_line1 }}" class="w-full border px-3 py-2"></div>
<div class="grid grid-cols-3 gap-3">
<input name="city" value="{{ $customer->city }}" placeholder="City" class="border px-3 py-2">
<input name="state" value="{{ $customer->state }}" placeholder="State" class="border px-3 py-2">
<input name="postal_code" value="{{ $customer->postal_code }}" placeholder="ZIP" class="border px-3 py-2">
</div>
<label class="flex gap-2 items-center text-sm"><input type="checkbox" name="autopay_enabled" value="1" @checked($customer->autopay_enabled)> Enable autopay</label>
<button class="bg-[#114393] text-white px-4 py-2 font-semibold">Save</button>
</form>
@endsection
