<?php

namespace App\Http\Controllers\Doctor;

use App\DoctorAppointment;
use App\LaboratoryAppointment;
use App\PatientPrescription;
use App\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Validator;
use App\ClinicTypes;
use App\DocHigestQualification;
use App\DocSpecilization;

class DoctorController extends Controller
{
    public function upcomingAppointment(Request $request){

        $user=Auth::user();
        


        if($user->user_type=='d'){


            $toDay= time();
            $toDay =date('Y-m-d',$toDay);
            $ok=$request->date;

            $ok=date('Y-m-d',strtotime($ok));


            if(empty($request->date)){
                $ok =$toDay;
            }

            $serId=DB::select('SELECT doctor_appointments.id,doctor_appointments.doctor_id,doctor_appointments.patient_id,doctor_appointments.service_id,doctor_appointments.appointment_time_id,doctor_appointments.appointment_date,doctor_appointments.service_amount,doctor_appointments.cancel,doctor_appointments.by_whom,doctor_appointments.status,users.name,users.email,users.phone_no,patients.d_o_b,patients.gender,patients.profile_pic,services.pick_up,services.service_time,services.in_package,time_table.start_time,time_table.end_time FROM `doctor_appointments`LEFT JOIN services ON services.id IN ( doctor_appointments.service_id) LEFT  JOIN  users ON  users.id IN (doctor_appointments.patient_id) LEFT JOIN patients ON patients.user_id IN (doctor_appointments.patient_id) LEFT  JOIN  time_table ON  time_table.id IN (doctor_appointments.appointment_time_id)WHERE doctor_id='.$user->id.' AND appointment_date =  "'.$ok.'" ORDER BY doctor_appointments.appointment_time_id ');




            foreach ($serId as $service){
                $service->services = Service::whereIn('id',explode(",",$service->service_id))->get();
            }



            if(count($serId) > 0){

                $response['return']=true;
                $response['message'] = "Upcoming appointments";
                $response['data']=$serId;
                return response()->json($response, 200);
                
            }

                $response['return']=true;
                $response['message'] = "No upcoming appointments";
                $response['data']=$serId;
                return response()->json($response, 200);
        }

            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
    }

    public function toDayAppointments(Request $request){
        $user=Auth::user();
        // dd($user);
        if($user->user_type=='d'){
            $dataAll = [];
            $serId=DB::select('SELECT concat(time_table.start_time,"-",time_table.end_time) AS timeUnit, doctor_appointments.id,doctor_appointments.doctor_id,doctor_appointments.patient_id,doctor_appointments.service_id,doctor_appointments.appointment_time_id,doctor_appointments.appointment_date,doctor_appointments.service_amount,doctor_appointments.cancel,doctor_appointments.by_whom,doctor_appointments.status,users.name,users.email,users.phone_no,patients.d_o_b,patients.gender,patients.profile_pic,services.pick_up,services.service_time,services.in_package,time_table.start_time,time_table.end_time FROM `doctor_appointments`LEFT JOIN services ON services.id IN ( doctor_appointments.service_id) LEFT  JOIN  users ON  users.id IN (doctor_appointments.patient_id) LEFT JOIN patients ON patients.user_id IN (doctor_appointments.patient_id) LEFT  JOIN  time_table ON  time_table.id IN (doctor_appointments.appointment_time_id)WHERE doctor_id='.$user->id.' AND appointment_date = CURRENT_DATE ORDER BY doctor_appointments.appointment_time_id');

            foreach ($serId as $service){
                $service->services = Service::whereIn('id',explode(",",$service->service_id))->get();
                if(!isset($dataAll[$service->timeUnit])){
                	$dataAll[$service->timeUnit] = [];
                }
                array_push($dataAll[$service->timeUnit], $service);
            }
            // $data['hello'] = [];
            if(count($dataAll) > 0){

                $response['return']=true;
                $response['message'] = "today's appointments";
                $response['data']=$dataAll;
                return response()->json($response, 200);
                
            }
                $response['return']=false;
                $response['message'] = "No appointments";
                $response['data']=$dataAll;
                return response()->json($response, 200);
                
            
        }
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
    }

    public function patientRecords(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'd') {

            $serId = DB::select('SELECT doctor_appointments.id,doctor_appointments.doctor_id,doctor_appointments.patient_id,doctor_appointments.service_id,doctor_appointments.appointment_time_id,doctor_appointments.appointment_date,doctor_appointments.service_amount,users.name,users.email,users.phone_no,patients.d_o_b,patients.gender,patients.blood_group,patients.d_o_b,patients.profile_pic,services.pick_up,services.service_time,services.in_package,time_table.start_time,time_table.end_time FROM `doctor_appointments`LEFT JOIN services ON services.id IN ( doctor_appointments.service_id) LEFT  JOIN  users ON  users.id IN (doctor_appointments.patient_id) LEFT JOIN patients ON patients.user_id IN (doctor_appointments.patient_id) LEFT  JOIN  time_table ON  time_table.id IN (doctor_appointments.appointment_time_id)WHERE doctor_id=' . $user->id . ' ORDER BY doctor_appointments.id desc');


            foreach ($serId as $service) {
                $service->services = Service::whereIn('id', explode(",", $service->service_id))->get();

            }


            if (count($serId) > 0) {

                $response['return'] = true;
                $response['message'] = "All Patients";
                $response['data'] = $serId;
                return response()->json($response, 200);
                
            }

                $response['return'] = true;
                $response['message'] = "No Patient Records Found";
                $response['data'] = $serId;
                return response()->json($response, 200);
        }


