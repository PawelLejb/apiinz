<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group_user;
use App\Models\Group;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use App\Models\Comment_data;
use App\Models\Comment;
use App\Models\Post_data;
use App\Models\Post_tag;
use Validator;
use App\Groups;
use Illuminate\Support\Facades\Gate;
use DB;
class CommentController extends Controller
{
    public function createComment($groupId, $postId, Request $request)
    {
        $userRole = DB::table('group_users')
            ->where('Users_idUser', '=', auth()->user()->id)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        if ($userRole == 'unauthorized' || $userRole == '') {
            return response()->json('Nie masz uprawnień musisz zostać zaakceptowany przez administratora', 400);
        }
        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|between:1,510',

        ]);
        $constant_values_array = array('Posts_idPost' => $postId, 'authorId' => auth()->user()->id, 'author' => auth()->user()->name.' '.auth()->user()->secondName);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $comment = Comment::create(array_merge(
            $constant_values_array,
            $validator->validated(),

        ));

        return response()->json([
            'message' => 'Utworzono komentarz!',
            'comment' => $comment
        ], 201);


    }

    public function updateComment($groupId, $commentId, Request $request)
    {
        if (Comment::where('id', $commentId)->exists()) {
            $comment = Comment::where('id', $commentId);
            $currentUser = auth()->user()->id;
            $userAuthor = DB::table('comments')
                ->where('id', '=', $commentId)
                ->value('authorId');

            $userRole = DB::table('group_users')
                ->where('Users_idUser', '=', $currentUser)
                ->where('Groups_idGroup', '=', $groupId)
                ->value('role');
            if ($currentUser != $userAuthor) {
                if ($userRole != 'god' || $userRole != 'admin') {
                    return response()->json('Nie masz uprawnień!', 400);
                }
            }
            $validator = Validator::make($request->all(), [
                'comment' => 'string|between:1,510',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $comment->update($request->all());
            return response()->json([
                'message' => 'Udało się zmodyfikować dane.',
                'comment' => $comment
            ], 201);

        } else {
            return response()->json([
                "message" => "Nie znaleziono komentarza"
            ], 404);
        }
    }

    public function deleteComment($groupId, $commentId)
    {
        $currentUser = auth()->user()->id;
        $userAuthor = DB::table('comments')
            ->where('id', '=', $commentId)
            ->value('authorId');

        $userRole = DB::table('group_users')
            ->where('Users_idUser', '=', $currentUser)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        if ($currentUser != $userAuthor) {
            if ($userRole != 'god' || $userRole != 'admin') {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }
        if (Comment::where('id', $commentId)->exists()) {
            $comment = Comment::find($commentId);
            $comment->delete();

            return response()->json([
                "message" => "Komentarz usunięty"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono komentarza"
            ], 404);
        }
    }

    public function getComments($groupId, $postId)
    {
        $currentUser = auth()->user()->id;
        $userRole = DB::table('group_users')
            ->where('Users_idUser', '=', $currentUser)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        if ($userRole == 'unauthorized' || $userRole == '') {
            return response()->json('Nie masz uprawnień!', 400);
        }
        $comment = DB::table('comments')
            ->select('comments.id', 'comments.comment', 'comments.author', 'comments.authorId', 'comments.updated_at', 'comments.created_at', 'comments.Posts_idPost')
            ->where('Posts_idPost', '=', $postId)
            ->orderBy('created_at')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($comment, 200);
    }

    // pliki do komentarzy
    public function createCommentData($groupId, $commentId, Request $request)
    {
        $currentUser = auth()->user()->id;
        $userAuthor = DB::table('comments')
            ->where('id', '=', $commentId)
            ->value('authorId');
        $userRole = DB::table('group_users')
            ->where('Users_idUser', '=', $currentUser)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        if ($currentUser != $userAuthor) {
            if ($userRole != 'god' || $userRole != 'admin') {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }

        $validator = Validator::make($request->all(), [
            'data'=>'required|file|mimes:png,jpg,jpeg,pdf,docx,xlsx,csv,txt,zip,rar|max:2048|min:1',
        ]);
        $filenamewithextension = $request->file('data')->getClientOriginalName();
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);
        $extension = $request->file('data')->getClientOriginalExtension();
        $filenametostore = $filename.'_'.uniqid().'.'.$extension;
        Storage::disk('s3')->put($filenametostore, fopen($request->file('data'), 'r+'));

        $constant_values_array = array(
            'dataName'=>$filename,
            'data'=>"https://elasticbeanstalk-eu-central-1-252092827841.s3.eu-central-1.amazonaws.com/".$filenametostore,
            'Comments_idComment' => $commentId);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $commentData = Comment_data::create(array_merge(
            $constant_values_array

        ));

        return response()->json([
            'message' => 'Dodano plik do notatki',
            'post' => $commentData
        ], 201);

    }

    public function deleteCommentData($groupId, $commentId, $commentDataId)
    {
        $currentUser = auth()->user()->id;
        $userAuthor = DB::table('comments')
            ->where('id', '=', $commentId)
            ->value('authorId');
        $userRole = DB::table('group_users')
            ->where('Users_idUser', '=', $currentUser)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        if ($currentUser != $userAuthor) {
            if ($userRole != 'god' || $userRole != 'admin') {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }
        $dataUrl=DB::table('comment_datas')
            ->where('id','=',$commentDataId)
            ->value('data');

        if(Storage::disk('s3')->exists($dataUrl)) {
           $response= Storage::disk('s3')->delete($dataUrl);
        }
        if (Comment_data::where('id', $commentDataId)->exists()) {
            $commentData = Comment_data::find($commentDataId);
            $commentData->delete();

            return response()->json([
                //      "message" => "Plik usunięty",
                "message" => $response
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono pliku"
            ], 404);
        }
    }

    public function updateCommentData($groupId, $commentId, $commentDataId, Request $request)
    {
        $commentData = Comment_data::where('id', $commentDataId);
        $currentUser = auth()->user()->id;
        $userAuthor = DB::table('comments')
            ->where('id', '=', $commentId)
            ->value('authorId');
        $userRole = DB::table('group_users')
            ->where('Users_idUser', '=', $currentUser)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        if ($currentUser != $userAuthor) {
            if ($userRole != 'god' || $userRole != 'admin') {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }
        $validator = Validator::make($request->all(), [
            'dataName' => 'string|between:1,16',
            'data' => 'between:1,1000',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $commentData->update($request->all());
        return response()->json([
            'message' => 'Udało się zmodyfikować dane.',
            'plik:' => $commentData
        ], 201);

    }

    public function getCommentDatas($groupId, $commentId)
    {
        $currentUser = auth()->user()->id;
        $userRole = DB::table('group_users')
            ->where('Users_idUser', '=', $currentUser)
            ->where('Groups_idGroup', '=', $groupId)
            ->value('role');
        if ($userRole == 'unauthorized' || $userRole == '') {
            return response()->json('Nie masz uprawnień!', 400);
        }
        $postData = DB::table('comment_datas')
            ->select('id', 'dataName', 'data', 'Comments_idComment', 'updated_at', 'created_at')
            ->where('Comments_idComment', '=', $commentId)
            ->orderBy('created_at')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($postData, 200);
    }

    public function getCommentData($groupId, $commentId, $commentDataId)
    {
        $currentUser = auth()->user()->id;
        if (Comment::where('id', $commentId)->exists()) {
            $userRole = DB::table('group_users')
                ->where('Users_idUser', '=', $currentUser)
                ->where('Groups_idGroup', '=', $groupId)
                ->value('role');
            if ($userRole == 'unauthorized' || $userRole == '') {
                return response()->json('Nie masz uprawnień!', 400);
            }
            $commentData = DB::table('comment_datas')
                ->select('id', 'dataName', 'data', 'updated_at', 'created_at', 'Comments_idComment')
                ->where('id', '=', $commentDataId)
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($commentData, 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono pliku"
            ], 404);
        }
//


    }
}
