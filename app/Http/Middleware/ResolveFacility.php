<?php

namespace App\Http\Middleware;

use App\Services\FacilityContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveFacility
{
    public function __construct(protected FacilityContext $facilityContext) {}

    public function handle(Request $request, Closure $next): Response
    {
        $facility = $this->facilityContext->resolve();
        if ($facility) {
            view()->share('facility', $facility);
            $request->attributes->set('facility', $facility);
        }

        return $next($request);
    }
}
