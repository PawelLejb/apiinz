<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group_user;
use App\Models\Group;
use App\Models\Post;
use App\Models\Post_data;
use App\Models\Post_tag;
use Validator;
use Illuminate\Support\Facades\Storage;
use App\groups;
use Illuminate\Support\Facades\Gate;
use DB;
class PostController extends Controller
{
    public function createPost($groupId,Request $request) {
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', auth()->user()->id)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($userRole=='unverified' || $userRole==''){
            return response()->json('Nie masz uprawnień musisz zostać zaakceptowany przez administratora', 400);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|between:6,60',
            'post' => 'required|between:10,1000',
        ]);
        $constant_values_array = array('Groups_idGroup' => $groupId,'authorId'=>auth()->user()->id,'author'=>auth()->user()->name.' '.auth()->user()->secondName);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $post = Post::create(array_merge(
            $constant_values_array,
            $validator->validated(),

        ));
        $constant_values_array=array('name' =>auth()->user()->name,
            'secondName' =>auth()->user()->secondName,
            'profilePic'=>auth()->user()->profilePic,
                                     'Groups_idGroup' => $groupId,
                                     
        'authorId' => auth()->user()->id);
        $post1=array_merge(
            $comment,
            $constant_values_array,
            $validator->validated(),

        );
        return response()->json([
            'message' => 'Utworzono post!',
            'post' => $post1
        ], 201);

    }
    public function updatePost($groupId,$postId,Request $request) {
        if (Post::where('id',$postId )->exists()) {
            $post =Post::where('id',$postId );
        $currentUser=auth()->user()->id;
            $userAuthor=DB::table('posts')
                ->where('id','=', $postId)
                ->value('authorId');

        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', $currentUser)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($currentUser!=$userAuthor) {
            if ($userRole != 'god' || $userRole != 'admin') {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }
        $validator = Validator::make($request->all(), [
            'title' => 'string|between:6,60',
            'post' => 'between:10,1000',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $post->update($request->all());
        return response()->json([
            'message' => 'Udało się zmodyfikować dane.',
            'post' => $post
        ], 201);

        } else {
        return response()->json([
        "message" => "Nie znaleziono notatki"
        ], 404);
        }
    }
    public function deletePost($groupId,$postId) {
        $currentUser=auth()->user()->id;
        $userAuthor=DB::table('posts')
            ->where('id','=', $postId)
            ->value('authorId');
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', $currentUser)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($currentUser!=$userAuthor) {
            if ($userRole != 'god' || $userRole != 'admin') {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }
        if(Post::where('id', $postId )->exists()) {
            $post = Post::find($postId);
            $post->delete();

            return response()->json([
                "message" => "Post usunięty"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono posta"
            ], 404);
        }
    }

    public function getPost($groupId,$postId) {
        $currentUser=auth()->user()->id;
        if (Post::where('id', $postId )->exists()) {
        $userRole=DB::table('group_users')
           
            ->where('Users_idUser','=', $currentUser)
            ->where('Groups_idGroup','=',$groupId)
           
            ->value('role');
        if($userRole=='unverified' || $userRole==''){
            return response()->json('Nie masz uprawnień!', 400);
        }

            $post = DB::table('posts')
                 ->join('users','users.id','=','posts.authorId')
                ->select('posts.id','posts.title','posts.post','posts.authorId','posts.updated_at','posts.created_at','posts.Groups_idGroup','users.profilePic','users.name','users.secondName')
                ->where('posts.id','=',$postId)
              
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($post, 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono wydarzenia"
            ], 404);
        }

    }

   
     public function getAllGroupsPosts() {
        $currentUser=auth()->user()->id;
        $post = DB::table('posts')
            ->join('group_users','posts.Groups_idGroup','=','group_users.Groups_idGroup')
             ->join('users','users.id','=','posts.authorId')
            ->join('groups','groups.id','=','posts.Groups_idGroup')
            ->select('posts.id','posts.title','posts.authorId','posts.updated_at','posts.created_at','posts.Groups_idGroup','posts.post','group_users.role','users.profilePic','users.name','users.secondName','groups.name as groupName')
    
            ->where('group_users.Users_idUser','=',$currentUser)
            ->where('group_users.role','!=','unverified')
            ->orderBy('created_at')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($post, 200);
    }
    public function getPosts($groupId) {
        $currentUser=auth()->user()->id;
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', $currentUser)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($userRole=='unverified' || $userRole==''){
            return response()->json('Nie masz uprawnień!', 400);
        }
        $post = DB::table('posts')
             ->join('users','users.id','=','posts.authorId')
           ->select('posts.id','posts.title','posts.authorId','posts.post','posts.updated_at','posts.created_at','posts.Groups_idGroup','users.profilePic','users.name','users.secondName')
           
            ->where('Groups_idGroup','=',$groupId)
 
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($post, 200);
    }


    //TAGI
    public function createTag($groupId,$postId,Request $request) {
        $currentUser=auth()->user()->id;
        $userAuthor=DB::table('posts')
            ->where('id','=', $postId)
            ->value('authorId');
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', $currentUser)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($currentUser!=$userAuthor) {
            if ($userRole != 'god' || $userRole != 'admin') {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:1,16',
        ]);
        $constant_values_array = array('Posts_idPost' => $postId);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $post = Post_tag::create(array_merge(
            $constant_values_array,
            $validator->validated(),

        ));

        return response()->json([
            'message' => 'Utworzono post!',
            'post' => $post
        ], 201);
    }
    public function deleteTag($groupId,$postId,$tagId) {
        $currentUser=auth()->user()->id;
        $userAuthor=DB::table('posts')
            ->where('id','=', $postId)
            ->value('authorId');
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', $currentUser)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($currentUser!=$userAuthor) {
            if ($userRole != 'god' || $userRole != 'admin') {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }
        if(Post_tag::where('id', $tagId )->exists()) {
            $postTag = Post_tag::find($tagId);
            $postTag->delete();

            return response()->json([
                "message" => "Tag usunięty"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono taga"
            ], 404);
        }

    }
    public function getPostTags($postId) {
        if(Post::where('id', $postId )->exists()) {
        $postTags = DB::table('post_tags')
            ->select('post_tags.id','post_tags.name','post_tags.Posts_idPost','post_tags.updated_at','post_tags.created_at')
            ->where('Posts_idPost','=',$postId)
            ->orderBy('created_at')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($postTags, 200);
        }else{
            return response()->json([
                "message" => "Nie znaleziono posta"
            ], 404);
        }
    }
    public function getAllTags($groupId) {
        $postTags = DB::table('post_tags')
            ->join('posts','posts.id','=','post_tags.Posts_idPost')
            ->select('post_tags.name','post_tags.created_at')
            ->where('posts.Groups_idGroup','=',$groupId)
            ->orderBy('post_tags.created_at')
            ->distinct()
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($postTags, 200);
    }
    public function getAllPostsWithTags($groupId,$postTagId) {
        $postWithTags = DB::table('post_tags')
            ->join('posts','posts.id','=','post_tags.Posts_idPost')
            ->select('post_tags.id as post_tags.id','post_tags.name as post_tags.name',
           'posts.id','posts.title','posts.author','posts.authorId','posts.updated_at','posts.created_at','posts.Groups_idGroup' )
            ->where('posts.Groups_idGroup','=',$groupId)
            ->where('post_tags.id','=',$postTagId)
            ->orderBy('created_at')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($postWithTags, 200);
    }
