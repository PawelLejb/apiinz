<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Activity_note;
use App\models\Activity_data;
use DB;
class ActivityNoteController extends Controller

{

    public function getAllActivityNotes($activityId) {
        $activity_notes = DB::table('activity_notes')
            ->leftjoin('activity_datas','activity_notes.id','=','activity_datas.Activity_notes_idActivity_notes')
            ->select('activity_notes.id','activity_notes.title','activity_notes.note','activity_notes.created_at','activity_notes.updated_at'
                ,'activity_datas.id AS activity_data.id','activity_datas.dataName','activity_datas.data AS activity_data.data','activity_datas.created_at AS activity_data.created','activity_datas.updated_at AS activity_data.updated')
            ->where('Activities_idActivities','=',$activityId)

            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($activity_notes, 200);


    }

    public function createActivityNote(Request $request,$activityId) {

        $activity_note = new Activity_note;
        $activity_note->title = $request->title;
        $activity_note->note = $request->note;
        $activity_note->Activities_idActivities = $activityId;
        $activity_note->save();

        return response()->json([
            "message" => "Dodano notatkę"
        ], 201);

    }
    public function getActivityNote($id) {

        if (Activity_note::where('id', $id )->exists()) {

            $activity_note = DB::table('activity_notes')
                ->leftjoin('activity_datas','activity_notes.id','=','activity_datas.Activity_notes_idActivity_notes')
                ->select('activity_notes.id','activity_notes.title','activity_notes.note','activity_notes.created_at','activity_notes.updated_at'
                    ,'activity_datas.id AS activity_data.id','activity_datas.dataName','activity_datas.data AS activity_data.data','activity_datas.created_at AS activity_data.created','activity_datas.updated_at AS activity_data.updated')
                ->where('activity_notes.id','=',$id)

                ->get()->toJson(JSON_PRETTY_PRINT);
            return response($activity_note, 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono notatki"
            ], 404);
        }

    }

    public function updateActivityNote(Request $request, $id) {
        if (Activity_note::where('id',$id )->exists()) {
            $activity_note = Activity_note::find($id);
            $activity_note->title = is_null($request->title) ? $activity_note->title : $request->title;
            $activity_note->note = is_null($request->note) ? $activity_note->note : $request->note;

            $activity_note->save();

            return response()->json([
                "message" => "Zmodyfikowano notatkę pomyślnie"
            ], 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono notatki"
            ], 404);

        }
    }

    public function deleteActivityNote ($id) {
        if(Activity_note::where('id', $id )->exists()) {
            $activity_note = Activity_note::find($id);
            $activity_note->delete();

            return response()->json([
                "message" => "Notatka usunięta"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono notatki"
            ], 404);
        }
    }

    //Pliki do notatek do POPRWAIENIA PAMIETAJ POTEM ZE MUSISZ ZROBIC TEZ WYSWIETLANIE SAMYCH NOTATEK

    public function addActivityNoteData(Request $request,$id) {
        $activity_data = new Activity_data;
        $activity_data->dataName = $request->dataName;
        $activity_data->data = $request->data;
        $activity_data->Activity_notes_idActivity_notes=$id;
        $activity_data->save();

        return response()->json([
            "message" => "Dodano plik do notatki"
        ], 201);

    }
    public function deleteActivityNoteData ($id) {

        if(Activity_data::where('id', $id )->exists()) {
            $activity_data = Activity_data::find($id);
            $activity_data->delete();

            return response()->json([
                "message" => "plik usunięty"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono pliku"
            ], 404);
        }
    }
    public function updateActivityNoteData(Request $request, $id) {
        if (Activity_data::where('id',$id )->exists()) {
            $activity_data = Activity_data::find($id);
            $activity_data->dataName = is_null($request->dataName) ? $activity_data->dataName : $request->dataName;
            $activity_data->data = is_null($request->data) ? $activity_data->data : $request->data;

            $activity_data->save();

            return response()->json([
                "message" => "Zmodyfikowano plik pomyślnie"
            ], 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono pliku"
            ], 404);

        }
    }



}
