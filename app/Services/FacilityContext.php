<?php

namespace App\Services;

use App\Models\Facility;
use Illuminate\Support\Facades\Cache;

class FacilityContext
{
    protected ?Facility $facility = null;

    public function resolve(?string $host = null): ?Facility
    {
        if ($this->facility) {
            return $this->facility;
        }

        $host = $host ?: request()->getHost();
        $host = preg_replace('/^www\./', '', strtolower($host));

        $facility = Cache::remember("facility_host_{$host}", 60, function () use ($host) {
            return Facility::query()
                ->with(['organization', 'settings', 'unitTypes' => fn ($q) => $q->where('show_on_website', true)->orderBy('sort_order')])
                ->where(function ($q) use ($host) {
                    $q->where('custom_domain', $host)
                        ->orWhere('custom_domain', 'www.'.$host)
                        ->orWhere('subdomain', $host);
                })
                ->where('is_active', true)
                ->first();
        });

        // Fallback demo/reference tenant (local + isoverse.ai/storagesoftai staging)
        $defaultSlug = env('DEFAULT_FACILITY_SLUG', '282-storage');
        if (! $facility && $defaultSlug) {
            $useDefault = app()->environment('local')
                || str_contains($host, '127.0.0.1')
                || str_contains($host, 'localhost')
                || str_contains($host, 'isoverse.ai');

            if ($useDefault) {
                $facility = Facility::with(['organization', 'settings', 'unitTypes' => fn ($q) => $q->where('show_on_website', true)->orderBy('sort_order')])
                    ->where('slug', $defaultSlug)
                    ->first();
            }
        }

        $this->facility = $facility;

        return $facility;
    }

    public function set(Facility $facility): void
    {
        $this->facility = $facility;
    }

    public function get(): ?Facility
    {
        return $this->facility ?? $this->resolve();
    }

    public function require(): Facility
    {
        $facility = $this->get();
        abort_unless($facility, 404, 'Facility not found for this domain.');

        return $facility;
    }
}
