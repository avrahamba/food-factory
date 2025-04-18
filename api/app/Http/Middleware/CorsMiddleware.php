<?php

namespace App\Http\Middleware;

use App\Http\Controllers\GeneralController;
use Closure;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Exception;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = [
            'https://food-factory.onrender.com',
            'https://www.food-factory.onrender.com',
        ];

        $origin = $request->header('Origin');

        // If origin is in our allowed list, set it as the allowed origin
        if (in_array($origin, $allowedOrigins)) {
            $currentOrigin = $origin;
        } else {
            // Default fallback
            $currentOrigin = 'https://food-factory.onrender.com';
        }


        $headers = [
            'Access-Control-Allow-Origin'   => $currentOrigin,
            'Access-Control-Allow-Credentials'  => 'true',
            'Access-Control-Allow-Methods'  => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            'Access-Control-Max-Age' => '86400', // 24 hours
        ];

        foreach ($headers as $key => $value) {
            header(header: $key . ': ' . $value);
        }

        // Handle preflight OPTIONS request
        if ($request->isMethod('OPTIONS')) {
            return response()->json(['success' => true], 200);
        } else {
            return $next($request);
        }
    }
}
