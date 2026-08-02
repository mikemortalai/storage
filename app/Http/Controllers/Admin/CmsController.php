<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function index(Request $request)
    {
        $pages = CmsPage::where('facility_id', $request->user()->facility_id)
            ->orderBy('page_type')
            ->orderBy('sort_order')
            ->get();

        return view('admin.cms.index', compact('pages'));
    }

    public function edit(Request $request, CmsPage $page)
    {
        abort_unless($page->facility_id === $request->user()->facility_id, 403);

        return view('admin.cms.edit', compact('page'));
    }

    public function update(Request $request, CmsPage $page)
    {
        abort_unless($page->facility_id === $request->user()->facility_id, 403);
        $data = $request->validate([
            'title' => 'required|string|max:160',
            'body' => 'nullable|string',
            'meta_title' => 'nullable|string|max:180',
            'meta_description' => 'nullable|string|max:300',
            'is_published' => 'nullable|boolean',
            'show_in_nav' => 'nullable|boolean',
        ]);

        $page->update([
            ...$data,
            'is_published' => (bool) ($data['is_published'] ?? false),
            'show_in_nav' => (bool) ($data['show_in_nav'] ?? false),
        ]);

        return redirect()->route('admin.cms.index')->with('success', 'Page saved.');
    }
}
