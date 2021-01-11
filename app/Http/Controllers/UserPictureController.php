<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_picture;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Adapter\Ftp as FtpAdapter;
protected $transferMode = FTP_BINARY;
use JWTAuth;
use Validator;
use DB;
class UserPictureController extends Controller
{
   public function addPicture(Request $request) {
        $user=auth()->user();
        $user->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|file|mimes:png,jpg,jpeg|max:1024|min:1',
            'picUrl'=>'',

        ]);
        if($validator->fails()) {

            return response()->json(['error'=>$validator->errors()], 401);
        }
        $filenamewithextension = $request->file('name')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('name')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.uniqid().'.'.$extension;
        Storage::disk('custom-ftp')->put($filenametostore, fopen($request->file('name'), 'r+'));
        $constant_values_array=array('Users_idUser'=>$user->id,
            'name'=>$filename,
            'picUrl'=>$filenametostore
        );
        $userPicutre = User_picture::create(array_merge(
            $constant_values_array
        ));
        $user->update(array('profilePic'=>"http://studenthelperappapiftp.prv.pl/studenthelperappapi/" . $filenametostore));
        return response()->json([
            'message' => 'Dodałeś zdjęcie',
            'userPicture' => $userPicutre
        ], 201);

    }
    public function deletePicture ($id) {
        $user=auth()->user();
        $user->id;
        $picUrl=DB::table('user_pictures')
            ->where('Users_idUser','=',$user->id)
            ->where('id','=',$id)
            ->value('picUrl');
            Storage::disk('custom-ftp')->delete($picUrl);
        if(User_picture::where('id', $id )->exists()) {
            $user_picture = User_picture::find($id);
            $user_picture->delete();

            return response()->json([
                "message" => "Zdjęcie usunięte"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono zdjęcia"
            ], 404);
        }
    }
    public function updatePicture(Request $request, $id)
    {
        if (User_picture::where('id', $id)->exists()) {
            $user_picture = User_picture::find($id);
            $user_picture->name = is_null($request->name) ? $user_picture->name : $request->name;
            $user_picture->picUrl = is_null($request->picUrl) ? $user_picture->picUrl : $request->picUrl;

            $user_picture->save();

            return response()->json([
                "message" => "Zmodyfikowano zdjęcie pomyślnie"
            ], 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono zdjęcia"
            ], 404);

        }
    }
        public function getAllPictures() {
            $user=auth()->user();
            $id=$user->id;

            $user_picture = DB::table('user_pictures')
                ->leftjoin('users','users.id','=','user_pictures.Users_idUser')
                ->select('users.id','user_pictures.id AS PictureId','user_pictures.name','user_pictures.picUrl','user_pictures.updated_at','user_pictures.created_at')
                ->where('Users_idUser','=',$id)
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($user_picture, 200);


        }
    public function getPicture($id) {

        if (User_picture::where('id', $id )->exists()) {
        $user_picture = DB::table('user_pictures')
            ->rightjoin('users','users.id','=','user_pictures.Users_idUser')
            ->select('users.id','user_pictures.id AS PictureId','user_pictures.name','user_pictures.picUrl','user_pictures.updated_at','user_pictures.created_at')
            ->where('user_pictures.id','=',$id)
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($user_picture, 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono zdjecia"
            ], 404);
        }

    }

}