        if ($user->user_type == 'l') {

            $serId = DB::select('SELECT laboratory_appointments.id,laboratory_appointments.lab_id,laboratory_appointments.patient_id,laboratory_appointments.service_id,laboratory_appointments.appointment_time_id,laboratory_appointments.appointment_date,users.name,users.email,users.phone_no,patients.gender,patients.blood_group,patients.d_o_b,patients.profile_pic,services.pick_up,services.in_package,time_table.start_time,time_table.end_time FROM `laboratory_appointments`LEFT JOIN services ON services.id IN ( laboratory_appointments.service_id) LEFT  JOIN  users ON  users.id IN (laboratory_appointments.patient_id) LEFT JOIN patients ON patients.user_id IN (laboratory_appointments.patient_id) LEFT  JOIN  time_table ON  time_table.id IN (laboratory_appointments.appointment_time_id)WHERE lab_id=' . $user->id . ' ORDER BY laboratory_appointments.id desc');


            foreach ($serId as $service) {
                $service->services = Service::whereIn('id', explode(",", $service->service_id))->get();
//                $service->prescription = PatientPrescription::whereIn('user_id',explode(",",$service->patient_id))->get();
            }

            if (count($serId) > 0) {
                $response['return'] = true;
                $response['message'] = "All Patients";
                $response['data'] = $serId;
                return response()->json($response, 200);
                
            }
                $response['return'] = true;
                $response['message'] = "No Records Found";
                $response['data'] = $serId;
                return response()->json($response, 200);

        }

