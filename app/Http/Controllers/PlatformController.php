<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilitySetting;
use App\Models\Organization;
use App\Models\SiteTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlatformController extends Controller
{
    public function marketing()
    {
        $templates = SiteTemplate::where('is_active', true)->get();

        return view('platform.marketing', compact('templates'));
    }

    public function pricing()
    {
        return view('platform.pricing');
    }

    public function templates()
    {
        $templates = SiteTemplate::where('is_active', true)->get();

        return view('platform.templates', compact('templates'));
    }

    public function signupForm()
    {
        $templates = SiteTemplate::where('is_active', true)->get();

        return view('platform.signup', compact('templates'));
    }

    public function signup(Request $request)
    {
        $data = $request->validate([
            'organization_name' => 'required|string|max:160',
            'facility_name' => 'required|string|max:160',
            'owner_name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:40',
            'plan' => 'required|in:starter,growth,pro_ai',
            'site_template_id' => 'nullable|exists:site_templates,id',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:32',
        ]);

        $org = Organization::create([
            'name' => $data['organization_name'],
            'slug' => Str::slug($data['organization_name']).'-'.Str::lower(Str::random(4)),
            'plan' => $data['plan'],
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $facility = Facility::create([
            'organization_id' => $org->id,
            'site_template_id' => $data['site_template_id'] ?? null,
            'name' => $data['facility_name'],
            'slug' => Str::slug($data['facility_name']).'-'.Str::lower(Str::random(3)),
            'subdomain' => Str::slug($data['facility_name']).'.storagesoftai.test',
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'],
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'primary_color' => '#114393',
            'secondary_color' => '#0edb47',
            'brand_tagline' => 'Self storage made simple',
            'is_active' => true,
        ]);

        FacilitySetting::create(['facility_id' => $facility->id]);

        $user = User::create([
            'name' => $data['owner_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'owner',
            'organization_id' => $org->id,
            'facility_id' => $facility->id,
            'phone' => $data['phone'] ?? null,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Welcome to StorageSoftAI! Your 14-day trial is active.');
    }
}
