<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use App\Models\CommunicationTemplate;
use App\Models\Customer;
use App\Models\Facility;
use App\Models\FacilitySetting;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\RetailProduct;
use App\Models\SiteTemplate;
use App\Models\Task;
use App\Models\Unit;
use App\Models\UnitType;
use App\Models\User;
use App\Models\WaitingListEntry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $ownerTemplate = SiteTemplate::create([
            'name' => 'Owner-Operated Climate Control',
            'slug' => 'owner-climate',
            'tagline' => 'Personal brand + climate-controlled storage',
            'theme_tokens' => ['primary' => '#114393', 'secondary' => '#0edb47'],
            'starter_pages' => ['home', 'storage', 'map', 'contact', 'blog'],
        ]);

        SiteTemplate::create([
            'name' => 'Budget Drive-Up',
            'slug' => 'budget-drive-up',
            'tagline' => 'Simple drive-up storage',
            'theme_tokens' => ['primary' => '#1f3d2a', 'secondary' => '#f0b429'],
            'starter_pages' => ['home', 'rent', 'contact'],
        ]);

        SiteTemplate::create([
            'name' => 'Mixed Vehicle + Climate',
            'slug' => 'mixed-vehicle-climate',
            'tagline' => 'Units, parking, and garages',
            'theme_tokens' => ['primary' => '#243b53', 'secondary' => '#38bdf8'],
            'starter_pages' => ['home', 'map', 'contact'],
        ]);

        $org = Organization::create([
            'name' => '282 Storage',
            'slug' => '282-storage',
            'plan' => 'pro_ai',
            'subscription_status' => 'active',
            'remove_powered_by' => false,
        ]);

        $facility = Facility::create([
            'organization_id' => $org->id,
            'site_template_id' => $ownerTemplate->id,
            'name' => '282 Storage',
            'slug' => '282-storage',
            'custom_domain' => '282storage.com',
            'subdomain' => '282storage.storagesoftai.test',
            'address_line1' => '3875 Tails Creek Rd',
            'city' => 'Ellijay',
            'state' => 'GA',
            'postal_code' => '30540',
            'phone' => '(706) 972-2065',
            'owner_phone' => '(404) 550-4390',
            'email' => 'hello@282storage.com',
            'timezone' => 'America/New_York',
            'invoice_lead_days' => 7,
            'move_out_days_restriction' => 10,
            'future_reservation_window_days' => 1,
            'gate_provider' => 'QuikStor',
            'gate_keys_enabled' => true,
            'login_cta_text' => 'Make a Payment/Login',
            'primary_color' => '#114393',
            'secondary_color' => '#0edb47',
            'accent_color' => '#0b2f6b',
            'facebook_url' => 'https://facebook.com/282storage',
            'instagram_url' => 'https://instagram.com/282storage',
            'tiktok_url' => 'https://tiktok.com/@282storage',
            'youtube_url' => 'https://youtube.com/channel/UC0Crr7w7n4W6Y_jIat82tEw',
            'google_review_url' => 'https://g.page/r/282storage/review',
            'office_hours' => [
                'Sun' => '10:00 AM–6:00 PM',
                'Mon' => '10:00 AM–6:00 PM',
                'Tue' => '10:00 AM–6:00 PM',
                'Wed' => '10:00 AM–6:00 PM',
                'Thu' => '10:00 AM–9:00 PM',
                'Fri' => '10:00 AM–6:00 PM',
                'Sat' => '10:00 AM–6:00 PM',
            ],
            'amenities' => [
                'Complimentary carts',
                'Onsite security / digital video surveillance',
                '10 cameras live to owner phone',
                'Online bill pay',
                'Variety of unit sizes',
                'Drive-up access',
                'Climate control (temperature + humidity)',
                'Paved roads',
                'Owner-operated / human answers phone',
            ],
            'about' => 'Owner-operated storage in Ellijay, GA with true climate control — temperature and humidity — paved access, and a real person on the phone.',
            'brand_tagline' => 'Mike the Storage Guy',
            'is_active' => true,
        ]);

        FacilitySetting::create([
            'facility_id' => $facility->id,
            'late_days' => 5,
            'late_fee' => 15,
            'lockout_days' => 7,
            'lockout_fee' => 5,
            'lien_days' => 31,
            'lien_fee' => 100,
            'auction_days' => 45,
        ]);

        User::create([
            'name' => 'Mike Storage',
            'email' => 'owner@282storage.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
            'organization_id' => $org->id,
            'facility_id' => $facility->id,
            'phone' => '(404) 550-4390',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Platform Admin',
            'email' => 'admin@storagesoftai.com',
            'password' => Hash::make('password'),
            'role' => 'platform_admin',
            'email_verified_at' => now(),
        ]);

        $typeDefs = [
            ['name' => 'Sign Rental (8x4x4)', 'l' => 8, 'w' => 4, 'h' => 4, 'rate' => 75, 'climate' => false, 'web' => false, 'count' => 2, 'sort' => 90],
            ['name' => '8x20 Container', 'l' => 8, 'w' => 20, 'h' => null, 'rate' => 110, 'climate' => false, 'web' => true, 'count' => 13, 'sort' => 10],
            ['name' => 'Uncovered parking (40x12)', 'l' => 40, 'w' => 12, 'h' => null, 'rate' => 125, 'climate' => false, 'web' => true, 'count' => 1, 'sort' => 20],
            ['name' => '10x10x12 Climate Control', 'l' => 10, 'w' => 10, 'h' => 12, 'rate' => 120, 'climate' => true, 'web' => true, 'count' => 11, 'sort' => 30],
            ['name' => '10x12x12 Climate Control', 'l' => 10, 'w' => 12, 'h' => 12, 'rate' => 120, 'climate' => true, 'web' => false, 'count' => 2, 'sort' => 35],
            ['name' => '10x15x12 Climate Control', 'l' => 10, 'w' => 15, 'h' => 12, 'rate' => 150, 'climate' => true, 'web' => true, 'count' => 22, 'sort' => 40],
            ['name' => '10x20 Covered Garage', 'l' => 10, 'w' => 20, 'h' => null, 'rate' => 175, 'climate' => false, 'web' => false, 'count' => 1, 'sort' => 50],
            ['name' => '10x20x12 Climate Control', 'l' => 10, 'w' => 20, 'h' => 12, 'rate' => 190, 'climate' => true, 'web' => true, 'count' => 23, 'sort' => 15],
            ['name' => 'Carvers Creek Gallery (50x50)', 'l' => 50, 'w' => 50, 'h' => null, 'rate' => 1900, 'climate' => false, 'web' => false, 'count' => 1, 'sort' => 100],
            ['name' => '12x20 covered parking', 'l' => 12, 'w' => 20, 'h' => null, 'rate' => 175, 'climate' => false, 'web' => false, 'count' => 1, 'sort' => 55],
            ['name' => 'Carvers Creek Shop (40x100x10)', 'l' => 40, 'w' => 100, 'h' => 10, 'rate' => 1500, 'climate' => false, 'web' => false, 'count' => 1, 'sort' => 110],
        ];

        $unitTypes = [];
        $unitCounter = 100;
        foreach ($typeDefs as $def) {
            $type = UnitType::create([
                'facility_id' => $facility->id,
                'name' => $def['name'],
                'length_ft' => $def['l'],
                'width_ft' => $def['w'],
                'height_ft' => $def['h'],
                'monthly_rate' => $def['rate'],
                'description' => $def['climate']
                    ? 'Climate controlled with temperature and humidity management.'
                    : 'Drive-up access storage.',
                'climate_controlled' => $def['climate'],
                'show_on_website' => $def['web'],
                'is_active' => true,
                'sort_order' => $def['sort'],
            ]);
            $unitTypes[] = ['model' => $type, 'count' => $def['count']];

            for ($i = 1; $i <= $def['count']; $i++) {
                Unit::create([
                    'facility_id' => $facility->id,
                    'unit_type_id' => $type->id,
                    'unit_number' => (string) ($unitCounter++),
                    'status' => 'available',
                    'monthly_rate' => $def['rate'],
                    'map_x' => (($i * 37) % 90) + 5,
                    'map_y' => (($def['sort'] + $i * 13) % 70) + 10,
                ]);
            }
        }

        // Occupancy target ~68/78 rented
        $allUnits = Unit::where('facility_id', $facility->id)->orderBy('id')->get();
        $toRent = $allUnits->take(68);
        $firstNames = ['Alex', 'Jordan', 'Casey', 'Riley', 'Sam', 'Taylor', 'Morgan', 'Jamie', 'Chris', 'Pat'];
        $lastNames = ['Brooks', 'Hayes', 'Coleman', 'Bennett', 'Reed', 'Foster', 'Griffin', 'Palmer', 'Sullivan', 'Warren'];

        foreach ($toRent as $idx => $unit) {
            $customer = Customer::create([
                'facility_id' => $facility->id,
                'first_name' => $firstNames[$idx % count($firstNames)],
                'last_name' => $lastNames[$idx % count($lastNames)].' '.($idx + 1),
                'email' => 'tenant'.($idx + 1).'@example.test',
                'phone' => '(706) 555-'.str_pad((string) (1000 + $idx), 4, '0', STR_PAD_LEFT),
                'status' => 'active',
                'balance' => 0,
                'autopay_enabled' => $idx % 3 === 0,
            ]);

            $paidThrough = now()->subDays($idx % 17 === 0 ? 12 : ($idx % 11 === 0 ? 6 : -5));
            $status = 'active';
            $unitStatus = 'rented';
            if ($paidThrough->lt(now()->subDays(7))) {
                $status = 'locked_out';
                $unitStatus = 'locked_out';
                $customer->status = 'lockout';
            } elseif ($paidThrough->lt(now()->subDays(5))) {
                $status = 'late';
                $unitStatus = 'late';
                $customer->status = 'late';
            }
            $customer->save();

            $rental = Rental::create([
                'facility_id' => $facility->id,
                'customer_id' => $customer->id,
                'unit_id' => $unit->id,
                'move_in_date' => now()->subMonths(rand(1, 18))->toDateString(),
                'paid_through' => $paidThrough->toDateString(),
                'next_bill_date' => $paidThrough->copy()->addDay()->toDateString(),
                'monthly_rate' => $unit->effectiveRate(),
                'status' => $status,
                'lease_signed' => true,
                'lease_signed_at' => now()->subMonths(2),
            ]);

            $unit->update([
                'status' => $unitStatus,
                'gate_code' => (string) random_int(1000, 9999),
            ]);

            if (in_array($status, ['late', 'locked_out'], true)) {
                $invoice = Invoice::create([
                    'facility_id' => $facility->id,
                    'customer_id' => $customer->id,
                    'rental_id' => $rental->id,
                    'invoice_number' => 'INV-SEED-'.strtoupper(Str::random(6)),
                    'due_date' => $paidThrough->toDateString(),
                    'subtotal' => $rental->monthly_rate,
                    'tax' => 0,
                    'total' => $rental->monthly_rate,
                    'amount_paid' => 0,
                    'balance' => $rental->monthly_rate,
                    'status' => 'open',
                    'description' => 'Past due rent - Unit '.$unit->unit_number,
                ]);
                InvoiceLine::create([
                    'invoice_id' => $invoice->id,
                    'description' => $invoice->description,
                    'category' => 'rent',
                    'quantity' => 1,
                    'unit_amount' => $rental->monthly_rate,
                    'amount' => $rental->monthly_rate,
                ]);
                $customer->recalculateBalance();
            } else {
                Payment::create([
                    'facility_id' => $facility->id,
                    'customer_id' => $customer->id,
                    'amount' => $rental->monthly_rate,
                    'method' => $idx % 2 ? 'card' : 'ach',
                    'status' => 'completed',
                    'reference' => 'PAY-SEED-'.$idx,
                    'processor' => 'demo',
                    'paid_at' => now()->subDays(rand(1, 20)),
                ]);
            }

            if ($idx === 0) {
                User::create([
                    'name' => $customer->fullName(),
                    'email' => 'tenant@282storage.com',
                    'password' => Hash::make('password'),
                    'role' => 'tenant',
                    'organization_id' => $org->id,
                    'facility_id' => $facility->id,
                    'customer_id' => $customer->id,
                    'email_verified_at' => now(),
                ]);
                $customer->update(['email' => 'tenant@282storage.com']);
            }
        }

        // Force a few homepage types onto waitlist (limited availability)
        foreach (UnitType::whereIn('name', ['Uncovered parking (40x12)', '10x10x12 Climate Control', '10x15x12 Climate Control'])->get() as $type) {
            Unit::where('unit_type_id', $type->id)->where('status', 'available')->update(['status' => 'rented']);
            WaitingListEntry::create([
                'facility_id' => $facility->id,
                'unit_type_id' => $type->id,
                'name' => 'Waitlist Lead '.$type->id,
                'email' => 'wait'.$type->id.'@example.test',
                'phone' => '(706) 555-0199',
                'texting_consent' => true,
                'desired_move_in' => now()->addDays(14)->toDateString(),
                'notes' => 'Needs climate if possible',
                'status' => 'waiting',
            ]);
        }

        // Ensure container + 10x20 climate have some available for Rent Now
        foreach (['8x20 Container', '10x20x12 Climate Control'] as $name) {
            $type = UnitType::where('facility_id', $facility->id)->where('name', $name)->first();
            $avail = Unit::where('unit_type_id', $type->id)->where('status', 'available')->count();
            if ($avail < 2) {
                Unit::where('unit_type_id', $type->id)->where('status', 'rented')->limit(3)->update(['status' => 'available', 'gate_code' => null]);
            }
        }

        RetailProduct::insert([
            ['facility_id' => $facility->id, 'name' => 'Overlock Service', 'price' => 75, 'inventory_count' => 20, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['facility_id' => $facility->id, 'name' => 'Moving Pad', 'price' => 39, 'inventory_count' => 15, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['facility_id' => $facility->id, 'name' => 'Disc Lock', 'price' => 30, 'inventory_count' => 40, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Task::create([
            'facility_id' => $facility->id,
            'title' => 'Follow up on waitlist for 10x10 climate',
            'due_date' => now()->addDay()->toDateString(),
            'status' => 'open',
        ]);
        Task::create([
            'facility_id' => $facility->id,
            'title' => 'Review locked-out accounts',
            'due_date' => now()->toDateString(),
            'status' => 'open',
        ]);

        $pages = [
            ['title' => 'Rent Storage', 'slug' => 'storage', 'type' => 'page', 'nav' => true, 'body' => '<p>Browse climate-controlled and drive-up storage at 282 Storage in Ellijay. Rent online in minutes.</p>'],
            ['title' => 'Map', 'slug' => 'map', 'type' => 'page', 'nav' => true, 'body' => '<p>Find us at 3875 Tails Creek Rd, Ellijay, GA 30540. Paved roads and easy drive-up access.</p>'],
            ['title' => 'Mike the Storage Guy', 'slug' => 'Mike-the-Storage-Guy', 'type' => 'page', 'nav' => true, 'body' => '<p>Meet Mike — owner-operator, local expert, and the human who answers the phone. Watch tips on YouTube and TikTok @282storage.</p>'],
            ['title' => 'Contact Us', 'slug' => 'contact', 'type' => 'page', 'nav' => true, 'body' => '<p>Call (706) 972-2065 or send a message. Office hours listed in the footer.</p>'],
            ['title' => 'Rent 10x10 Climate', 'slug' => 'rent10x10climate', 'type' => 'lander', 'nav' => false, 'body' => '<p>10x10x12 climate-controlled storage with temperature and humidity control.</p>'],
            ['title' => 'Why Climate Control Matters', 'slug' => 'why-climate-control', 'type' => 'blog', 'nav' => false, 'body' => '<p>Temperature alone is not enough — humidity control protects furniture, electronics, and keepsakes through Georgia seasons.</p>'],
            ['title' => 'Packing Tips from Mike', 'slug' => 'packing-tips-mike', 'type' => 'blog', 'nav' => false, 'body' => '<p>Label boxes on two sides, leave an aisle, and store what you need most near the door.</p>'],
        ];

        foreach ($pages as $i => $page) {
            CmsPage::create([
                'facility_id' => $facility->id,
                'title' => $page['title'],
                'slug' => $page['slug'],
                'page_type' => $page['type'],
                'body' => $page['body'],
                'meta_title' => $page['title'].' | 282 Storage',
                'meta_description' => '282 Storage Ellijay — '.$page['title'],
                'is_published' => true,
                'show_in_nav' => $page['nav'],
                'sort_order' => $i,
                'published_at' => now()->subDays($i),
            ]);
        }

        foreach ([
            'Invoice Reminder', 'Manual Payment Receipt', 'Waiting List Confirmation',
            'Notice of Lockout', 'Storage Agreement', 'Reservation Receipt',
        ] as $name) {
            CommunicationTemplate::create([
                'facility_id' => $facility->id,
                'name' => $name,
                'channel' => 'email',
                'subject' => $name.' — 282 Storage',
                'body' => "Hello [[CUSTOMER_NAME]],\n\n{$name} regarding unit [[UNIT_NUMBER]].\n\nThank you,\n282 Storage",
            ]);
        }
    }
}
