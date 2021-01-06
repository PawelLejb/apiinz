<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use JWTAuth;
use Validator;
use DB;
class UserController extends Controller
{
    public function getUser() {
        return auth()->user();

    }

    public function updateUser(Request $request) {
        $user=auth()->user();
        $user->id;

        $validator = Validator::make($request->all(), [
            'name' => 'string|between:2,100',
            'email' => 'string|email|max:100|unique:users',
            'password' => 'string|min:6',
            'passwordConfirmation' => 'required_with:password|same:password|min:6',
            'secondName'=> 'string|min:1',
            'birthday'=> 'date',
            'pofileDesc'=> 'between:0,250',
            'profilePic'=> 'Active URL',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user->update($request->all());
        return response()->json([
            'message' => 'Udało się zmodyfikować dane.',
            'user' => $user
        ], 201);
    }

    public function deleteUser () {
        $user=auth()->user();
        $id=$user->id;
        if(User::where('id', $id)->exists()) {
            $user = User::find($id);
            $user->delete();

            return response()->json([
                "message" => "Usunięto użytkownika"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono użytkownika"
            ], 404);
        }
    }
        public function searchUser($term) {

        $search = DB::table('users')
                ->select("*", DB::raw("CONCAT(users.first_name,' ',users.last_name) AS full_name"))
                ->where('full_name','like','%'.$term.'%')
                ->get()->toJson(JSON_PRETTY_PRINT);

        return response($search, 200);
        }
}
