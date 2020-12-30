<?php

namespace App\Http\Controllers;
use App\Models\Activity;
use App\Models\Activity_date;
use DB;
use Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
class ActivityController extends Controller
{
    public function createActivity(Request $request, $planId)
    {


        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'category' => 'required|string|between:2,100',
            'place' => 'required|string|min:1',
            'colour' => 'required',
            'description' => 'required|string|min:2',

        ]);
        $constant_values_array = array('Plans_idPlan' => $planId);
        $validator->Plans_idPlan = $planId;


        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $activity = Activity::create(array_merge(
            $constant_values_array,
            $validator->validated(),

        ));

        return response()->json([
            'message' => 'Dodałeś przedmiot',

            'activity' => $activity
        ], 201);

    }

    public function getAllActivities($planId)
    {


        $activities = DB::table('activities')
            ->leftjoin('activity_dates', 'activities.id', '=', 'Activities_idActivities')
            ->select('activities.id', 'activities.name', 'activities.place', 'activities.created_at',
                'activities.updated_at', 'activities.colour', 'activities.description', 'activities.Plans_idPlan', 'activity_dates.id AS activity_dates.id', 'activity_dates.start_date', 'activity_dates.end_date',
           'activity_dates.periodicity',   'activity_dates.created_at AS activity_dates.created', 'activity_dates.updated_at AS activity_dates.updated','activity_dates.periodicityDatesId AS periodicityDatesId')
            ->where('Plans_idPlan', '=', $planId)
            ->orderBy('start_date')
            ->get()->toJson(JSON_PRETTY_PRINT);
        return response($activities, 200);
    }

    public function getActivity($id, $planId)
    {


        if (Activity::where('id', $id)->exists()) {
            $activity_dates = DB::table('activities')
                ->leftjoin('activity_dates', 'activities.id', '=', 'Activities_idActivities')
                ->select('activities.id', 'activities.name', 'activities.place', 'activities.created_at', 'activities.updated_at', 'activities.colour', 'activities.description', 'activities.Plans_idPlan', 'activity_dates.periodicity'
                    , 'activity_dates.id AS activity_dates.id', 'activity_dates.start_date', 'activity_dates.end_date', 'activity_dates.created_at AS activity_dates.created', 'activity_dates.updated_at AS activity_dates.updated','activity_dates.periodicityDatesId AS periodicityDatesId')
                ->where('Plans_idPlan', '=', $planId)
                ->where('activities.id', '=', $id)
                ->orderBy('start_date')
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($activity_dates, 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono przedmiotu"
            ], 404);
        }

    }

    public function updateActivity(Request $request, $id)
    {

        $activity = Activity::where('id', $id);
        if (Activity::where('id', $id)->exists()) {
            $validator = Validator::make($request->all(), [
                'name' => 'string|between:2,100',
                'category' => 'string|between:2,100',
                'place' => 'string|min:1',
                'colour' => '',
                'description' => 'string|min:2',


            ]);


            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $activity->update($request->all());
            return response()->json([
                'message' => 'Udało się zmodyfikować dane.',
                'activity' => $activity
            ], 201);

        } else {
            return response()->json([
                "message" => "Nie znaleziono przedmiotu do zmodyfikowania"
            ], 404);

        }
    }

    public function deleteActivity($id)
    {

        if (Activity::where('id', $id)->exists()) {
            $activity = Activity::find($id);
            $activity->delete();

            return response()->json([
                "message" => "Przedmiot usunięty"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono przedmiotu"
            ], 404);
        }
    }


    //daty przedmiotu


    public function createActivityDate(Request $request,$planId,$activityId)
    {
        $user=auth()->user();
        $endPlanDate=DB::table('activities')
            ->join('plans','plans.id','=','activities.Plans_idPlan')
           ->where('plans.Users_idUser','=',$user->id)
            ->where('activities.Plans_idPlan','=',$planId)
            ->value('plans.end_date');
        $startPlanDate=DB::table('activities')
            ->join('plans','plans.id','=','activities.Plans_idPlan')
            ->where('plans.Users_idUser','=',$user->id)
            ->where('activities.Plans_idPlan','=',$planId)
            ->value('plans.start_date');
         $periodicityDatesId = DB::table('activity_dates')->max('periodicityDatesId');
        $periodicityDatesId=$periodicityDatesId+1;
       // return $periodicityDatesId;
        $endPlanDate = Carbon::createFromFormat('Y-m-d H:i',$endPlanDate.' 00:00')->addDays(1);
        $startPlanDate = Carbon::createFromFormat('Y-m-d H:i',$startPlanDate.' 00:00')->addDays(-1);

            $validator = Validator::make($request->all(), [
                'start_date' => 'required|before_or_equal:'.$endPlanDate.'|after_or_equal:'.$startPlanDate,
                'end_date' => 'required|after_or_equal:start_date|before_or_equal:'.$endPlanDate,
                'periodicity' => 'required|min:0|max:1',
                'Activities_idActivities' => '',
                'periodicityDatesId' => '',
            ]);
        $request->start_date=$request->start_date = Carbon::createFromFormat('Y-m-d H:i',$request->start_date);
        $request->end_date=$request->end_date = Carbon::createFromFormat('Y-m-d H:i',$request->end_date);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }
            if($request->periodicity==1){
            while ($endPlanDate->greaterThan($request->end_date)) {
                $constant_values_array = array('Activities_idActivities' => $activityId
                , 'periodicityDatesId' => ''
                , 'start_date' => $request->start_date
                , 'end_date' => $request->end_date
           
                , 'periodicity' => $request->periodicity);
                $activityDate = Activity_date::create(array_merge(
                    $constant_values_array,
                ));
                $request->start_date = $request->start_date->addDays(7);
                $request->end_date = $request->end_date->addDays(7);

            }
                return response()->json([
                    'message' => 'Dodałeś datę przedmiotu',
                    'activity' => $activityDate
                ], 201);
            }
        $constant_values_array = array('Activities_idActivities' => $activityId 
        , 'periodicityDatesId' => ''
        , 'start_date' => $request->start_date
        , 'end_date' => $request->end_date
        , 'periodicity' => $request->periodicity);
        $activityDate = Activity_date::create(array_merge(
            $constant_values_array,
        ));
    return response()->json([
        'message' => 'Dodałeś datę przedmiotu',
        'activity' => $activityDate
    ], 201);
}

    public function deleteActivityDate($id)
    {
        if (Activity_date::where('id', $id)->exists()) {
            $activity_date = Activity_date::find($id);
            $activity_date->delete();

            return response()->json([
                "message" => "usunięto datę"
            ], 202);
        } else {
            return response()->json([
                "message" => "Nie znaleziono daty"
            ], 404);
        }
    }

    public function updateActivityDate(Request $request, $id)
    {
        $activity_date = Activity_date::where('id', $id);
        if (Activity_date::where('id', $id)->exists()) {
            $validator = Validator::make($request->all(), [
                'start_date' => '',
                'end_date' => 'after_or_equal:start_date',


            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $activity_date->update($request->all());
            return response()->json([
                'message' => 'Udało się zmodyfikować dane.',
                'event_date' => $activity_date
            ], 201);

        } else {
            return response()->json([
                "message" => "Nie znaleziono daty"
            ], 404);

        }
    }

}
