<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Task;
use App\Models\Unit;
use App\Models\WaitingListEntry;
use App\Services\FacilityContext;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, FacilityContext $facilities)
    {
        $user = $request->user();
        $facility = $user->facility ?? $facilities->require();

        $totalUnits = Unit::where('facility_id', $facility->id)->where('status', '!=', 'unavailable')->count();
        $occupied = Unit::where('facility_id', $facility->id)->whereIn('status', ['rented', 'late', 'locked_out', 'lien', 'auction', 'moving_out', 'reserved'])->count();
        $available = Unit::where('facility_id', $facility->id)->where('status', 'available')->count();
        $delinquent = Rental::where('facility_id', $facility->id)->whereIn('status', ['late', 'locked_out', 'lien', 'auction'])->count();
        $balance = Customer::where('facility_id', $facility->id)->sum('balance');
        $waitlist = WaitingListEntry::where('facility_id', $facility->id)->where('status', 'waiting')->count();
        $leads = Lead::where('facility_id', $facility->id)->where('status', 'new')->count();
        $tasks = Task::where('facility_id', $facility->id)->where('status', 'open')->orderBy('due_date')->limit(8)->get();
        $recentPayments = Payment::with('customer')->where('facility_id', $facility->id)->latest('paid_at')->limit(8)->get();

        return view('admin.dashboard', compact(
            'facility', 'totalUnits', 'occupied', 'available', 'delinquent',
            'balance', 'waitlist', 'leads', 'tasks', 'recentPayments'
        ));
    }
}
