<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('plan')->default('pro_ai'); // starter, growth, pro_ai
            $table->string('subscription_status')->default('active'); // trial, active, past_due, cancelled
            $table->timestamp('trial_ends_at')->nullable();
            $table->boolean('remove_powered_by')->default(false);
            $table->json('entitlements')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('site_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->json('theme_tokens')->nullable();
            $table->json('starter_pages')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('site_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('custom_domain')->nullable()->unique();
            $table->string('subdomain')->nullable()->unique();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 32)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('phone')->nullable();
            $table->string('owner_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('timezone')->default('America/New_York');
            $table->string('currency', 8)->default('USD');
            $table->string('date_format')->default('m/d/Y');
            $table->string('phone_format')->default('(555) 555-5555');
            $table->unsignedTinyInteger('invoice_lead_days')->default(7);
            $table->unsignedTinyInteger('move_out_days_restriction')->default(10);
            $table->unsignedTinyInteger('future_reservation_window_days')->default(1);
            $table->boolean('customers_can_prepay')->default(true);
            $table->boolean('auto_approve_rentals')->default(true);
            $table->boolean('customers_can_edit_profile')->default(true);
            $table->boolean('customers_can_edit_payment_accounts')->default(true);
            $table->boolean('customers_can_schedule_move_outs')->default(true);
            $table->string('gate_provider')->nullable();
            $table->boolean('gate_keys_enabled')->default(true);
            $table->string('login_cta_text')->default('Make a Payment/Login');
            $table->string('primary_color')->default('#114393');
            $table->string('secondary_color')->default('#0edb47');
            $table->string('accent_color')->default('#0b2f6b');
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('google_review_url')->nullable();
            $table->json('office_hours')->nullable();
            $table->json('amenities')->nullable();
            $table->text('about')->nullable();
            $table->string('brand_tagline')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['organization_id', 'slug']);
        });

        Schema::create('facility_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('late_days')->default(5);
            $table->decimal('late_fee', 10, 2)->default(15);
            $table->unsignedTinyInteger('lockout_days')->default(7);
            $table->decimal('lockout_fee', 10, 2)->default(5);
            $table->unsignedTinyInteger('lien_days')->default(31);
            $table->decimal('lien_fee', 10, 2)->default(100);
            $table->unsignedTinyInteger('auction_days')->default(45);
            $table->string('proration_mode')->default('prorate_this_month_bill_now');
            $table->boolean('disable_partial_payments_when_locked_out')->default(true);
            $table->timestamps();
        });

        Schema::create('unit_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('length_ft', 8, 2)->nullable();
            $table->decimal('width_ft', 8, 2)->nullable();
            $table->decimal('height_ft', 8, 2)->nullable();
            $table->decimal('monthly_rate', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('climate_controlled')->default(false);
            $table->boolean('show_on_website')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_type_id')->constrained()->cascadeOnDelete();
            $table->string('unit_number');
            $table->string('status')->default('available');
            // available, reserved, rented, late, locked_out, pre_lien, lien, auction, pending, moving_out, unavailable
            $table->decimal('monthly_rate', 10, 2)->nullable();
            $table->string('gate_code')->nullable();
            $table->decimal('map_x', 8, 2)->nullable();
            $table->decimal('map_y', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['facility_id', 'unit_number']);
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 32)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('status')->default('active'); // active, late, lockout, archived, lead, waitlist
            $table->decimal('balance', 10, 2)->default(0);
            $table->boolean('autopay_enabled')->default(false);
            $table->boolean('late_fee_exempt')->default(false);
            $table->boolean('tax_exempt')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['facility_id', 'last_name', 'first_name']);
            $table->index(['facility_id', 'email']);
        });

        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->date('move_in_date');
            $table->date('paid_through')->nullable();
            $table->date('next_bill_date')->nullable();
            $table->date('scheduled_move_out')->nullable();
            $table->date('moved_out_at')->nullable();
            $table->decimal('monthly_rate', 10, 2);
            $table->string('status')->default('active'); // active, late, locked_out, pre_lien, lien, auction, moved_out, reserved
            $table->boolean('lease_signed')->default(false);
            $table->timestamp('lease_signed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rental_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->date('due_date');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('status')->default('open'); // open, paid, partial, void
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('category')->default('rent'); // rent, late_fee, lockout_fee, lien_fee, retail, other
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('unit_amount', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('method')->default('card'); // card, ach, cash, check, other
            $table->string('status')->default('completed'); // pending, completed, failed, refunded
            $table->string('reference')->nullable();
            $table->string('processor')->default('demo');
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('waiting_list_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_type_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('texting_consent')->default(false);
            $table->date('desired_move_in')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('waiting'); // waiting, contacted, rented, removed
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->string('source')->default('contact_form');
            $table->string('status')->default('new'); // new, contacted, converted, closed
            $table->timestamps();
        });

        Schema::create('retail_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('inventory_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('open'); // open, completed
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('page_type')->default('page'); // page, blog, lander
            $table->longText('body')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('show_in_nav')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['facility_id', 'slug']);
        });

        Schema::create('communication_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('channel')->default('email'); // email, sms, print
            $table->string('subject')->nullable();
            $table->longText('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('facility_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index(['facility_id', 'created_at']);
        });

        Schema::create('ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // briefing, pricing, delinquency, content, chat
            $table->string('prompt')->nullable();
            $table->longText('suggestion');
            $table->string('status')->default('pending'); // pending, accepted, dismissed
            $table->json('meta')->nullable();
            $table->timestamp('acted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('rental_intents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->date('desired_move_in')->nullable();
            $table->string('status')->default('started'); // started, account_created, lease_pending, paid, completed, abandoned
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $tables = [
            'rental_intents', 'ai_suggestions', 'audit_logs', 'communication_templates',
            'cms_pages', 'tasks', 'retail_products', 'leads', 'waiting_list_entries',
            'payments', 'invoice_lines', 'invoices', 'rentals', 'customers', 'units',
            'unit_types', 'facility_settings', 'facilities', 'site_templates', 'organizations',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
