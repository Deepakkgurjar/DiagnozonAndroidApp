<?php

namespace App\Http\Controllers\Laboratory;

use App\LaboratoryAppointment;
use App\PatientReport;
use App\Service;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Validator;

class LaboratoryController extends Controller
{
    public function upcomingTest(Request $request){
        $user=Auth::user();
        if($user->user_type=='l'){

            $toDay= time();
            $toDay =date('Y-m-d',$toDay);

            $ok=$request->date;
            $ok=date('Y-m-d',strtotime($ok));

            if(empty($request->date)){
                $ok =$toDay;
            }        


            $serId=DB::select('SELECT laboratory_appointments.id,laboratory_appointments.lab_id,laboratory_appointments.patient_id,laboratory_appointments.service_id,laboratory_appointments.appointment_time_id,laboratory_appointments.appointment_date,laboratory_appointments.cancel,laboratory_appointments.by_whom,laboratory_appointments.status,users.name,users.email,users.phone_no,patients.d_o_b,patients.gender,patients.blood_group,patients.profile_pic,services.pick_up,services.in_package,time_table.start_time,time_table.end_time FROM `laboratory_appointments`LEFT JOIN services ON services.id IN ( laboratory_appointments.service_id) LEFT  JOIN  users ON  users.id IN (laboratory_appointments.patient_id) LEFT JOIN patients ON patients.user_id IN (laboratory_appointments.patient_id) LEFT  JOIN  time_table ON  time_table.id IN (laboratory_appointments.appointment_time_id)WHERE lab_id='.$user->id.' AND appointment_date>= "'.$ok.'" ORDER BY laboratory_appointments.appointment_time_id ');


            foreach ($serId as $service){
                $service->services = Service::whereIn('id',explode(",",$service->service_id))->get();
            }

            if(count($serId)<=0){

                $response['return']=true;
                $response['message'] = "No upcoming appointments";
                $response['data']=$serId;
                return response()->json($response, 200);
                
            }

            $response['return']=true;
            $response['message'] = "get appointments with date";
            $response['data']=$serId;
            return response()->json($response, 200);
        }
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
    }

    public function toDayTests(Request $request){
        $user=Auth::user();
        if($user->user_type=='l'){
            $dataAll = [];

//            $toDay= time();
//            $toDay =date('Y-m-d',$toDay);
//            dd($toDay);

            $serId=DB::select('SELECT concat(time_table.start_time,"-",time_table.end_time) AS timeUnit, laboratory_appointments.id,laboratory_appointments.lab_id,laboratory_appointments.patient_id,laboratory_appointments.service_id,laboratory_appointments.appointment_time_id,laboratory_appointments.appointment_date,laboratory_appointments.cancel,laboratory_appointments.by_whom,laboratory_appointments.status,users.name,users.email,users.phone_no,patients.d_o_b,patients.gender,patients.blood_group,patients.profile_pic,services.pick_up,services.in_package,time_table.start_time,time_table.end_time FROM `laboratory_appointments`LEFT JOIN services ON services.id IN ( laboratory_appointments.service_id) LEFT  JOIN  users ON  users.id IN (laboratory_appointments.patient_id) LEFT JOIN patients ON patients.user_id IN (laboratory_appointments.patient_id) LEFT  JOIN  time_table ON  time_table.id IN (laboratory_appointments.appointment_time_id)WHERE lab_id='.$user->id.' AND appointment_date =CURRENT_DATE ORDER BY laboratory_appointments.appointment_time_id ');


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
                $response['return']=true;
                $response['message'] = "No appointments";
                $response['data']=$dataAll;
                return response()->json($response, 200);

            // if(count($serId)<=0){
            //     $response['return']=false;
            //     $response['error'] = "No appointments";
            //     return response()->json($response, 400);
            // }

            // $response['return']=true;
            // $response['message'] = "ToDay's appointments";
            // $response['data']=$serId;
            // return response()->json($response, 200);
        }
                $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);

    }

    public function uploadReport(Request $request){

        $user = Auth::user();
        if ($user->user_type == 'l') {


            $rule = [
                'patient_id' => 'required|exists:laboratory_appointments,patient_id',
                'report_title' => 'required',
                'report_desc' => 'required',
                'report_img' => 'required'

            ];
            $message = [

                'patient_id.required' => 'select patient',
                'report_title.required' => 'prescription title required',
                'report_desc.required' => 'prescription description required',
                'report_img.required' => 'prescription document required'
            ];

            $validator = Validator::make($request->all(), $rule, $message);

            if ($validator->fails()) {
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $patientPrescription=new PatientReport();
            $patientPrescription->user_id=$request->patient_id;
            $patientPrescription->rep_title=$request->report_title;
            $patientPrescription->rep_desc=$request->report_desc;


            if(!empty($request->report_img)){
                $file = $request->file('report_img');
                $name = time().'.'.$file->getClientOriginalExtension();
                $file->move(public_path().'/storage/images/patientReports/',$name);
                $path_patient_prescri = '/storage/images/patientReports/'.$name;
                $patientPrescription->rep_img = $path_patient_prescri;
            }
            $patientPrescription->by_whom=$user->name;
            $patientPrescription->time=time();
            $patientPrescription->save();

            $response['return']=true;
            $response['message']='Report upload sucessfully';
            $response['data']=$patientPrescription;
            return response()->json($response, 200);
        }

        if($user->user_type=='p'){

            $rule = [
                
                'report_title' => 'required',
                'report_desc' => 'required',
                'report_img' => 'required'

            ];
            $message = [

                
                'report_title.required' => 'prescription title required',
                'report_desc.required' => 'prescription description required',
                'report_img.required' => 'prescription document required'
            ];

            $validator = Validator::make($request->all(), $rule, $message);

            if ($validator->fails()) {
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $patientPrescription=new PatientReport();
            $patientPrescription->user_id=$user->id;
            $patientPrescription->rep_title=$request->report_title;
            $patientPrescription->rep_desc=$request->report_desc;


            if(!empty($request->report_img)){
                $file = $request->file('report_img');
                $name = time().'.'.$file->getClientOriginalExtension();
                $file->move(public_path().'/storage/images/patientReports/',$name);
                $path_patient_prescri = '/storage/images/patientReports/'.$name;
                $patientPrescription->rep_img = $path_patient_prescri;
            }
            $patientPrescription->by_whom=$user->name;
            $patientPrescription->time=time();
            $patientPrescription->save();

            $response['return']=true;
            $response['message']='Report upload sucessfully';
            $response['data']=$patientPrescription;
            return response()->json($response, 200);

        }
                $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
    }
}
