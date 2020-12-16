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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('{VerySecureKey}/login', [AuthController::class, 'login'])->where('VerySecureKey', 'abcd');
Route::post('{VerySecureKey}/register', [AuthController::class, 'register'])->where('VerySecureKey', 'abcd');


Route::group([
    'middleware'=>'jwt.auth'
    ],function(){

    //autoryzacja
    Route::post('/{VerySecureKey}/refresh', [AuthController::class,'refresh'])->where('/VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/logout', [AuthController::class,'logout'])->where('VerySecureKey', 'abcd');
    //

    // Dane uzytkownika
    Route::get('{VerySecureKey}/users', [UserController::class,'getUser'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/users', [UserController::class,'updateUser'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/users',[UserController::class,'deleteUser'])->where('VerySecureKey', 'abcd');
    //

    // Notatki uzytkownika
    Route::get('{VerySecureKey}/userNotes', [UserNoteController::class, 'getAllUserNotes'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/userNote/{id}', [UserNoteController::class,'getUserNote'])->where('VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/userNotes', [UserNoteController::class,'createUserNote'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/userNotes/{id}', [UserNoteController::class,'updateUserNote'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/userNotes/{id}',[UserNoteController::class,'deleteUserNote'])->where('VerySecureKey', 'abcd');
    //pliki do notatek
    Route::get('{VerySecureKey}/userNotesData', [UserNoteController::class, 'getAllUserNotesData'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/userNotesData/{id}', [UserNoteController::class,'getUserNoteData'])->where('VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/userNotesData/{id}', [UserNoteController::class,'addUserNoteData'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/userNotesData/{id}', [UserNoteController::class,'updateUserNoteData'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/userNotesData/{id}',[UserNoteController::class,'deleteUserNoteData'])->where('VerySecureKey', 'abcd');
    //

    //zdjecia uzytkownika
    Route::get('{VerySecureKey}/userPictures', [UserPictureController::class, 'getAllPictures'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/userPicture/{id}', [UserPictureController::class,'getPicture'])->where('VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/userPictures', [UserPictureController::class,'addPicture'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/userPictures/{id}', [UserPictureController::class,'updatePicture'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/userPictures/{id}',[UserPictureController::class,'deletePicture'])->where('VerySecureKey', 'abcd');
    //

    //wydarzenia
    Route::get('{VerySecureKey}/userEvents/{id}', [EventController::class,'getEvent'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/userEvents', [EventController::class,'getAllEvents'])->where('VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/userEvents', [EventController::class,'createEvent'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/userEvents/{id}', [EventController::class,'updateEvent'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/userEvents/{id}', [EventController::class,'deleteEvent'])->where('VerySecureKey', 'abcd');
    //daty wydarzeń
    Route::post('{VerySecureKey}/userEventsDate/{id}', [EventController::class,'createEventDate'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/userEventsDate/{id}', [EventController::class,'deleteEventDate'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/userEventsDate/{id}', [EventController::class,'updateEventDate'])->where('VerySecureKey', 'abcd');
    //




    // Notatki przedmiotu
    Route::get('{VerySecureKey}/activityNotes/{activityId}', [ActivityNoteController::class, 'getAllActivityNotes'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/activityNote/{id}', [ActivityNoteController::class,'getActivityNote'])->where('VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/activityNotes/{activityId}', [ActivityNoteController::class,'createActivityNote'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/activityNotes/{id}', [ActivityNoteController::class,'updateActivityNote'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/activityNotes/{id}',[ActivityNoteController::class,'deletcomment_dataseActivityNote'])->where('VerySecureKey', 'abcd');
    //pliki do notatek
    Route::post('{VerySecureKey}/activityNotesData/{id}', [ActivityNoteController::class,'addActivityNoteData'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/activityNotesData/{id}', [ActivityNoteController::class,'updateActivityNoteData'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/activityNotesData/{id}',[ActivityNoteController::class,'deleteActivityNoteData'])->where('VerySecureKey', 'abcd');
    //

    //PLAN ZAJĘĆ
    Route::get('{VerySecureKey}/plans', [PlanController::class, 'getAllPlans'])->where('VerySecureKey', 'abcd');
  //  Route::get('{VerySecureKey}/plan/{id}', [PlanController::class,'getPlan'])->where('VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/plans', [PlanController::class,'createPlan'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/plans/{id}', [PlanController::class,'updatePlan'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/plans/{id}',[PlanController::class,'deletePlan'])->where('VerySecureKey', 'abcd');
    //
    //przedmioty
    Route::get('{VerySecureKey}/activity/{id}/{planId}', [ActivityController::class,'getActivity'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/activities/{planId}', [ActivityController::class,'getAllActivities'])->where('VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/activities/{planId}', [ActivityController::class,'createActivity'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/activities/{id}', [ActivityController::class,'updateActivity'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/activities/{id}', [ActivityController::class,'deleteActivity'])->where('VerySecureKey', 'abcd');
    //daty przedmiotów
    Route::post('{VerySecureKey}/activitiesDate/{planId}/{activityId}', [ActivityController::class,'createActivityDate'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/activitiesDate/{id}', [ActivityController::class,'deleteActivityDate'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/activitiesDate/{id}', [ActivityController::class,'updateActivityDate'])->where('VerySecureKey', 'abcd');
    //
    //GRUPY
    Route::get('{VerySecureKey}/group', [GroupController::class,'getGroups'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/group/{groupId}', [GroupController::class,'getGroup'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/groupUsers/{groupId}', [GroupController::class,'getUsersGroup'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/groupUser/{userId}', [GroupController::class,'getUserGroup'])->where('VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/group', [GroupController::class,'createGroup'])->where('VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/joinGroup/{groupId}', [GroupController::class,'joinGroup'])->where('VerySecureKey', 'abcd');
    Route::post('{VerySecureKey}/groupUser/{userId}/{groupId}', [GroupController::class,'addUserGroup'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/groupUser/{userId}/{groupId}', [GroupController::class,'deleteUserGroup'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/group/{groupId}', [GroupController::class,'deleteGroup'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/groupUser/{userId}/{groupId}/{role}', [GroupController::class,'updateUserGroup'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/group/{groupId}', [GroupController::class,'updateGroup'])->where('VerySecureKey', 'abcd');
    //
    //POSTY
    Route::post('{VerySecureKey}/post/{groupId}', [PostController::class,'createPost'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/post/{groupId}/{postId}', [PostController::class,'updatePost'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/post/{groupId}/{postId}', [PostController::class,'deletePost'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/post/{groupId}/{postId}', [PostController::class,'getPost'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/posts/{groupId}', [PostController::class,'getPosts'])->where('VerySecureKey', 'abcd');
    //
    //tagi posta
    Route::post('{VerySecureKey}/postTag/{groupId}/{postId}', [PostController::class,'createTag'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/postTag/{groupId}/{postId}/{tagId}', [PostController::class,'deleteTag'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/postTags/{postId}', [PostController::class,'getPostTags'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/tag/{groupId}/{postTagId}', [PostController::class,'getAllPostsWithTags'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/tags/{groupId}', [PostController::class,'getAllTags'])->where('VerySecureKey', 'abcd');
    //

    //pliki posta
    Route::post('{VerySecureKey}/postData/{groupId}/{postId}', [PostController::class,'createPostData'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/postData/{groupId}/{postId}/{postDataId}', [PostController::class,'deletePostData'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/postData/{groupId}/{postId}/{postDataId}', [PostController::class,'updatePostData'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/postData/{groupId}/{postId}/{postDataId}', [PostController::class,'getPostData'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/postData/{groupId}/{postId}', [PostController::class,'getPostDatas'])->where('VerySecureKey', 'abcd');
    //


    //komentarz
    Route::post('{VerySecureKey}/comment/{groupId}/{postId}', [CommentController::class,'createComment'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/comment/{groupId}/{commentId}', [CommentController::class,'updateComment'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/comment/{groupId}/{commentId}', [CommentController::class,'deleteComment'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/comments/{groupId}/{postId}', [CommentController::class,'getComments'])->where('VerySecureKey', 'abcd');
    //

    //pliki komentarz
    Route::post('{VerySecureKey}/commentData/{groupId}/{commentId}', [CommentController::class,'createCommentData'])->where('VerySecureKey', 'abcd');
    Route::delete('VerySecureKey}/commentData/{groupId}/{commentId}/{commentDataId}', [CommentController::class,'deleteCommentData'])->where('VerySecureKey', 'abcd');
    Route::put('{VerySecureKey}/commentData/{groupId}/{commentId}/{commentDataId}', [CommentController::class,'updateCommentData'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/commentData/{groupId}/{commentId}/{commentDataId}', [CommentController::class,'getCommentData'])->where('VerySecureKey', 'abcd');
    Route::get('{VerySecureKey}/commentDatas/{groupId}/{commentId}', [CommentController::class,'getCommentDatas'])->where('VerySecureKey', 'abcd');
    //





});




// Pliki do notatek uzytkownika

//
//Zdjecia uzytkownika

//
