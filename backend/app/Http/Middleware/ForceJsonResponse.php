<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Force every API request to be treated as a JSON request.
     *
     * Without this, a request whose Accept header is not application/json makes
     * Laravel answer auth/validation failures with a browser 302 redirect (or a
     * 500 "Route [login] not defined"). The Nuxt proxy cannot relay that redirect
     * and returns 502 Bad Gateway to the client. Forcing Accept: application/json
     * guarantees consistent 401/403/422/500 JSON responses instead.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
