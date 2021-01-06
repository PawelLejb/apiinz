<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_note;
use App\Models\User_data;
use DB;
use Validator;
use App\Models\Note_tag;
class UserNoteController extends Controller
{

    public function getAllUserNotes() {
        $user=auth()->user();
       $id=$user->id;

        $user_notes = DB::table('user_notes')
            ->leftjoin('user_datas','user_notes.id','=','user_datas.User_notes_idNotes_user')
            ->select('user_notes.id','user_notes.title','user_notes.note','user_notes.created_at','user_notes.updated_at'
                ,'user_datas.id AS user_data.id','user_datas.dataName','user_datas.data AS user_data.data','user_datas.created_at AS user_data.created','user_datas.updated_at AS user_data.updated')
            ->where('Users_idUser','=',$id)
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($user_notes, 200);


    }

    public function createUserNote(Request $request) {
        $user=auth()->user();
        $userId=  $user->id;
        $user_note = new User_note;
        $user_note->title = $request->title;
        $user_note->note = $request->note;
        $user_note->Users_idUser = $userId;
        $user_note->save();

        return response()->json([
            "message" => "Dodano notatkę"
        ], 201);

    }
    public function getUserNote($id) {
        $user=auth()->user();
         $user->id;
        if (User_note::where('id', $id )->exists()) {
        $user_note = DB::table('user_notes')
            ->join('user_datas','user_notes.id','=','user_datas.User_notes_idNotes_user')
            ->select('user_notes.id','user_notes.title','user_notes.note','user_notes.created_at','user_notes.updated_at'
                ,'user_datas.id','user_datas.dataName','user_datas.data','user_datas.created_at','user_datas.updated_at')
            ->where('user_notes.id','=',$id)
            ->where('Users_idUser','=',$user->id)
            ->get()->toJson(JSON_PRETTY_PRINT);
         return response($user_note, 200);
            } else {
            return response()->json([
                "message" => "Nie znaleziono notatki"
            ], 404);
        }

    }

    public function updateUserNote(Request $request, $id) {
        if (User_note::where('id',$id )->exists()) {
            $user_note = User_note::find($id);
            $user_note->title = is_null($request->title) ? $user_note->title : $request->title;
            $user_note->note = is_null($request->note) ? $user_note->note : $request->note;

            $user_note->save();

            return response()->json([
                "message" => "Zmodyfikowano notatkę pomyślnie"
            ], 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono notatki"
            ], 404);

        }
    }

    public function deleteUserNote ($id) {
        $user=auth()->user();
        $userId=  $user->id;
        if(User_note::where('id', $id )->exists()) {
            $user_note = User_note::find($id);
            $user_note->delete();

            return response()->json([
                "message" => "Notatka usunięta"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono notatki"
            ], 404);
        }
    }

    //Pliki do notatek

    public function addUserNoteData(Request $request) {
        $user=auth()->user();
        $userId=  $user->id;
        $user_data = new User_data;
        $user_data->dataName = $request->dataName;
        $user_data->data = $request->data;
        $user_data->User_notes_idNotes_user=$userId;
        $user_data->save();

        return response()->json([
            "message" => "Dodano plik do notatki"
        ], 201);

    }
    public function deleteUserUserNoteData ($id) {
        $user=auth()->user();
         $user->id;
        if(User_data::where('id', $id )->exists()) {
            $user_data = User_data::find($id);
            $user_data->delete();

            return response()->json([
                "message" => "plik usunięty"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono pliku"
            ], 404);
        }
    }
    public function updateUserNoteData(Request $request, $id) {
        if (User_data::where('id',$id )->exists()) {
            $user_data = User_note::find($id);
            $user_data->dataName = is_null($request->dataName) ? $user_data->dataName : $request->dataName;
            $user_data->data = is_null($request->data) ? $user_data->data : $request->data;

            $user_data->save();

            return response()->json([
                "message" => "Zmodyfikowano plik pomyślnie"
            ], 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono pliku"
            ], 404);

        }
    }
////tagi
    ///
    public function createNoteTag($noteId,Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:1,16',
        ]);
        $constant_values_array = array('Notes_idNote' => $noteId);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $post = Note_tag::create(array_merge(
            $constant_values_array,
            $validator->validated(),

        ));

        return response()->json([
            'message' => 'Utworzono post!',
            'post' => $post
        ], 201);
    }
    public function deleteNoteTag($tagId) {

        if(Note_tag::where('id', $tagId )->exists()) {
            $noteTag = Note_tag::find($tagId);
            $noteTag->delete();

            return response()->json([
                "message" => "Tag usunięty"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono taga"
            ], 404);
        }

    }
    public function getNoteTags($noteId) {
        if(User_note::where('id', $noteId )->exists()) {
            $postTags = DB::table('note_tags')
                ->select('note_tags.id','note_tags.name','note_tags.Notes_idNote','note_tags.updated_at','note_tags.created_at')
                ->where('Notes_idNote','=',$noteId)
                ->orderBy('created_at')
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($postTags, 200);
        }else{
            return response()->json([
                "message" => "Nie znaleziono posta"
            ], 404);
        }
    }
    public function getAllNoteTags() {
        $user=auth()->user();
        $id=$user->id;
        $noteTags = DB::table('note_tags')
            ->join('user_notes','user_notes.id','=','note_tags.Notes_idNote')
            ->select('note_tags.name','note_tags.id','note_tags.created_at')
            ->where('user_notes.Users_idUser','=',$id)
            ->orderBy('note_tags.created_at')
            ->distinct()
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($noteTags, 200);
    }
    public function getAllNotesWithTags($noteTagId) {
        $user=auth()->user();
        $id=$user->id;
        $noteWithTags = DB::table('note_tags')
            ->join('user_notes','user_notes.id','=','note_tags.Notes_idNote')
            ->select('note_tags.id as note_tags.id','note_tags.name as note_tags.name',
                'user_notes.id','user_notes.title','user_notes.note','user_notes.updated_at','user_notes.created_at','user_notes.Users_idUser' )
            ->where('user_notes.Users_idUser','=',$id)
            ->where('note_tags.id','=',$noteTagId)
            ->orderBy('created_at')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($noteWithTags, 200);
    }
    ///
 public function searchNote($term) {

        $search = DB::table('user_notes')
            ->select("*")
            ->where('title','like',$term)
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($search, 200);
    }

}
