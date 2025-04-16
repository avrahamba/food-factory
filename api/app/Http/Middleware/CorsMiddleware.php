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

        $currentOrigin = 'https://food-factory.onrender.com';
        $allowedOrigins = [
            'https://food-factory.onrender.com',
            'https://www.food-factory.onrender.com',
        ];

        // check allowed origins according to referer
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];

            foreach ($allowedOrigins as $allowedOrigin) {
                if (strpos($referer, $allowedOrigin) !== false) {
                    $currentOrigin = $allowedOrigin;
                    break;
                }
            }
        }

        $headers = [
            'Access-Control-Allow-Origin'   => $currentOrigin,
            'Access-Control-Allow-Credentials'  => 'true',
            'Access-Control-Allow-Methods'  => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'  => 'Content-Type, Authorization'
        ];

        foreach ($headers as $key => $value) {
            header(header: $key . ': ' . $value);
        }

        if ($request->getMethod() === 'OPTIONS') {
            return response()->json(['success' => true]);
        }
        return $next($request);
    }
}
