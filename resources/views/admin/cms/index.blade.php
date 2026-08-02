@extends('layouts.admin')
@section('title', 'Website CMS')
@section('heading', 'Website CMS')
@section('content')
<div class="bg-white border overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="bg-slate-50 text-left"><tr>
<th class="px-3 py-2">Title</th><th class="px-3 py-2">Slug</th><th class="px-3 py-2">Type</th><th class="px-3 py-2">Published</th><th class="px-3 py-2"></th>
</tr></thead>
<tbody>
@foreach($pages as $page)
<tr class="border-t">
<td class="px-3 py-2 font-semibold">{{ $page->title }}</td>
<td class="px-3 py-2">{{ $page->slug }}</td>
<td class="px-3 py-2">{{ $page->page_type }}</td>
<td class="px-3 py-2">{{ $page->is_published ? 'Yes' : 'No' }}</td>
<td class="px-3 py-2"><a href="{{ route('admin.cms.edit', $page) }}" class="text-[#114393] font-semibold">Edit</a></td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endsection
