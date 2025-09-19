<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Transformers\UserTransformer;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseApiController
{
    public function login(Request $request): JsonResponse
    {
        $credentials = ['password' => $request->get('password')];

        if (filter_var($request->get('login'), FILTER_VALIDATE_EMAIL)) {
            $credentials['email'] = $request->get('login');
        } else {
            $credentials['username'] = $request->get('login');
        }

        if (!$token = $this->guard()->attempt($credentials)) {
            return $this->respondUnauthorized();
        }

        try {
            /** @var User $user */
            $user = $this->guard()->user();
        } catch (\Exception $e) {
            return $this->respondInternalServerError($e->getMessage());
        }

        return $this->respond(true, array_merge(['token' => $token], $user->getJWTCustomClaims()), 200, 0, '');
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->guard()->logout();
            return $this->respond(true);
        } catch (\Exception $e) {
            return $this->respondInternalServerError($e->getMessage());
        }
    }

    public function me(): JsonResponse
    {
        $user = $this->transform(
            auth()->user(),
            UserTransformer::class,
            'user');

        return $this->respond(true, $user);
    }

    public function guard(): Guard|StatefulGuard
    {
        return Auth::guard('api');
    }
}