        if ($user->user_type == 'p') {
                    
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);


                }
    }

    public function viewPrescription(Request $request){

            $user=Auth::user();
            if($user->user_type=='d'){
                $rule=[
                    'patient_id'=>'required',
                ];
                $message=[

                    'patient_id.required'=>'select patient'
                ];

                $validator=Validator::make($request->all(),$rule,$message);

                if($validator->fails()){
                    $response['return']=false;
                    $response['errors']=$validator->errors()->toArray();
                    $response['errors_key']=array_keys($validator->errors()->toArray());
                    return response()->json($response,400);
                }

                $prescriptions = PatientPrescription::orderBy('id','desc')->where('user_id',$request->patient_id)->get();


                if(count($prescriptions) > 0){
                    $response['return']=true;
                    $response['message'] = "All Prescriptions";
                    $response['data']=$prescriptions;
                    return response()->json($response, 200);
                    $response['error'] = "No Records Found";
                    return response()->json($response, 400);
                }
                    $response['return']=true;
                    $response['message'] = "No Prescriptions Found";
                    $response['data']=$prescriptions;
                    return response()->json($response, 200);

            }
                    $response['return']=false;
                    $response['message']='Something went wrong';
                    $response['errors']="";
                    $response['errors_key']="";
                    return response()->json($response, 400);
    }

    public function uploadPrescriptionbyDoctor(Request $request)
    {
        $user = Auth::user();
        if ($user->user_type == 'd') {
            $rule = [
                'patient_id' => 'required',
                'prescription_title'=>'required',
                'prescription_desc'=>'required',
                'prescription_img'=>'required'

            ];
            $message = [

                'patient_id.required' => 'select patient',
                'prescription_title.required'=>'prescription title required',
                'prescription_desc.required'=>'prescription description required',
                'prescription_img.required'=>'prescription document required'
            ];

            $validator = Validator::make($request->all(), $rule, $message);

            if ($validator->fails()) {
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $patientPrescription=new PatientPrescription();
            $patientPrescription->user_id=$request->patient_id;
            $patientPrescription->prescription_title=$request->prescription_title;
            $patientPrescription->prescription_desc=$request->prescription_desc;


            if(!empty($request->prescription_img)){
                $file = $request->file('prescription_img');
                $name = time().'.'.$file->getClientOriginalExtension();
                $file->move(public_path().'/storage/images/patientPrescriptions/',$name);
                $path_patient_prescri = '/storage/images/patientPrescriptions/'.$name;
                $patientPrescription->prescription_img = $path_patient_prescri;
            }
            $patientPrescription->by_whom=$user->name;
            $patientPrescription->time=time();
            $patientPrescription->save();

            $response['return']=true;
            $response['message']='Prescription upload sucessfully';
            $response['data']=$patientPrescription;
            return response()->json($response, 200);
        }
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
    }

    public function appointmentDone(Request $request)
    {

        $user = Auth::user();
        if ($user->user_type == 'd') {
            $rule = [
                'appointment_id' => 'required|exists:doctor_appointments,id',
            ];
            $message = [

                'appointment_id.required' => 'select patient',

            ];

            $validator = Validator::make($request->all(), $rule, $message);

            if ($validator->fails()) {
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $getAppointment=DoctorAppointment::where('id',$request->appointment_id)->where('doctor_id',$user->id)->first();
            // dd($getAppointment);

            if(empty($getAppointment)){
                $response['return']=false;
                $response['message']='Appoint not found';
                return response()->json($response, 400);
            }

            if($getAppointment->status=='c'){
                $getAppointment->status='n';
                $getAppointment->save();
                $response['return']=true;
                $response['message']='Not Done';
                $response['data']=$getAppointment;
                return response()->json($response, 200);
            }
            $getAppointment->status='c';
            $getAppointment->save();
            $response['return']=true;
            $response['message']='Done';
            $response['data']=$getAppointment;
            return response()->json($response, 200);


        }

        if ($user->user_type == 'l') {
            $rule = [
                'appointment_id' => 'required|exists:laboratory_appointments,id',
            ];
            $message = [

                'appointment_id.required' => 'select patient',

            ];

            $validator = Validator::make($request->all(), $rule, $message);

            if ($validator->fails()) {
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $getAppointment=LaboratoryAppointment::where('id',$request->appointment_id)->where('lab_id',$user->id)->first();

            if(empty($getAppointment)){
                $response['return']=false;
                $response['message']='Appoint not found';
                return response()->json($response, 400);
            }

            if($getAppointment->status =='c'){
                $getAppointment->status='n';
                $getAppointment->save();
                $response['return']=true;
                $response['message']='Not Done';
                $response['data']=$getAppointment;
                return response()->json($response, 200);
            }
            $getAppointment->status='c';
            $getAppointment->save();
            $response['return']=true;
            $response['message']='Done';
            $response['data']=$getAppointment;
            return response()->json($response, 200);


        }

            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);


    }

    public function clinicTypes(Request $request)
    {
        $rule = [
                'user_type' => 'required',
            ];
            $message = [

                'user_type.required' => 'select user_type',

            ];

            $validator = Validator::make($request->all(), $rule, $message);

            if ($validator->fails()) {
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }else{

                if($request->user_type=='d'){
                    $allClinicTypes= ClinicTypes::orderBy('id','desc')->get();

                $response['return']=true;
                $response['message'] = "Clinic List";
                $response['data']=$allClinicTypes;
                return response()->json($response, 200);
            }else{
                $response['return']=false;
                $response['message']='Something went wrong';
                $response['errors']="";
                $response['errors_key']="";
                return response()->json($response, 400);
            }

                

            }
            
        
    }

    public function higestQualification(Request $request)
    {
        $rule = [
                'user_type' => 'required',
                'clinic_type_id'=>'required',
            ];
            $message = [

                'user_type.required' => 'select user_type',
                'clinic_type_id.required'=>'select clinic type',

            ];

            $validator = Validator::make($request->all(), $rule, $message);

            if ($validator->fails()) {
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }else{

                if($request->user_type =='d'){
                    $getAllQualification= DocHigestQualification::orderby('id','desc')->where('clinic_type_id',$request->clinic_type_id)->get();
                   $response['return']=true;
                    $response['message'] = "Qualification List";
                    $response['data']=$getAllQualification;
                    return response()->json($response, 200);
                }else{
                    $response['return']=false;
                    $response['message']='Something went wrong';
                    $response['errors']="";
                    $response['errors_key']="";
                    return response()->json($response, 400);

                }

                


            }
    }

    public function specilizations(Request $request)
    {
        $rule = [
                'user_type' => 'required',
                
            ];
            $message = [

                'user_type.required' => 'select user_type',
                
            ];

            $validator = Validator::make($request->all(), $rule, $message);

            if ($validator->fails()) {
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }else{

                if($request->user_type =='d'){

                    $allSpecilizations= DocSpecilization::where('type','d')->orderby('id','desc')->get();
                    $response['return']=true;
                    $response['message'] = "All specilizations";
                    $response['data']=$allSpecilizations;
                    return response()->json($response, 200);
                }
                
                if($request->user_type =='l'){

                    $allSpecilizations= DocSpecilization::where('type','l')->orderby('id','desc')->get();
                    
                    $response['return']=true;
                    $response['message'] = "All specilizations";
                    $response['data']=$allSpecilizations;
                    return response()->json($response, 200);
                }
                    $response['return']=true;
                    $response['message'] = "No Specilizations";
                    $response['data']=$allSpecilizations=[];
                    return response()->json($response, 200);
                
            }
    }


}


