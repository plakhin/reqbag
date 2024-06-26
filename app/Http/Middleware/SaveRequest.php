<?php

namespace App\Http\Middleware;

use App\Enums\HttpMethod;
use App\Models\Bag;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SaveRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Bag::where('slug', $request->bag)
            ->sole()
            ->requests()->create([
                'method' => HttpMethod::{$request->method()},
                'url' => $request->fullUrl(),
                'headers' => array_diff_key($request->header(), ['cookie' => []]),
                'post' => $request->post(),
                'raw' => (string) $request,
                'ips' => $request->ips(),
            ]);

        return $next($request);
    }
}
