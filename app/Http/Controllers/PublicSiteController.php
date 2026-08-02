<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use App\Models\Lead;
use App\Models\RentalIntent;
use App\Models\Unit;
use App\Models\UnitType;
use App\Models\WaitingListEntry;
use App\Services\BillingService;
use App\Services\FacilityContext;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PublicSiteController extends Controller
{
    public function __construct(protected FacilityContext $facilities) {}

    public function home()
    {
        $facility = $this->facilities->require();
        $unitTypes = UnitType::withCount([
            'units as available_count' => fn ($q) => $q->where('status', 'available'),
        ])->where('facility_id', $facility->id)
            ->where('show_on_website', true)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('public.home', compact('facility', 'unitTypes'));
    }

    public function page(string $slug)
    {
        $facility = $this->facilities->require();
        $page = CmsPage::where('facility_id', $facility->id)
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('public.page', compact('facility', 'page'));
    }

    public function blog()
    {
        $facility = $this->facilities->require();
        $posts = CmsPage::where('facility_id', $facility->id)
            ->where('page_type', 'blog')
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->get();

        return view('public.blog', compact('facility', 'posts'));
    }

    public function contact()
    {
        $facility = $this->facilities->require();

        return view('public.contact', compact('facility'));
    }

    public function submitContact(Request $request)
    {
        $facility = $this->facilities->require();
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'nullable|email|max:160',
            'phone' => 'nullable|string|max:40',
            'message' => 'required|string|max:5000',
        ]);

        Lead::create([
            'facility_id' => $facility->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'message' => $data['message'],
            'source' => 'contact_form',
            'status' => 'new',
        ]);

        return back()->with('success', 'Thanks! We received your message and will get back to you soon.');
    }

    public function rentForm(UnitType $unitType)
    {
        $facility = $this->facilities->require();
        abort_unless($unitType->facility_id === $facility->id && $unitType->show_on_website, 404);

        $units = Unit::where('unit_type_id', $unitType->id)->where('status', 'available')->orderBy('unit_number')->get();

        return view('public.rent', compact('facility', 'unitType', 'units'));
    }

    public function startRent(Request $request, UnitType $unitType, BillingService $billing)
    {
        $facility = $this->facilities->require();
        abort_unless($unitType->facility_id === $facility->id, 404);

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'phone' => 'nullable|string|max:40',
            'unit_id' => 'nullable|exists:units,id',
            'desired_move_in' => 'nullable|date',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $unit = null;
        if (! empty($data['unit_id'])) {
            $unit = Unit::where('id', $data['unit_id'])->where('unit_type_id', $unitType->id)->where('status', 'available')->firstOrFail();
        } else {
            $unit = Unit::where('unit_type_id', $unitType->id)->where('status', 'available')->orderBy('unit_number')->first();
        }

        if (! $unit) {
            return redirect()->route('waitlist.show', $unitType)->with('error', 'That size is full. Join the waiting list.');
        }

        $intent = RentalIntent::create([
            'facility_id' => $facility->id,
            'unit_type_id' => $unitType->id,
            'unit_id' => $unit->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'desired_move_in' => $data['desired_move_in'] ?? now()->toDateString(),
            'status' => 'started',
        ]);

        [$first, $last] = array_pad(explode(' ', $data['name'], 2), 2, '');

        $customer = Customer::firstOrCreate(
            ['facility_id' => $facility->id, 'email' => $data['email']],
            [
                'first_name' => $first ?: $data['name'],
                'last_name' => $last ?: 'Customer',
                'phone' => $data['phone'] ?? null,
                'status' => 'active',
            ]
        );

        $user = User::firstOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'],
                'password' => Hash::make($data['password']),
                'role' => 'tenant',
                'organization_id' => $facility->organization_id,
                'facility_id' => $facility->id,
                'customer_id' => $customer->id,
                'phone' => $data['phone'] ?? null,
                'email_verified_at' => now(),
            ]
        );

        if (! $user->customer_id) {
            $user->update(['customer_id' => $customer->id, 'role' => 'tenant', 'facility_id' => $facility->id]);
        }

        $rental = $billing->rentUnit($unit, $customer, null, true);
        $intent->update(['status' => 'completed']);

        Auth::login($user);

        return redirect()->route('tenant.dashboard')->with('success', 'Welcome! Unit '.$rental->unit->unit_number.' is rented. Lease marked signed and first month paid (demo processor).');
    }

    public function waitlistForm(UnitType $unitType)
    {
        $facility = $this->facilities->require();
        abort_unless($unitType->facility_id === $facility->id, 404);

        return view('public.waitlist', compact('facility', 'unitType'));
    }

    public function submitWaitlist(Request $request, UnitType $unitType)
    {
        $facility = $this->facilities->require();
        abort_unless($unitType->facility_id === $facility->id, 404);

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'nullable|email|max:160',
            'phone' => 'nullable|string|max:40',
            'texting_consent' => 'nullable|boolean',
            'desired_move_in' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        WaitingListEntry::create([
            'facility_id' => $facility->id,
            'unit_type_id' => $unitType->id,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'texting_consent' => (bool) ($data['texting_consent'] ?? false),
            'desired_move_in' => $data['desired_move_in'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'waiting',
        ]);

        return redirect()->route('home')->with('success', 'You are on the waiting list. We will contact you when a unit opens.');
    }

    public function map()
    {
        $facility = $this->facilities->require();
        $units = Unit::with('unitType')->where('facility_id', $facility->id)->orderBy('unit_number')->get();

        return view('public.map', compact('facility', 'units'));
    }
}
