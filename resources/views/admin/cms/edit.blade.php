@extends('layouts.admin')
@section('title', 'Edit '.$page->title)
@section('heading', 'Edit page')
@section('content')
<form method="POST" action="{{ route('admin.cms.update', $page) }}" class="bg-white border p-6 max-w-3xl space-y-4">
@csrf @method('PATCH')
<div><label class="text-sm font-semibold">Title</label><input name="title" value="{{ old('title', $page->title) }}" class="w-full border px-3 py-2" required></div>
<div><label class="text-sm font-semibold">Meta title</label><input name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="w-full border px-3 py-2"></div>
<div><label class="text-sm font-semibold">Meta description</label><textarea name="meta_description" class="w-full border px-3 py-2" rows="2">{{ old('meta_description', $page->meta_description) }}</textarea></div>
<div><label class="text-sm font-semibold">Body (HTML)</label><textarea name="body" class="w-full border px-3 py-2 font-mono text-sm" rows="12">{{ old('body', $page->body) }}</textarea></div>
<label class="flex gap-2 text-sm items-center"><input type="checkbox" name="is_published" value="1" @checked($page->is_published)> Published</label>
<label class="flex gap-2 text-sm items-center"><input type="checkbox" name="show_in_nav" value="1" @checked($page->show_in_nav)> Show in nav</label>
<button class="bg-[#114393] text-white px-4 py-2 font-semibold">Save page</button>
</form>
@endsection
