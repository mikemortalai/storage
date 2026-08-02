@extends('layouts.admin')
@section('title', 'Waiting List')
@section('heading', 'Waiting List')

@section('content')
<div class="bg-white border overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-left"><tr>
            <th class="px-3 py-2">Added</th><th class="px-3 py-2">Name</th><th class="px-3 py-2">Contact</th>
            <th class="px-3 py-2">Unit type</th><th class="px-3 py-2">Move-in</th><th class="px-3 py-2">Notes</th><th class="px-3 py-2"></th>
        </tr></thead>
        <tbody>
        @foreach($entries as $entry)
            <tr class="border-t">
                <td class="px-3 py-2">{{ $entry->created_at->format('m/d/Y') }}</td>
                <td class="px-3 py-2 font-semibold">{{ $entry->name }}</td>
                <td class="px-3 py-2">{{ $entry->email }}<br>{{ $entry->phone }}</td>
                <td class="px-3 py-2">{{ $entry->unitType->name }}</td>
                <td class="px-3 py-2">{{ optional($entry->desired_move_in)->format('m/d/Y') ?: '—' }}</td>
                <td class="px-3 py-2">{{ $entry->notes }}</td>
                <td class="px-3 py-2">
                    <form method="POST" action="{{ route('admin.waitlist.update', $entry) }}">
                        @csrf @method('PATCH')
                        <select name="status" class="border text-xs" onchange="this.form.submit()">
                            @foreach(['waiting','contacted','rented','removed'] as $status)
                                <option value="{{ $status }}" @selected($entry->status===$status)>{{ $status }}</option>
                            @endforeach
                        </select>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $entries->links() }}</div>
@endsection
