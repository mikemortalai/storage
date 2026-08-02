<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaitingListEntry;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function index(Request $request)
    {
        $facilityId = $request->user()->facility_id;
        $entries = WaitingListEntry::with('unitType')
            ->where('facility_id', $facilityId)
            ->where('status', 'waiting')
            ->latest()
            ->paginate(40);

        return view('admin.waitlist.index', compact('entries'));
    }

    public function update(Request $request, WaitingListEntry $entry)
    {
        abort_unless($entry->facility_id === $request->user()->facility_id, 403);
        $data = $request->validate(['status' => 'required|in:waiting,contacted,rented,removed']);
        $entry->update($data);

        return back()->with('success', 'Waitlist entry updated.');
    }
}
