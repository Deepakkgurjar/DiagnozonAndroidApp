<?php

namespace App\Http\Controllers\Laboratory;

use App\LaboratoryAppointment;
use App\TimeTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;


class AppointmentController extends Controller
{
    public function bookAppointmentLab(Request $request){
        $user=Auth::user();

        if($user->user_type=='p'){
            $rules=[
                'lab_id'=>'required',
                'service_id'=>'required',
                'date'=>'required',
                'time_id'=>'required',
            ];

            $messages=[
                'lab_id.required'=>'select laboratory ',
                'service_id.required'=>'select services',
                'date.required'=>'select appointment date',
                'time_id.required'=>'choose appointment time'
            ];

            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }
            $totalAmountofSelectedServices=DB::select('SELECT sum(rate) AS total FROM services where id IN('.$request->service_id.')')[0];
//            dd($totalAmountofSelectedServices->total);

            $bookAppointment=new LaboratoryAppointment();
            $bookAppointment->lab_id=$request->lab_id;
            $bookAppointment->patient_id=$user->id;
            $bookAppointment->service_id=$request->service_id;
            $bookAppointment->appointment_time_id=$request->time_id;
            $appointdate= $request->date;
            $appointmentDate=date('Y-m-d',strtotime($appointdate));
            $bookAppointment->appointment_date=$appointmentDate;
            $bookAppointment->service_amount=$totalAmountofSelectedServices->total;
            $bookAppointment->time=time();
            $bookAppointment->save();

            $getLab= User::where('id',$request->lab_id)->first();
            $getTime =TimeTable::where('id',$request->time_id)->first();

            $message='Hello, '.$user->name.'
your appointment with '.$getLab->name.' is scheduled on '.$appointmentDate.', ' .$getTime->start_time.' to '.$getTime->end_time.'';
            $number= $user->phone_no;
            sendMessages($message,$number);

            $message='Hello, '.$getLab->name.'
Your New Appointment 
scheduled with '.$user->name.' on '.$appointmentDate.', ' .$getTime->start_time.' to '.$getTime->end_time.'';
            $number=$getLab->phone_no;


            sendMessages($message,$number);
            $response['return']=true;
            $response['message'] = "Your appointment is book";
            $response['data']=$bookAppointment;
            return response()->json($response, 200);

        }

            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);

    }
}
