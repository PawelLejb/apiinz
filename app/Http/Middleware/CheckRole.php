<?php

namespace App\Http\Middleware;

use Closure;
use Validator;
use Illuminate\Http\Request;

class CheckRole
{
      /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $validator = Validator::make($request->all(), [
            'VerySecureKey' => 'required|min:4|max:4']);
        if ($validator->fails()) {
            return response()->json([
                "message" => "Nie masz dostępu"
            ], 400);
        }
        return $next($request);
    }
}
