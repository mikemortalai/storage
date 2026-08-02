<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    protected function facilityId(Request $request): int
    {
        return (int) ($request->user()->facility_id ?: abort(403));
    }

    public function index()
    {
        return view('admin.reports.index');
    }

    public function occupancy(Request $request)
    {
        $facilityId = $this->facilityId($request);
        $rows = UnitType::where('facility_id', $facilityId)->orderBy('name')->get()->map(function (UnitType $type) {
            $total = $type->units()->count();
            $occupied = $type->units()->whereIn('status', ['rented', 'late', 'locked_out', 'lien', 'auction', 'moving_out', 'reserved'])->count();
            $sqft = ((float) $type->length_ft * (float) $type->width_ft) * $total;
            $occSqft = ((float) $type->length_ft * (float) $type->width_ft) * $occupied;

            return [
                'name' => $type->name,
                'units' => $total,
                'occupied' => $occupied,
                'occ_pct' => $total ? round(($occupied / $total) * 100, 1) : 0,
                'monthly_rate' => $type->monthly_rate,
                'sqft' => $sqft,
                'economic_occ' => $sqft ? round(($occSqft / $sqft) * 100, 1) : 0,
            ];
        });

        return view('admin.reports.occupancy', compact('rows'));
    }

    public function collections(Request $request)
    {
        $facilityId = $this->facilityId($request);
        $customers = Customer::with(['rentals.unit'])
            ->where('facility_id', $facilityId)
            ->where('balance', '>', 0)
            ->orderByDesc('balance')
            ->get();

        return view('admin.reports.collections', compact('customers'));
    }

    public function rentRoll(Request $request)
    {
        $facilityId = $this->facilityId($request);
        $rentals = Rental::with(['customer', 'unit.unitType'])
            ->where('facility_id', $facilityId)
            ->whereNull('moved_out_at')
            ->orderBy('id')
            ->get();

        return view('admin.reports.rent-roll', compact('rentals'));
    }

    public function unitStatus(Request $request)
    {
        $facilityId = $this->facilityId($request);
        $rows = Unit::where('facility_id', $facilityId)
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('admin.reports.unit-status', compact('rows'));
    }

    public function deposits(Request $request)
    {
        $facilityId = $this->facilityId($request);
        $payments = Payment::with('customer')
            ->where('facility_id', $facilityId)
            ->where('status', 'completed')
            ->when($request->month, function ($q) use ($request) {
                $q->whereMonth('paid_at', (int) substr($request->month, 5, 2))
                    ->whereYear('paid_at', (int) substr($request->month, 0, 4));
            }, function ($q) {
                $q->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year);
            })
            ->orderByDesc('paid_at')
            ->get();

        return view('admin.reports.deposits', compact('payments'));
    }

    public function exportCustomers(Request $request): StreamedResponse
    {
        $facilityId = $this->facilityId($request);
        $filename = 'customers-'.now()->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($facilityId) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['First Name', 'Last Name', 'Email', 'Phone', 'Status', 'Balance', 'Units']);
            Customer::with('rentals.unit')->where('facility_id', $facilityId)->orderBy('last_name')->chunk(100, function ($chunk) use ($out) {
                foreach ($chunk as $c) {
                    fputcsv($out, [
                        $c->first_name,
                        $c->last_name,
                        $c->email,
                        $c->phone,
                        $c->status,
                        $c->balance,
                        $c->rentals->whereNull('moved_out_at')->pluck('unit.unit_number')->filter()->implode(', '),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
