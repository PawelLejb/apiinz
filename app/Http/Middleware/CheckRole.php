<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next)
    {

        $validator = Validator::make($request->all(), [
            'VerySecureKey' => 'required']);
        if ($validator->fails()) {
            return response()->json([
                "message" => "Nie masz dostępu"
            ], 400);
        }
        return $next($request);
    }
}
