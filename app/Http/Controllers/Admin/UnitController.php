<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    protected function facilityId(Request $request): int
    {
        return (int) ($request->user()->facility_id ?: abort(403));
    }

    public function index(Request $request)
    {
        $facilityId = $this->facilityId($request);
        $units = Unit::with(['unitType', 'activeRental.customer'])
            ->where('facility_id', $facilityId)
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->unit_type_id, fn ($q) => $q->where('unit_type_id', $request->unit_type_id))
            ->orderBy('unit_number')
            ->paginate(50);

        $unitTypes = UnitType::where('facility_id', $facilityId)->orderBy('name')->get();
        $statusCounts = Unit::where('facility_id', $facilityId)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('admin.units.index', compact('units', 'unitTypes', 'statusCounts'));
    }

    public function grid(Request $request)
    {
        $facilityId = $this->facilityId($request);
        $units = Unit::with('unitType')->where('facility_id', $facilityId)->orderBy('unit_number')->get();

        return view('admin.units.grid', compact('units'));
    }

    public function store(Request $request)
    {
        $facilityId = $this->facilityId($request);
        $data = $request->validate([
            'unit_type_id' => 'required|exists:unit_types,id',
            'unit_number' => 'required|string|max:40',
            'monthly_rate' => 'nullable|numeric|min:0',
            'status' => 'required|string',
        ]);

        abort_unless(UnitType::where('id', $data['unit_type_id'])->where('facility_id', $facilityId)->exists(), 403);

        Unit::create([
            ...$data,
            'facility_id' => $facilityId,
        ]);

        return back()->with('success', 'Unit created.');
    }

    public function update(Request $request, Unit $unit)
    {
        abort_unless($unit->facility_id === $this->facilityId($request), 403);
        $data = $request->validate([
            'status' => 'required|string',
            'monthly_rate' => 'nullable|numeric|min:0',
            'gate_code' => 'nullable|string|max:40',
            'notes' => 'nullable|string|max:5000',
        ]);
        $unit->update($data);

        return back()->with('success', 'Unit updated.');
    }
}