//TAGI

//PLIKI DO POSTA
     public function createPostData($groupId,$postId,Request $request) {
        $currentUser=auth()->user()->id;
        $userAuthor=DB::table('posts')
            ->where('id','=', $postId)
            ->value('authorId');
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', $currentUser)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($currentUser!=$userAuthor) {
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
            'data'=>'https://elasticbeanstalk-eu-central-1-252092827841.s3.eu-central-1.amazonaws.com/'.$filenametostore,

            'Posts_idPost' => $postId);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $postData = Post_data::create(array_merge(
            $constant_values_array
        ));

        return response()->json([
            'message' => 'Dodano plik do posta!',
            'post' => $postData
        ], 201);

    }
    public function deletePostData ($groupId,$postId,$postDataId) {
        $currentUser=auth()->user()->id;
        $userAuthor=DB::table('posts')
            ->where('id','=', $postId)
            ->value('authorId');
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', $currentUser)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($currentUser!=$userAuthor) {
            if ($userRole != 'god' || $userRole != 'admin') {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }
        $dataUrl=DB::table('post_datas')
            ->where('id','=',$postDataId)
            ->value('data');

        if(Storage::disk('s3')->exists(str_replace('https://elasticbeanstalk-eu-central-1-252092827841.s3.eu-central-1.amazonaws.com/','',$dataUrl))) {
            Storage::disk('s3')->delete(str_replace('https://elasticbeanstalk-eu-central-1-252092827841.s3.eu-central-1.amazonaws.com/','',$dataUrl));
        }
        if(Post_data::where('id', $postDataId )->exists()) {
            $postData = Post_data::find($postDataId);
            $postData->delete();
            return response()->json([
                "message" => "Plik usunięty"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono pliku"
            ], 404);
        }
    }
    public function updatePostData($groupId,$postId,$postDataId,Request $request) {
            $postData =Post_data::where('id',$postDataId );
        $currentUser=auth()->user()->id;
        $userAuthor=DB::table('posts')
            ->where('id','=', $postId)
            ->value('authorId');
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', $currentUser)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($currentUser!=$userAuthor) {
            if ($userRole != 'god' || $userRole != 'admin') {
                return response()->json('Nie masz uprawnień!', 400);
            }
        }
        $validator = Validator::make($request->all(), [
            'dataName' => 'string|between:1,16',
            'data' => 'between:1,1000',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $postData->update($request->all());
        return response()->json([
            'message' => 'Udało się zmodyfikować dane.',
            'plik:' => $postData
        ], 201);

        }
    public function getPostDatas($groupId,$postId) {
        $currentUser=auth()->user()->id;
        $userRole=DB::table('group_users')
            ->where('Users_idUser','=', $currentUser)
            ->where('Groups_idGroup','=',$groupId)
            ->value('role');
        if($userRole=='unverified' || $userRole==''){
            return response()->json('Nie masz uprawnień!', 400);
        }
        $postData = DB::table('post_datas')
            ->select('id','dataName','data','Posts_idPost','updated_at','created_at')
            ->where('Posts_idPost','=',$postId)
            ->orderBy('created_at')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($postData, 200);
    }
    public function getPostData($groupId,$postId,$postDataId) {
        $currentUser=auth()->user()->id;
        if (Post::where('id', $postId )->exists()) {
            $userRole=DB::table('group_users')
                ->where('Users_idUser','=', $currentUser)
                ->where('Groups_idGroup','=',$groupId)
                ->value('role');
            if($userRole=='unverified' || $userRole==''){
                return response()->json('Nie masz uprawnień!', 400);
            }
            $postData = DB::table('post_datas')
                ->select('id','dataName','data','updated_at','created_at','Posts_idPost')
                ->where('id','=',$postDataId)
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($postData, 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono wydarzenia"
            ], 404);
        }

    }
public function searchPostTag($term) {

        $search = DB::table('post_tags')
            ->select("*")
            ->where('name','like','%'.$term.'%')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($search, 200);
    }
//PLIKI DO POSTA


}
