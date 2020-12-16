<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;


class AuthController extends Controller
{
    public function login(Request  $request){
        $user =$request->only('email','password');

        if(!$token = JWTAuth::attempt($user)){
            return response()->json([
                'error'=>'Błędne hasło lub mail'
            ],401);
        }

        return response()->json([
            'token'=>$token,
            auth()->user()
        ],200);


    }
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
            'passwordConfirmation' => 'required_with:password|same:password|min:6',
            'secondName'=> 'required|string|min:1',
            'birthday'=> 'required|date',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'Utworzono użytkownika, witamy!',
            'user' => $user
        ], 201);
    }


    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }


    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    protected function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

}
