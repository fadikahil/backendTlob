<?php
// app/Http/Middleware/OptionalAuth.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class OptionalAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $request->setUserResolver(fn () => $accessToken->tokenable);
            }
        }

        return $next($request);
    }
}