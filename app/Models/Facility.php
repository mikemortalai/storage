<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id', 'site_template_id', 'name', 'slug', 'custom_domain', 'subdomain',
        'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'phone', 'owner_phone',
        'email', 'timezone', 'currency', 'date_format', 'phone_format', 'invoice_lead_days',
        'move_out_days_restriction', 'future_reservation_window_days', 'customers_can_prepay',
        'auto_approve_rentals', 'customers_can_edit_profile', 'customers_can_edit_payment_accounts',
        'customers_can_schedule_move_outs', 'gate_provider', 'gate_keys_enabled', 'login_cta_text',
        'primary_color', 'secondary_color', 'accent_color', 'facebook_url', 'instagram_url',
        'tiktok_url', 'youtube_url', 'google_review_url', 'office_hours', 'amenities', 'about',
        'brand_tagline', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'office_hours' => 'array',
            'amenities' => 'array',
            'customers_can_prepay' => 'boolean',
            'auto_approve_rentals' => 'boolean',
            'customers_can_edit_profile' => 'boolean',
            'customers_can_edit_payment_accounts' => 'boolean',
            'customers_can_schedule_move_outs' => 'boolean',
            'gate_keys_enabled' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(FacilitySetting::class);
    }

    public function unitTypes(): HasMany
    {
        return $this->hasMany(UnitType::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(Rental::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function waitingListEntries(): HasMany
    {
        return $this->hasMany(WaitingListEntry::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function pages(): HasMany
    {
        return $this->hasMany(CmsPage::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function retailProducts(): HasMany
    {
        return $this->hasMany(RetailProduct::class);
    }

    public function fullAddress(): string
    {
        return trim(collect([
            $this->address_line1,
            $this->city,
            trim(($this->state ?? '').' '.($this->postal_code ?? '')),
        ])->filter()->implode(', '));
    }
}
