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

        $cacheKey = "facility_id_host_{$host}";
        $facilityId = Cache::remember($cacheKey, 60, function () use ($host) {
            $match = Facility::query()
                ->where(function ($q) use ($host) {
                    $q->where('custom_domain', $host)
                        ->orWhere('custom_domain', 'www.'.$host)
                        ->orWhere('subdomain', $host);
                })
                ->where('is_active', true)
                ->value('id');

            if ($match) {
                return $match;
            }

            // Fallback demo/reference tenant (local + isoverse.ai/storagesoftai staging).
            $defaultSlug = (string) config('storagesoftai.default_facility_slug', '282-storage');
            $useDefault = app()->environment('local')
                || str_contains($host, '127.0.0.1')
                || str_contains($host, 'localhost')
                || str_contains($host, 'isoverse.ai');

            if ($useDefault && $defaultSlug !== '') {
                return Facility::query()
                    ->where('is_active', true)
                    ->where(function ($q) use ($defaultSlug) {
                        $q->where('slug', $defaultSlug)->orWhere('id', '>', 0);
                    })
                    ->orderByRaw('CASE WHEN slug = ? THEN 0 ELSE 1 END', [$defaultSlug])
                    ->value('id');
            }

            return null;
        });

        $facility = $facilityId
            ? Facility::with([
                'organization',
                'settings',
                'unitTypes' => fn ($q) => $q->where('show_on_website', true)->orderBy('sort_order'),
            ])->find($facilityId)
            : null;

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
