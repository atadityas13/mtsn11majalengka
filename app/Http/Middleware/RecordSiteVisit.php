<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordSiteVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->isMethod('GET')) {
            return $response;
        }

        if ($request->is('admin', 'admin/*', 'livewire/*', 'up', 'manifest.webmanifest')) {
            return $response;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return $response;
        }

        try {
            SiteVisit::recordVisit();
        } catch (\Throwable) {
            // Jangan gagalkan halaman publik jika pencatatan visit error.
        }

        return $response;
    }
}
