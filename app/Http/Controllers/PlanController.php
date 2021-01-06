<?php

namespace App\Http\Controllers;
use App\Models\Plan;
use Illuminate\Http\Request;
use DB;
use Validator;
use Carbon\Carbon;
class PlanController extends Controller
{
   
    public function createPlan(Request $request) {
        $user=auth()->user();
        $userId=  $user->id;
        $validator = Validator::make($request->all(), [
            'start_date'=>'required',
            'end_date'=>'required|after_or_equal:start_date',
            'name'=>'required|string|min:1',
            'Users_idUser'=>'',
        ]);
        //$constant_values_array=array('Users_idUser'=>$userId);
        if($request->start_date<Carbon::now()){
            $constant_values_array=array('Users_idUser'=>$userId,'activeFlag'=>'0');
        }
        else{
            $constant_values_array=array('Users_idUser'=>$userId,'activeFlag'=>'1');
        }
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }


        $activityDate = Plan::create(array_merge(
            $constant_values_array,
            $validator->validated()
        ));

        return response()->json([
            'message' => 'Dodałeś datę przedmiotu',
            'activity' => $activityDate
        ], 201);
    }
   public function getAllPlans() {
        $user=auth()->user();
        $plans = DB::table('plans')
            ->select('plans.id','plans.name','plans.start_date','plans.end_date','plans.created_at','plans.updated_at','plans.activeFlag')
            ->where('Users_idUser','=',$user->id)
            ->orderBy('start_date')
            ->get()->toJson(JSON_PRETTY_PRINT);

        return response($plans, 200);
    }
/*
    public function getPlan($planId) {
        $user=auth()->user();

        if (Plan::where('id', $planId )->exists()) {
            $planDates = DB::table('plans')
                ->join('activities','activities.Plans_idPlan','=','plans.id')
           //     ->leftjoin('activity_dates','activities.id','=','Activities_idActivities')
               // ->select('activities.id','activities.name','activities.place','activities.created_at','activities.updated_at','activities.colour','activities.description'
               //     ,'activity_dates.id AS activity_dates.id','activity_dates.start_date','activity_dates.end_date','activity_dates.created_at AS activity_dates.created','activity_dates.updated_at AS activity_dates.updated')
              ->select('activities.id','activities.name','activities.place','activities.created_at','activities.updated_at','activities.colour','activities.description','activities.Plans_idPlan')
         //       ->where('plans.Users_idUser','=',$user->id)
                ->where('activities.Plans_idPlan','=',$planId)
               // ->orderBy('activity_dates.start_date')
                ->get()->toJson(JSON_PRETTY_PRINT);

            return response($planDates, 200);
        } else {
            return response()->json([
                "message" => "Nie znaleziono przedmiotu"
            ], 404);
        }

    }
*/
   public function updatePlan(Request $request,$id) {
        $activeFlag=DB::table('plans')
            ->where('id','=', $id)
            ->value('activeFlag');
        if($activeFlag=='1'){
            $startDate=DB::table('plans')
                ->where('id','=', $id)
                ->value('start_date');
            if($startDate<Carbon::now()){
                Plan::where('id',$id )->update(array('activeFlag' =>'0'));
            }
        }
        $plans =Plan::where('id',$id );
        if (Plan::where('id',$id )->exists()) {
            $validator = Validator::make($request->all(), [
                'start_date'=>'',
                'end_date'=>'after_or_equal:start_date',
                'name'=>'string|min:1',
                'activeFlag'=>'int|min:0|max:1'
            ]);
            if($request->start_date<Carbon::now()){
                $constant_values_array=array('activeFlag'=>'0');
            }
            else{
                $constant_values_array=array('activeFlag'=>'1');
            }

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 400);
            }

            $plans->update($request->all());
            return response()->json([
                'message' => 'Udało się zmodyfikować dane.',
                'plan' => $plans
            ], 201);

        } else {
            return response()->json([
                "message" => "Nie znaleziono przedmiotu do zmodyfikowania"
            ], 404);

        }
    }
    public function deletePlan ($id) {

        if(Plan::where('id', $id )->exists()) {
            $activity = Plan::find($id);
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
}
