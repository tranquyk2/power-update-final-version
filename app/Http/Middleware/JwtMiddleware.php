<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Http\Request;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = JWTAuth::getToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token not provided',
                ], 401);
            }

            $user = JWTAuth::authenticate($token);

            if (!$user) {
                throw new Exception('User is invalid');
            }

            auth()->login($user);
        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Token is Invalid'], 401);
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token is Expired'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
