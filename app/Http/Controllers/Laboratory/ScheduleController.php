<?php

namespace App\Http\Controllers\Laboratory;

use App\LaboratorySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
   public function makeLabSchedule(Request $request){
       $user=Auth::user();
       if($user->user_type=='l'){
           $rules=[
               'off_hours_id'=>'required',
//               'off_dates'=>'required',
//               'off_weeks_id'=>'required'
           ];
           $message=[
               'off_hours_id.required'=>'Select non-sitting hours'
           ];

           $validator= Validator::make($request->all(),$rules,$message);
           if($validator->fails()){
               $response=false;
               $response['errors']=$validator->errors()->toArray();
               $response['errors_key']=array_keys($validator->errors()->toArray());
               return response()->json($response, 400);
           }


           if(!empty($request->off_dates)){
               $new_array = array();
               $new_array= explode(',',$request->off_dates);
               foreach ($new_array as $new_dates){
                   $closeDate=date('Y-m-d',strtotime($new_dates));
                   $offDateInsert=new LaboratorySchedule();
                   $offDateInsert->user_id=$user->id;
                   $offDateInsert->off_dates=$closeDate;
                   $offDateInsert->save();
               }

           }

           if(!empty($request->off_weeks_id)){
               $new_array = array();
               $new_array= explode(',',$request->off_weeks_id);
               foreach ($new_array as $new_weeks){
                   $offDateInsert=new LaboratorySchedule();
                   $offDateInsert->user_id=$user->id;
                   $offDateInsert->off_weeks_id=$new_weeks;
                   $offDateInsert->save();
               }

           }

           if(!empty($request->off_hours_id)){
               $new_array = array();
               $new_array= explode(',',$request->off_hours_id);
               foreach ($new_array as $new_hrs){
                   $offDateInsert=new LaboratorySchedule();
                   $offDateInsert->user_id=$user->id;
                   $offDateInsert->time_table_id=$new_hrs;
                   $offDateInsert->save();
               }

           }

           $response['return']=true;
           $response['message']='Sucessfully update your schedule';
           $response['data']=$offDateInsert;
           return response()->json($response, 200);


       }

            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);

   }

    public function labReSchedule(Request $request){
        $user=Auth::user();
        if($user->user_type=='l'){
            if(!empty($request->off_dates)){

                $date= $request->off_dates;

                $removeDate=date('Y-m-d',strtotime($date));

                $DeleteData= LaboratorySchedule::where('user_id',$user->id)->where('off_dates',$removeDate)->first();

                $DeleteData->delete();
            }

            if(!empty($request->off_weeks_id)){
                $removeWeek= $request->off_weeks_id;

                $DeleteData= LaboratorySchedule::where('user_id',$user->id)->where('off_weeks_id',$removeWeek)->first();

                $DeleteData->delete();

            }

            if(!empty($request->off_hours_id)){
                $removehour= $request->off_hours_id;

                $DeleteData= LaboratorySchedule::where('user_id',$user->id)->where('time_table_id',$removehour)->first();

                $DeleteData->delete();
            }

            $response['return']=true;
            $response['message']='Sucessfully reschedule your profile';
            $response['data']=$DeleteData;
            return response()->json($response, 200);


        }
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);

    }

    public function slotDistrubution(Request $request){
        $user=Auth::user();
        if($user->user_type=='p'){
            $rules=[
                'lab_id'=>'required',
                'service_id'=>'required',
                'date'=>'required',

            ];

            $messages=[
                'lab_id.required'=>'select Laboratory ',
                'service_id.required'=>'select services',
                'date.required'=>'select appointment date',

            ];

            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }
            $toDay=time();
            $toDay=date('Y-m-d',$toDay);

            // dd($request->date);

            if($request->date <= $toDay){
              $response['return']=false;
              $response['message']='Date should be greater then to day.';
              $response['errors']="";
              $response['errors_key']="";
              return response()->json($response, 400);
              
            }

            $select_slot=DB::select('SELECT t.id,t.start_time,t.end_time, COUNT(da.appointment_time_id) AS app FROM time_table AS t LEFT JOIN laboratory_appointments AS da ON t.id=da.appointment_time_id AND da.lab_id='.$request->lab_id.' AND da.appointment_date='.$request->date.' GROUP BY t.id HAVING app < 30 AND t.id NOT IN (SELECT ds.time_table_id FROM laboratory_schedule AS ds WHERE ds.time_table_id is NOT null AND ds.user_id ='.$request->lab_id.')');

            $response['return']=true;
            $response['message']='Choose time slot id';
            $response['data']=$select_slot;
            return response()->json($response, 200);

        }
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);


    }
}
