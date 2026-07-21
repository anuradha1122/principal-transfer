<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AttachAuditContext
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $requestId = $request->headers->get('X-Request-ID')
            ?: (string) Str::uuid();

        /*
         * Laravel Context allows services and observers to retrieve request
         * information without tightly coupling themselves to HTTP code.
         */
        Context::add([
            'audit.request_id' => $requestId,
            'audit.route_name' => $request->route()?->getName(),
            'audit.http_method' => $request->method(),
            'audit.url' => $request->fullUrl(),
            'audit.ip_address' => $request->ip(),
            'audit.user_agent' => $request->userAgent(),
        ]);

        $request->attributes->set(
            'audit_request_id',
            $requestId
        );

        $response = $next($request);

        $response->headers->set(
            'X-Request-ID',
            $requestId
        );

        return $response;
    }
}
