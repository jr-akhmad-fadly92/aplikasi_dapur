<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to add cache headers to API responses
 * 
 * Usage:
 *   Route::get(...)->middleware('api.cache:3600')
 */
class CacheApiResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|null  $seconds  Cache duration in seconds (default: 300)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $seconds = 300)
    {
        $response = $next($request);

        // Only cache successful GET/HEAD responses
        if ($request->isMethod(['GET', 'HEAD']) && $response->getStatusCode() === 200) {
            $ttl = (int) $seconds;
            
            // Set cache headers
            $response->header('Cache-Control', "public, max-age={$ttl}");
            $response->header('Pragma', 'cache');
            $response->header('Expires', gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT');
        } else {
            // Don't cache non-GET requests or error responses
            $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', '0');
        }

        return $response;
    }
}
