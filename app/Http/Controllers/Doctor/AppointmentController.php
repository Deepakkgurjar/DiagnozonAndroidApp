<?php

namespace App\Http\Controllers\Doctor;

use App\Doctor;
use App\DoctorAppointment;
use App\PatientBooking;
use App\Service;
use App\TimeTable;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
   public function bookAppointment(Request $request){
       $user=Auth::user();

       if($user->user_type=='p'){
           $rules=[
               'doctor_id'=>'required',
               'service_id'=>'required',
               'date'=>'required',
               'time_id'=>'required',
           ];

           $messages=[
               'doctor_id.required'=>'select doctor ',
               'service_id.required'=>'select services',
               'date.required'=>'select appointment date',
               'time_id.required'=>'choose appointment time id'
           ];

           $validator=Validator::make($request->all(),$rules,$messages);
           if($validator->fails()){
               $response['return'] = false;
               $response['errors'] = $validator->errors()->toArray();
               $response['errors_key'] = array_keys($validator->errors()->toArray());
               return response()->json($response, 400);
           }

//
//           $doctorFreeTime= DB::select('SELECT doctors.shift_first_start_time AS  fsst ,doctors.shift_first_end_time AS fset ,doctors.shift_second_start_time AS ssst,doctors.shift_second_end_time AS sset FROM `doctors`LEFT JOIN services ON doctors.user_id=services.user_id WHERE doctors.user_id ='.$request->doctor_id.' AND services.id='.$request->service_id.' limit 1')[0];

           $totalAmountofSelectedServices=DB::select('SELECT sum(rate) AS total FROM services where id IN('.$request->service_id.')')[0];

           $bookAppointment=new DoctorAppointment();
           $bookAppointment->doctor_id=$request->doctor_id;
           $bookAppointment->patient_id=$user->id;
           $bookAppointment->service_id=$request->service_id;
           $bookAppointment->appointment_time_id=$request->time_id;
           $appointdate= $request->date;
           $appointmentDate=date('Y-m-d',strtotime($appointdate));
           $bookAppointment->appointment_date=$appointmentDate;
           $bookAppointment->service_amount=$totalAmountofSelectedServices->total;
           $bookAppointment->time=time();
           $bookAppointment->save();

           $getDoctor= User::where('id',$request->doctor_id)->first();
           $getTime =TimeTable::where('id',$request->time_id)->first();

            // for Patient
           
           $message='Hello, '.$user->name.'
your appointment with '.$getDoctor->name.' is scheduled on '.$appointmentDate.', ' .$getTime->start_time.' to '.$getTime->end_time.'';
               $number= $user->phone_no;


           sendMessages($message,$number);


           // for Doctor 

           $message='Hello, '.$getDoctor->name.'
Your New Appointment 
scheduled with '.$user->name.' on '.$appointmentDate.', ' .$getTime->start_time.' to '.$getTime->end_time.'';
           $number=$getDoctor->phone_no;


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
