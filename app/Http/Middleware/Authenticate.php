<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
    }
    
    /**
     * Stop all system operation when user not authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\Middleware\Authenticate  $guards
     * @return string|null
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            $response = response()->json([
                'code' => 401,
                'status' => 'error',
                'message' => 'Unauthenticated Access'
            ], 401);

            abort($response);
        }
    }
}
