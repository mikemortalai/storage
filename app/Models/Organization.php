<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'plan', 'subscription_status',
        'trial_ends_at', 'remove_powered_by', 'entitlements',
    ];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'remove_powered_by' => 'boolean',
            'entitlements' => 'array',
        ];
    }

    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasEntitlement(string $feature): bool
    {
        $defaults = match ($this->plan) {
            'starter' => ['website', 'rent_online', 'tenant_portal', 'basic_reports'],
            'growth' => ['website', 'rent_online', 'tenant_portal', 'basic_reports', 'delinquency', 'sms', 'gate', 'retail', 'advanced_reports', 'multi_user'],
            default => ['website', 'rent_online', 'tenant_portal', 'basic_reports', 'delinquency', 'sms', 'gate', 'retail', 'advanced_reports', 'multi_user', 'ai_copilot', 'ai_pricing', 'ai_chat', 'ai_content'],
        };

        $entitlements = $this->entitlements ?? $defaults;

        return in_array($feature, $entitlements, true);
    }
}
