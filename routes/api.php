<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserNoteController;
use App\Http\Controllers\UserPictureController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityNoteController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware'=>'VerySecureKey'
],function(){
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
});

Route::group([
    'middleware'=>'jwt.auth'
],function(){

    //autoryzacja
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('logout', [AuthController::class,'logout']);
    //

    // Dane uzytkownika
    Route::get('users', [UserController::class,'getUser']);
    Route::get('user/{userId}', [UserController::class,'getSingleUser']);
    
    Route::put('users', [UserController::class,'updateUser']);
    Route::delete('users',[UserController::class,'deleteUser']);
    Route::get('searchUsers/{term}',[UserController::class,'searchUser']);
    //

    // Notatki uzytkownika
    Route::get('userNotes', [UserNoteController::class, 'getAllUserNotes']);
    Route::get('userNote/{id}', [UserNoteController::class,'getUserNote']);
    Route::post('userNotes', [UserNoteController::class,'createUserNote']);
    Route::put('userNotes/{id}', [UserNoteController::class,'updateUserNote']);
    Route::delete('userNotes/{id}',[UserNoteController::class,'deleteUserNote']);
     Route::get('searchNoteTag/{term}', [UserNoteController::class,'searchNoteTag']);
    //pliki do notatek
    Route::get('userNotesData', [UserNoteController::class, 'getAllUserNotesData']);
    Route::get('userNotesData/{id}', [UserNoteController::class,'getUserNoteData']);
    Route::post('userNotesData/{id}', [UserNoteController::class,'addUserNoteData']);
    Route::put('userNotesData/{id}', [UserNoteController::class,'updateUserNoteData']);
    Route::delete('userNotesData/{id}',[UserNoteController::class,'deleteUserNoteData']);
    //
     //tagi notatek
    Route::post('noteTag/{noteId}', [UserNoteController::class,'createNoteTag']);
    Route::delete('noteTag/{tagId}', [UserNoteController::class,'deleteNoteTag']);
    Route::get('noteTags/{noteId}', [UserNoteController::class,'getNoteTags']);
    Route::get('noteTag/{noteTagId}', [UserNoteController::class,'getAllNotesWithTags']);
    Route::get('noteTags', [UserNoteController::class,'getAllNoteTags']);
    //
    //zdjecia uzytkownika
    Route::get('userPictures', [UserPictureController::class, 'getAllPictures']);
    Route::get('userPicture/{id}', [UserPictureController::class,'getPicture']);
    Route::post('userPictures', [UserPictureController::class,'addPicture']);
    Route::put('userPictures/{id}', [UserPictureController::class,'updatePicture']);
    Route::delete('userPictures/{id}',[UserPictureController::class,'deletePicture']);
    //

    //wydarzenia
    Route::get('userEvents/{id}', [EventController::class,'getEvent']);
    Route::get('userEvents', [EventController::class,'getAllEvents']);
    Route::post('userEvents', [EventController::class,'createEvent']);
    Route::put('userEvents/{id}', [EventController::class,'updateEvent']);
    Route::delete('userEvents/{id}', [EventController::class,'deleteEvent']);
    //daty wydarzeń
    Route::post('userEventsDate/{id}', [EventController::class,'createEventDate']);
    Route::delete('userEventsDate/{id}', [EventController::class,'deleteEventDate']);
    Route::put('userEventsDate/{id}', [EventController::class,'updateEventDate']);
    //




    // Notatki przedmiotu
    Route::get('activityNotes/{activityId}', [ActivityNoteController::class, 'getAllActivityNotes']);
    Route::get('activityNote/{id}', [ActivityNoteController::class,'getActivityNote']);
    Route::post('activityNotes/{activityId}', [ActivityNoteController::class,'createActivityNote']);
    Route::put('activityNotes/{id}', [ActivityNoteController::class,'updateActivityNote']);
    Route::delete('activityNotes/{id}',[ActivityNoteController::class,'deletcomment_dataseActivityNote']);
    Route::get('searchNote/{term}', [UserNoteController::class,'searchNote']);
    //pliki do notatek
    Route::post('activityNotesData/{id}', [ActivityNoteController::class,'addActivityNoteData']);
    Route::put('activityNotesData/{id}', [ActivityNoteController::class,'updateActivityNoteData']);
    Route::delete('activityNotesData/{id}',[ActivityNoteController::class,'deleteActivityNoteData']);
    //

    //PLAN ZAJĘĆ
    Route::get('plans', [PlanController::class, 'getAllPlans']);
  //  Route::get('plan/{id}', [PlanController::class,'getPlan']);
    Route::post('plans', [PlanController::class,'createPlan']);
    Route::put('plans/{id}', [PlanController::class,'updatePlan']);
    Route::delete('plans/{id}',[PlanController::class,'deletePlan']);
    //
    //przedmioty
    Route::get('activity/{id}/{planId}', [ActivityController::class,'getActivity']);
    Route::get('activities/{planId}', [ActivityController::class,'getAllActivities']);
    Route::post('activities/{planId}', [ActivityController::class,'createActivity']);
    Route::put('activities/{id}', [ActivityController::class,'updateActivity']);
    Route::delete('activities/{id}', [ActivityController::class,'deleteActivity']);
    //daty przedmiotów
    Route::post('activitiesDate/{planId}/{activityId}', [ActivityController::class,'createActivityDate']);
    Route::delete('activitiesDate/{id}', [ActivityController::class,'deleteActivityDate']);
    Route::put('activitiesDate/{id}', [ActivityController::class,'updateActivityDate']);
     Route::delete('activitiesDates/{id}', [ActivityController::class,'deleteActivityDates']);
    Route::put('activitiesDates/{id}', [ActivityController::class,'updateActivityDates']);
    //
    //GRUPY
    Route::get('group', [GroupController::class,'getGroups']);
    Route::get('group/{groupId}', [GroupController::class,'getGroup']);
    Route::get('groupUsers/{groupId}', [GroupController::class,'getUsersGroup']);
    Route::get('groupUser/{userId}/{groupId}', [GroupController::class,'getUserGroup']);
    Route::post('group', [GroupController::class,'createGroup']);
    Route::get('getUsersGroups/{userId}', [GroupController::class,'getUsersGroups']);
    Route::post('joinGroup/{groupId}', [GroupController::class,'joinGroup']);
    Route::post('groupUser/{userId}/{groupId}', [GroupController::class,'addUserGroup']);
    Route::delete('groupUser/{userId}/{groupId}', [GroupController::class,'deleteUserGroup']);
    Route::delete('group/{groupId}', [GroupController::class,'deleteGroup']);
    Route::put('groupUser/{userId}/{groupId}/{role}', [GroupController::class,'updateUserGroup']);
    Route::put('group/{groupId}', [GroupController::class,'updateGroup']);
    Route::get('searchGroup/{term}', [GroupController::class,'searchGroup']);
    //
    //POSTY
    Route::post('post/{groupId}', [PostController::class,'createPost']);
    Route::put('post/{groupId}/{postId}', [PostController::class,'updatePost']);
    Route::delete('post/{groupId}/{postId}', [PostController::class,'deletePost']);
    Route::get('post/{groupId}/{postId}', [PostController::class,'getPost']);
    Route::get('posts/{groupId}', [PostController::class,'getPosts']);
    Route::get('AllGroupsPosts', [PostController::class,'getAllGroupsPosts']);
    //
    //tagi posta
    Route::post('postTag/{groupId}/{postId}', [PostController::class,'createTag']);
    Route::delete('postTag/{groupId}/{postId}/{tagId}', [PostController::class,'deleteTag']);
    Route::get('postTags/{postId}', [PostController::class,'getPostTags']);
    Route::get('tag/{groupId}/{postTagId}', [PostController::class,'getAllPostsWithTags']);
    Route::get('tags/{groupId}', [PostController::class,'getAllTags']);
     Route::get('searchPostTag/{term}', [PostController::class,'searchPostTag']);
    //

    //pliki posta
    Route::post('postData/{groupId}/{postId}', [PostController::class,'createPostData']);
    Route::delete('postData/{groupId}/{postId}/{postDataId}', [PostController::class,'deletePostData']);
    Route::put('postData/{groupId}/{postId}/{postDataId}', [PostController::class,'updatePostData']);
    Route::get('postData/{groupId}/{postId}/{postDataId}', [PostController::class,'getPostData']);
    Route::get('postData/{groupId}/{postId}', [PostController::class,'getPostDatas']);
    //


    //komentarz
    Route::post('comment/{groupId}/{postId}', [CommentController::class,'createComment']);
    Route::put('comment/{groupId}/{commentId}', [CommentController::class,'updateComment']);
    Route::delete('comment/{groupId}/{commentId}', [CommentController::class,'deleteComment']);
    Route::get('comments/{groupId}/{postId}', [CommentController::class,'getComments']);
    //

    //pliki komentarz
    Route::post('commentData/{groupId}/{commentId}', [CommentController::class,'createCommentData']);
    Route::delete('commentData/{groupId}/{commentId}/{commentDataId}', [CommentController::class,'deleteCommentData']);
    Route::put('commentData/{groupId}/{commentId}/{commentDataId}', [CommentController::class,'updateCommentData']);
    Route::get('commentData/{groupId}/{commentId}/{commentDataId}', [CommentController::class,'getCommentData']);
    Route::get('commentDatas/{groupId}/{commentId}', [CommentController::class,'getCommentDatas']);
    //





});




// Pliki do notatek uzytkownika

//
//Zdjecia uzytkownika

//
