<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AffiliateRefMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // ref code contoh: /?ref=ABC123
        $ref = trim((string) $request->query('ref', ''));
        if ($ref === '') {
            return $response;
        }

        // last-click-wins: selalu overwrite
        $days = 30;

        return $response
            ->withCookie(cookie('affiliate_ref', $ref, $days * 24 * 60)) // menit
            ->withCookie(cookie('affiliate_ref_set_at', now()->toIso8601String(), $days * 24 * 60));
    }
}
