<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group_user;
use App\Models\Group;
use Validator;
use App\Groups;
use Illuminate\Support\Facades\Gate;
use DB;
class GroupController extends Controller
{
    public function getGroups() {
        $groups = DB::table('groups')
            ->select('id','name','description','picture','created_at','updated_at')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($groups, 200);
    }

    public function getGroup($groupId) {
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', auth()->user()->id)
            ->where('Groups_idGroup','=',$groupId)
        ->value('role');
        if($userRole=='god' || $userRole=='admin' || $userRole=='user') {
            $groups = DB::table('groups')
                ->select('id', 'name', 'description', 'picture', 'created_at', 'updated_at')
                ->where('id', '=', $groupId)
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($groups, 200);
        }else{
            return response()->json('Nie masz uprawnień do tej grupy! najpierw do niej dołącz', 400);
        }
    }
        public function getUsersGroups($userId) {
            $groups = DB::table('groups')
                ->join('group_users', 'group_users.Users_idUser', '=', 'groups.id')
                ->select('groups.id', 'groups.name', 'groups.description', 'groups.picture','groups.created_at','groups.created_at'
                    , 'group_users.role', 'group_users.created_at', 'group_users.updated_at')
                ->where('group_users.Users_idUser', '=', $userId)
                ->orderBy('groups.created_at')
                ->distinct()
                ->get()->toJson(JSON_PRETTY_PRINT);
            return response($groups, 200);
        }
    public function getUsersGroup($groupId) {
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', auth()->user()->id)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($userRole=='god' || $userRole=='admin' || $userRole=='user') {
            $groups = DB::table('users')
                ->join('group_users', 'group_users.Users_idUser', '=', 'users.id')
                ->select('users.id', 'users.name', 'users.secondName', 'users.profilePic'
                    , 'group_users.role', 'group_users.created_at', 'group_users.updated_at')
                ->where('group_users.Groups_idGroup', '=', $groupId)
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($groups, 200);
        }else{
            return response()->json('Nie masz uprawnień do tej grupy! najpierw do niej dołącz', 400);
        }
    }
    public function getUserGroup($userId)
    {

            $userGroup = DB::table('users')
                ->select('users.id', 'users.name', 'users.secondName', 'users.profileDesc', 'users.profilePic')
                ->where('id', '=', $userId)
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($userGroup, 200);

    }
    public function createGroup(Request $request) {
        $user=auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'description' => 'required|min:1',
            'picture' => '',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $group = Group::create(array_merge(
            $validator->validated(),

        ));

        return response()->json([
            'message' => 'Utworzono grupę, dodaj znajomych!',
            $user->groups()->attach($group, ['role' => 'god']),

            'group' => $group
        ], 201);

    }
    public function updateGroup(Request $request,$groupId) {
        $role = DB::table('group_users')
            ->where('Users_idUser', '=', auth()->user()->id)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        if($role=='god'|| $role =='admin') {
            $validator = Validator::make($request->all(), [
                'name' => 'string|between:2,100',
                'description' => 'min:1',
                'picture' => '',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $group = Group::create(array_merge(
                $validator->validated(),

            ));

            return response()->json([
                'message' => 'zmodyfikowano dane w grupie!',
                'group' => $group
            ], 201);
        }else {
            return response()->json('Nie masz uprawnień!', 400);
        }
    }
    public function addUserGroup($userId, $groupId)
    {
        if (User::where('id', $userId)->exists()) {
            $user = User::find($userId);
            $group = Group::find($groupId);

            $userRole = DB::table('group_users')
                ->where('Users_idUser', '=', auth()->user()->id)
                ->where('Groups_idGroup', '=', $groupId)
                ->value('role');

            $addUserRole = DB::table('group_users')
                ->where('Users_idUser', '=', $userId)
                ->where('Groups_idGroup', '=', $groupId)
                ->value('role');
            if ($addUserRole != '') {
                return response()->json('Nie można dodać ponownie użytkwonika', 400);

            }
            if ($userRole == 'god') {
                $user->groups()->attach($group, ['role' => 'user']);
                return response()->json('Dodano użytkownika z uprawnieniami zwykłego użytkownika', 201);

            } else if ($userRole == 'admin') {
                $user->groups()->attach($group, ['role' => 'unverified']);
                return response()->json('Dodano użytkownika oczekiwanie na zatwierdzenie przez głównego administratora', 201);

            } else {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }else {
            return response()->json([
                "message" => "Nie znaleziono użytkownika"
            ], 404);
        }

    }
    public function joinGroup($groupId) {
        $addUserRole = DB::table('group_users')
            ->where('Users_idUser', '=', auth()->user()->id)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');

        if($addUserRole == ''){
            $currentUser=auth()->user();
            $group=Group::find($groupId);
            $currentUser->groups()->attach($group, ['role' => 'unverified']);
        }else{
            return response()->json('Nie można dołączyć do grupy w której już jesteś lub jesteś na etapie zatwierdzania', 400);

        }

    }

    public function deleteUserGroup($userId,$groupId)
    {

        if (User::where('id', $userId)->exists()) {
            $user = User::find($userId);
            $group = Group::find($groupId);

            $userRole = DB::table('group_users')
                ->where('Users_idUser', '=', auth()->user()->id)
                ->where('Groups_idGroup', '=', $groupId)
                ->value('role');
            if ($userRole == 'god') {
                $delUserRole = DB::table('group_users')
                    ->where('Users_idUser', '=', $userId)
                    ->where('Groups_idGroup', '=', $groupId)
                    ->value('role');

                if ($delUserRole == 'god') {
                    return response()->json('Nie można usunąć samego siebie z grupy kiedy ją stworzyłeś, jeżeli chcesz opuścić grupę usuń ją!', 400);
                }else if($delUserRole!=''){
                    $user->groups()->detach($group, ['role' => '']);
                    return response()->json('Usunięto użytkownika', 400);
                }else{
                    return response()->json('Użytkownika nie ma w grupie.', 400);
                }
            }else{
                return response()->json('Nie masz uprawnień!', 400);
            }
        }else {
            return response()->json([
                "message" => "Nie znaleziono użytkownika"
            ], 404);
        }
    }
    public function updateUserGroup($userId, $groupId ,$role) {
        if($role=='god'){
            return response()->json('Użytkownik nie może zostać ustawiony na właściciela grupy!', 400);
        }
        $user = User::find($userId);
        $group = Group::find($groupId);
        $userRole = DB::table('group_users')
            ->where('Users_idUser', '=', auth()->user()->id)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        $updUserRole = DB::table('group_users')
            ->where('Users_idUser', '=', $userId)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        if($userRole=='god'){
            if($updUserRole==''){
                return response()->json('Użytkownika nie ma w grupie', 400);
            }else {
                $user->groups()->detach($group, ['role' => '']);
                $user->groups()->attach($group, ['role' => $role]);
               // $result=DB::table('group_users')
               //     ->where('Users_idUser','=',$userId)
               //     ->where('Groups_idGroup','=',$groupId)
                //    ->update(['role'=>$role]);
                return response()->json('Zmieniono uprawnienia użytkownika', 201);

            }
        }else{
            return response()->json('Nie masz uprawnień!', 400);
        }
    }
    public function deleteGroup($groupId)
    {
        $userRole = DB::table('group_users')
            ->where('Users_idUser', '=', auth()->user()->id)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        if ($userRole == 'god') {
            if (Group::where('id', $groupId)->exists()) {
                $group = Group::find($groupId);
                $group->forceDelete();

                return response()->json([
                    "message" => "Usunięto grupę"
                ], 202);
            } else {
                return response()->json([
                    "message" => "Nie znaleziono grupy"
                ], 404);
            }
        }else{
            return response()->json('Nie masz uprawnień!', 400);
        }
    }






}
