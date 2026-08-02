@extends('layouts.admin')
@section('title', 'Unit Status')
@section('heading', 'Unit Status')
@section('content')
<div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
@foreach($rows as $status => $count)
<div class="bg-white border p-5">
<div class="text-xs uppercase text-slate-500">{{ str_replace('_',' ', $status) }}</div>
<div class="font-display text-3xl font-bold mt-2">{{ $count }}</div>
</div>
@endforeach
</div>
@endsection
