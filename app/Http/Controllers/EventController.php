<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\Event;
use App\models\Event_date;
use DB;
use Validator;
class EventController extends Controller
{
    public function createEvent(Request $request) {
        $user=auth()->user();
        $id=$user->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'place' => 'required|string|between:2,100',
            'description' => 'required|string|min:1',
            'colour'=> 'required',
            'category'=> 'required|string|min:2',
            'Users_idUser'=>'',


        ]);

        $constant_values_array=array('Users_idUser'=>$id);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $event = Event::create(array_merge(
            $constant_values_array,
            $validator->validated(),

        ));

        return response()->json([
            'message' => 'Dodałeś wydarzenie',

            'event' => $event
        ], 201);

    }

    public function getAllEvents() {
        $user=auth()->user();
        $id=$user->id;

        $user_events = DB::table('events')
            ->leftjoin('event_dates','events.id','=','Events_idEvents')
            ->select('events.id','events.name','events.place','events.created_at','events.updated_at','events.colour','events.description','events.category','events.Users_idUser'
                ,'event_dates.id AS event_date.id','event_dates.start_date','event_dates.end_date','event_dates.created_at AS event_date.created','event_dates.updated_at AS event_date.updated')
            ->where('Users_idUser','=',$id)
            ->orderBy('start_date')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($user_events, 200);


    }
    public function getEvent($id) {
        $user=auth()->user();

        if (Event::where('id', $id )->exists()) {
            $user_events = DB::table('events')
                ->leftjoin('event_dates','events.id','=','Events_idEvents')
                ->select('events.id','events.name','events.place','events.created_at','events.updated_at','events.colour','events.description','events.category','events.Users_idUser'
                    ,'event_dates.id AS event_date.id','event_dates.start_date','event_dates.end_date','event_dates.created_at AS event_date.created','event_dates.updated_at AS event_date.updated')
                ->where('Users_idUser','=',$user->id)
                ->where('events.id','=',$id)
                ->orderBy('start_date')
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($user_events, 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono wydarzenia"
            ], 404);
        }

    }
    public function updateEvent(Request $request,$id) {

        $event =Event::where('id',$id );
        if (Event::where('id',$id )->exists()) {
            $validator = Validator::make($request->all(), [
                'name' => 'string|between:2,100',
                'place' => 'string|between:2,100',
                'description' => 'string|min:1',
                'colour' => '',
                'category' => 'string|min:2',
                'Users_idUser' => '',


            ]);


            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $event->update($request->all());
            return response()->json([
                'message' => 'Udało się zmodyfikować dane.',
                'event' => $event
            ], 201);

        } else {
            return response()->json([
                "message" => "Nie znaleziono notatki"
            ], 404);

        }
    }
    public function deleteEvent ($id) {

        if(Event::where('id', $id )->exists()) {
            $event = Event::find($id);
            $event->delete();

            return response()->json([
                "message" => "Notatka usunięta"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono notatki"
            ], 404);
        }
    }
//dni wydarzeń
    public function createEventDate(Request $request,$id) {
    $validator = Validator::make($request->all(), [
        'start_date'=>'required',
        'end_date'=>'required|after_or_equal:start_date',
        'Events_idEvents'=>'',
    ]);
        $constant_values_array=array('Events_idEvents'=>$id);
    if($validator->fails()){
        return response()->json($validator->errors()->toJson(), 400);
    }

    $eventDate = Event_date::create(array_merge(
        $constant_values_array,
        $validator->validated()
    ));

    return response()->json([
        'message' => 'Dodałeś datę wydarzenia',
        'event' => $eventDate
    ], 201);
}
    public function deleteEventDate ($id) {
        if(Event_date::where('id', $id )->exists()) {
            $event_date = Event_date::find($id);
            $event_date->delete();

            return response()->json([
                "message" => "usunięto datę"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono daty"
            ], 404);
        }
    }
    public function updateEventDate(Request $request,$id) {
        $event_date =Event_date::where('id',$id );
        if (Event_date::where('id',$id )->exists()) {
            $validator = Validator::make($request->all(), [
                'start_date'=>'',
                'end_date'=>'after_or_equal:start_date',



            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $event_date->update($request->all());
            return response()->json([
                'message' => 'Udało się zmodyfikować dane.',
                'event_date' => $event_date
            ], 201);

        } else {
            return response()->json([
                "message" => "Nie znaleziono daty"
            ], 404);

        }
}
}
