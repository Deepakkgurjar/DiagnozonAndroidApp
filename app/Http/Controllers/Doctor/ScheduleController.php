<?php

namespace App\Http\Controllers\Doctor;

use App\Doctor;
use App\DoctorAppointment;
use App\DoctorSchedule;
use App\TimeTable;
use App\User;
use Illuminate\Cache\DynamoDbStore;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Null_;
use PhpParser\Comment\Doc;
use DB;

class ScheduleController extends Controller
{


    public function makeDoctorSchedule(Request $request){
        $user=Auth::user();
        if($user->user_type=='d'){
            $rules=[
                'off_hours_id'=>'required',
//                'first_shift'=>'required',
//                'second_shift'=>'required',
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


//            if(!empty($request->off_hours_id)&&!empty($request->off_dates)){
//                $new_array_hrs = array();
//                $new_array_hrs= explode(',',$request->off_hours_id);
//                $new_array_date = array();
//                $new_array_date= explode(',',$request->off_dates);
//                foreach ($new_array_hrs as $new_hrs){
//                    $offDateInsert=new DoctorSchedule();
//                    $offDateInsert->user_id=$user->id;
//                    $offDateInsert->time_table_id=$new_hrs;
//                    $offDateInsert->save();
//                    echo $offDateInsert;
//                }
//
//
//            }
//            dd('df');


            if(!empty($request->off_dates)){
                $new_array = array();
                $new_array= explode(',',$request->off_dates);
                foreach ($new_array as $new_dates){
                    $closeDate=date('Y-m-d',strtotime($new_dates));
                    $offDateInsert=new DoctorSchedule();
                    $offDateInsert->user_id=$user->id;
                    $offDateInsert->off_dates=$closeDate;
                    $offDateInsert->save();
                }

            }

            if(!empty($request->off_weeks_id)){
                $new_array = array();
                $new_array= explode(',',$request->off_weeks_id);
                foreach ($new_array as $new_weeks){
                    $offDateInsert=new DoctorSchedule();
                    $offDateInsert->user_id=$user->id;
                    $offDateInsert->off_weeks=$new_weeks;
                    $offDateInsert->save();
                }

            }

            if(!empty($request->off_hours_id)){
                $new_array = array();
                $new_array= explode(',',$request->off_hours_id);
                foreach ($new_array as $new_hrs){
                    $offDateInsert=new DoctorSchedule();
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

    }

    public function doctorReSchedule(Request $request){
        $user=Auth::user();
        $DeleteData = [];
        if($user->user_type=='d'){
            if(!empty($request->off_dates)){

              $date= $request->off_dates;

                    $removeDate=date('Y-m-d',strtotime($date));

                $DeleteData= DoctorSchedule::where('user_id',$user->id)->where('off_dates',$removeDate)->first();
                // dd($DeleteData);
                if(is_null($DeleteData)){
                    $response['message'] =  'No data found';
                    $response["return"] = false;
                    return response()->json($response, 400);
                }
                $DeleteData->delete();
            }

            if(!empty($request->off_weeks_id)){
                $removeWeek= $request->off_weeks_id;

                $DeleteData= DoctorSchedule::where('user_id',$user->id)->where('off_weeks',$removeWeek)->first();
                if(is_null($DeleteData)){
                    $response['message'] =  'No data found';
                    $response["return"] = false;
                    return response()->json($response, 400);
                }
                $DeleteData->delete();

            }

            if(!empty($request->off_hours_id)){
                $removehour= $request->off_hours_id;

                $DeleteData= DoctorSchedule::where('user_id',$user->id)->where('time_table_id',$removehour)->first();

                $DeleteData->delete();
            }

            $response['return']=true;
            $response['message']='Sucessfully reschedule your profile';
            $response['data']=$DeleteData;
            return response()->json($response, 200);


        }

        $response['return']=false;
        $response['message'] = "Something went wrong";
        $response['errors'] = "";
        $response['errors_key'] = "";
        return response()->json($response, 400);

    }

    public function slotDistrubution(Request $request){
        $user=Auth::user();
        if($user->user_type=='p'){
            $rules=[
                'doctor_id'=>'required',
                'service_id'=>'required',
                'date'=>'required',

            ];

            $messages=[
                'doctor_id.required'=>'select doctor ',
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
//
//            $totalpatient=DB::select('SELECT COUNT( appointment_time_id) AS ap_count From doctor_appointments WHERE doctor_id = '.$request->doctor_id.'');
//
//          $doctroSchedule=DB::select('SELECT * FROM time_table
//WHERE id NOT IN (SELECT time_table_id FROM doctor_schedule WHERE time_table_id is NOT null)');
            $toDay=time();
            $toDay=date('Y-m-d',$toDay);
            // dd($request->date);

            if($request->date <= $toDay){
                $response['return']=false;
                $response['message']='Date should be greater then to day.';
                return response()->json($response, 400);
            }


            $select_slot=DB::select('SELECT t.id,t.start_time,t.end_time, COUNT(da.appointment_time_id) AS app FROM time_table AS t LEFT JOIN doctor_appointments AS da ON t.id=da.appointment_time_id AND da.doctor_id='.$request->doctor_id.' AND da.appointment_date='.$request->date.' GROUP BY t.id HAVING app < 30 AND t.id NOT IN (SELECT ds.time_table_id FROM doctor_schedule AS ds WHERE ds.time_table_id is NOT null AND ds.user_id ='.$request->doctor_id.')');

            $response['return']=true;
            $response['message']='Choose time slot';
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
