<?php

namespace App\Http\Controllers\Patient;

use App\Doctor;
use App\DoctorAppointment;
use App\Laboratories;
use App\LaboratoryAppointment;
use App\PatientPrescription;
use App\PatientReport;
use App\Service;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use DB;
use Symfony\Component\Routing\Tests\Fixtures\AnnotationFixtures\RequirementsWithoutPlaceholderNameController;


class PatientController extends Controller
{
    public function uploadPrescription(Request $request){

        $user=Auth::user();
        if($user->user_type=='p'){

            $rules=[
                'prescription_title'=>'required',
//                'prescription_desc'=>'required',
                'prescription_img'=>'required',
                 'by_whom'=>'required'

            ];
            $messages=[
                'prescription_title.required'=>'prescription title required',
//                'prescription_desc.required'=>'prescription description required',
                'prescription_img.required'=>'prescription document required',
                'by_whom.required'=>'Doctor Name'
            ];

            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return']=false;
                $request['errors']=$validator->errors()->toArray();
                $response['errors_key']=array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $patientPrescription=new PatientPrescription();
            $patientPrescription->user_id=$user->id;
            $patientPrescription->prescription_title=$request->prescription_title;
            if(!empty($request->prescription_desc)){
                $patientPrescription->prescription_desc=$request->prescription_desc;
            }

            if(!empty($request->prescription_img)){
                $file = $request->file('prescription_img');
                $name = time().'.'.$file->getClientOriginalExtension();
                $file->move(public_path().'/storage/images/patientPrescriptions/',$name);
                $path_patient_prescri = '/storage/images/patientPrescriptions/'.$name;
                $patientPrescription->prescription_img = $path_patient_prescri;
            }

            $patientPrescription->by_whom=$request->by_whom;
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

    public function allPatientPrescription(Request $request){
        $user=Auth::user();
        if($user->user_type=='p'){

            $getPrescription=PatientPrescription::orderBy('id','desc')->where('user_id',$user->id)->get();

            $response['return']=true;
            $response['message']='All prescriptions';
            $response['data']=$getPrescription;
            return response()->json($response, 200);
        }

            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);

    }

    public function allResultSearch(Request $request){
        $user=Auth::user();
        if($user->user_type=='p'){
            $allResultSearch=User::orderBy('id','desc')->where('user_type',"d")->orWhere('user_type',"l")->with('doctorDetails','labDetails')->get();
//dd($allResultSearch);

            $response['return']=true;
            $response['message']='All results';
            $response['data']=$allResultSearch;
            return response()->json($response, 200);

        }
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
    }

    public function allDoctors(){
        $user=Auth::user();
        if($user->user_type=='p'){
            $allResultSearch=User::orderBy('id','desc')->where('user_type',"d")->with('doctorDetails')->get();
//dd($allResultSearch);
            $response['return']=true;
            $response['message']='All doctors';
            $response['data']=$allResultSearch;
            return response()->json($response, 200);

        }
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
    }

    public function allLabs(){
        $user=Auth::user();
        if($user->user_type=='p'){
            $allResultSearch=User::orderBy('id','desc')->where('user_type',"l")->with('labDetails')->get();
//dd($allResultSearch);
            $response['return']=true;
            $response['message']='All Laboratories';
            $response['data']=$allResultSearch;
            return response()->json($response, 200);

        }
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
    }

    public function globalSearch(Request $request){
        $user=Auth::user();
        if ($user->user_type=='p'){

            if(!empty($request->search_box)){

                $arr=array();


                $searchData1=DB::select('SELECT name, email, "usr" as doctors FROM users WHERE name LIKE "%'.
                $request->search_box.'%" OR email LIKE "%'.$request->search_box.'%"');
                array_push($arr,$searchData1);

                $searchData2=DB::select('SELECT clinic_name, clinic_addr, "clicname" as clinic FROM doctors WHERE clinic_name LIKE "%'.$request->search_box.'%" OR clinic_addr LIKE "%'.$request->search_box.'%"');
                array_push($arr,$searchData2);

                $searchData3=DB::select('SELECT lab_name, lab_address, "lab" as labs FROM laboratories WHERE lab_name LIKE "%'.
                    $request->search_box.'%" OR lab_address LIKE "%'.$request->search_box.'%"');
                array_push($arr,$searchData3);

                $searchData4=DB::select('SELECT specialization, "spec" as speciality FROM specializations WHERE specialization LIKE "%'.$request->search_box.'%" ');
                array_push($arr,$searchData4);

                $searchData5=DB::select('SELECT name, "ser" as services FROM services WHERE name LIKE "%'.$request->search_box.'%" ');
                array_push($arr,$searchData5);

                $response['return']=true;
                $response['message']='Global search data';
                $response['data']=$arr;
                return response()->json($response, 200);

            }
        }


    }

    public function totalAmount(Request $request){

        $user=Auth::user();
        if($user->user_type=='p'){
            $rules=[
                'service_id'=>'required',
            ];
            $messages=[
                'service_id.required'=>'Select Services',

            ];
            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $totalAmountofSelectedServices=DB::select('SELECT sum(rate) AS total FROM services where id IN('.$request->service_id.')');

            $response['return']=true;
            $response['message'] = "Total Charges";
            $response['total'] = $totalAmountofSelectedServices[0]->total ?? 0;
            return response()->json($response, 200);
        }
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
    }

    public function recentServices(Request $request){
        $user=Auth::user();
        if($user->user_type=='p'){

//            $RecentServices=DB::select('select GROUP_CONCAT( DISTINCT x.sid) sid from (SELECT GROUP_CONCAT(DISTINCT laboratory_appointments.service_id )AS sid FROM laboratory_appointments JOIN doctor_appointments ON laboratory_appointments.patient_id = doctor_appointments.patient_id
//WHERE laboratory_appointments.patient_id = ?
//UNION
//SELECT GROUP_CONCAT(DISTINCT doctor_appointments.service_id)AS sid FROM doctor_appointments JOIN laboratory_appointments ON laboratory_appointments.patient_id = doctor_appointments.patient_id
//WHERE doctor_appointments.patient_id = ? ) x',[$user->id,$user->id])[0];
//            dd($RecentServices->sid);

            $recentArray=array();
            $RecentServicesfdoc=DB::select('SELECT GROUP_CONCAT(DISTINCT doctor_appointments.service_id)AS sid FROM doctor_appointments WHERE doctor_appointments.patient_id = '.$user->id.' ')[0];
            array_push($recentArray,$RecentServicesfdoc);

            $RecentServicesflab=DB::select('select GROUP_CONCAT( DISTINCT laboratory_appointments.service_id)AS sid FROM  laboratory_appointments WHERE laboratory_appointments.patient_id = '.$user->id.' ')[0];
            array_push($recentArray,$RecentServicesflab);
            $recentStringn="";
           foreach ($recentArray as $rearray){
               $recentStringn.= ",".$rearray->sid;
           }
            $recentStringn= trim($recentStringn, ',');


            $exploed = (array_unique(explode(',',$recentStringn)));


            $recentData=  Service::orderBy('id','desc')->whereIn('id',$exploed)->get();
            $response['true']=true;
            $response['message']='Recent services';
            $response['data']=$recentData;
            return response()->json($response, 200);

        }

            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
    }

    public function recentDoctors(Request $request){
        $user=Auth::user();
        if($user->user_type =='p'){


            $data=DB::select('SELECT GROUP_CONCAT( DISTINCT doctor_appointments.doctor_id)AS did from doctor_appointments WHERE doctor_appointments.patient_id='.$user->id.'')[0];
//            dd($data);

            $recentData=Doctor::whereIn('user_id',explode(',',$data->did))->with('doctorName')->get();

//            dd($recentData);
            $response['true']=true;
            $response['message']='Recent services';
            $response['data']=$recentData;
            return response()->json($response, 200);

        }
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);


    }

    public function mySchedule(Request $request){
        $user=Auth::user();
        if($user->user_type=='p'){

            $serId=DB::select('SELECT doctor_appointments.id,doctor_appointments.doctor_id,doctor_appointments.patient_id,doctor_appointments.service_id,doctor_appointments.appointment_time_id,doctor_appointments.appointment_date,doctor_appointments.service_amount,services.pick_up,services.in_package,users.name,users.email,users.phone_no,doctors.clinic_name,doctors.clinic_addr,doctors.profile_pic,doctors.gender,time_table.start_time,time_table.end_time FROM `doctor_appointments`LEFT JOIN services ON services.id IN ( doctor_appointments.service_id) LEFT  JOIN  users ON  users.id IN (doctor_appointments.doctor_id) LEFT JOIN doctors ON doctors.user_id IN (doctor_appointments.doctor_id) LEFT  JOIN  time_table ON  time_table.id IN (doctor_appointments.appointment_time_id)WHERE patient_id='.$user->id.' AND appointment_date>=CURRENT_DATE ORDER BY doctor_appointments.appointment_time_id ');


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

    public function appointmentCalculator(Request $request){
        $user=Auth::user();

        if($user->user_type=='d'){


            $totalappointments=DB::select('SELECT COUNT( appointment_time_id) AS totalAppointments From doctor_appointments WHERE doctor_id = '.$user->id.' AND appointment_date= CURRENT_DATE AND cancel IS null');

            $reaminingappointments=DB::select('SELECT COUNT( appointment_time_id) AS remaningAppointments From doctor_appointments WHERE doctor_id = '.$user->id.' AND appointment_date= CURRENT_DATE AND cancel IS null AND status ="n" ');
            // dd($reaminingappointments);


            if(!empty($totalappointments)){
                $response['return']=true;
                $response['message'] = "Today's Appointments";
                $response['totalAppointments']= $totalappointments[0]->totalAppointments ?? 0;
                $response['remaningAppointments']=$reaminingappointments[0]->remaningAppointments ?? 0;
                return response()->json($response, 200);
               
            }
                $response['return']=true;
                $response['message'] = "No Appointments today";
                $response['totalAppointments']= $totalappointments[0]->totalAppointments ?? 0;
                $response['remaningAppointments']=$reaminingappointments[0]->remaningAppointments ?? 0;
                return response()->json($response, 200);

            
        }

        if($user->user_type=='l'){
            $totalappointments=DB::select('SELECT COUNT( appointment_time_id) AS totalAppointments From laboratory_appointments WHERE lab_id = '.$user->id.' AND appointment_date= CURRENT_DATE AND cancel IS null ');

            $reaminingappointments=DB::select('SELECT COUNT( appointment_time_id) AS remaningAppointments From laboratory_appointments WHERE lab_id = '.$user->id.' AND appointment_date= CURRENT_DATE AND cancel IS null AND status ="n" ');

            if(!empty($totalappointments)){

                $response['return']=true;
                $response['message'] = "Today's Appointments";
                $response['totalappointments']=$totalappointments[0]->totalAppointments ?? 0;
                $response['reaminingappointments']=$reaminingappointments[0]->remaningAppointments ?? 0;
                return response()->json($response, 200);

                
            }
                $response['return']=true;
                $response['message'] = "No Appointments today";
                 $response['totalappointments']=$totalappointments[0]->totalAppointments ?? 0;
                $response['reaminingappointments']=$reaminingappointments[0]->remaningAppointments ?? 0;
                return response()->json($response, 200);

            
        }

            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);



    }

    public function cancelAppointment(Request $request){

        $user=Auth::user();

        if($user->user_type=='l'){
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);

        }

        $rule=[
            'appointment_id'=>'required|exists:doctor_appointments,id',
        ];
        $message=[
            'appointment_id.required'=>'select appointment',
        ];

        $validator=Validator::make($request->all(),$rule,$message);

        if($validator->fails()){
            $response['return']=false;
            $response['errors']=$validator->errors()->toArray();
            $response['errors_key']=array_keys($validator->errors()->toArray());
            return response()->json($response,400);
        }

        $appointmetis=DoctorAppointment::where('id',$request->appointment_id)->first();

        $appointmetis->cancel='y';
        if($user->id==$appointmetis->doctor_id||$user->id==$appointmetis->patient_id){
            $appointmetis->by_whom=$user->name;
        }else{
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
        }
        $appointmetis->save();


        $getDate =DoctorAppointment::where('id',$request->appointment_id)->with('doctorDetails','patientDetails')->first();


        $message='Hello, '.$getDate->patientDetails->name.'
your appointment with '.$getDate->doctorDetails->name.' has been Cancelled which is held on '.$getDate->appointment_date.'

appointment cancelled by : '.$user->name.'';
        $number= $getDate->patientDetails->phone_no;


        sendMessages($message,$number);

        $message='Hello, '.$getDate->doctorDetails->name.'
your appointment with '.$getDate->patientDetails->name.' has been Cancelled which is held on '.$getDate->appointment_date.'

appointment cancelled by : '.$user->name.'';
        $number= $getDate->patientDetails->phone_no;

//        dd($message,$number);
        sendMessages($message,$number);

        $response['return']=true;
        $response['message']='sucessfully cancel appointment';
        $response['data']=$appointmetis;
        return response()->json($response, 200);
    }

    public function cancelTest(Request $request){
        $user=Auth::user();

        if($user->user_type=='d'){
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);

        }
        $rule=[
            'test_id'=>'required|exists:laboratory_appointments,id',
        ];
        $message=[
            'test_id.required'=>'select appointment'
        ];

        $validator=Validator::make($request->all(),$rule,$message);

        if($validator->fails()){
            $response['return']=false;
            $response['errors']=$validator->errors()->toArray();
            $response['errors_key']=array_keys($validator->errors()->toArray());
            return response()->json($response,400);
        }

        $appointmetis=LaboratoryAppointment::where('id',$request->test_id)->first();
        // dd($appointmetis);


        $appointmetis->cancel='y';

        if($user->id==$appointmetis->lab_id||$user->id==$appointmetis->patient_id){
            $appointmetis->by_whom=$user->name;
        }else{
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
        }

        $appointmetis->save();

        $getDate =LaboratoryAppointment::where('id',$request->test_id)->with('labDetails','patientDetails')->first();


        $message='Hello, '.$getDate->patientDetails->name.'
your appointment with '.$getDate->labDetails->name.' has been Cancelled which is held on '.$getDate->appointment_date.'

appointment cancelled by : '.$user->name.'';
        $number= $getDate->patientDetails->phone_no;


        // sendMessages($message,$number);

        $message='Hello, '.$getDate->labDetails->name.'
your appointment with '.$getDate->patientDetails->name.' has been Cancelled which is held on '.$getDate->appointment_date.'

appointment cancelled by : '.$user->name.'';
        $number= $getDate->patientDetails->phone_no;

//        dd($message,$number);
        // sendMessages($message,$number);

        $response['return']=true;
        $response['message']='sucessfully cancel your appointment';
        $response['data']=$appointmetis;
        return response()->json($response, 200);

    }

    public function rePlanAppointment(Request $request){
        $user=Auth::user();
        if($user->user_type=='l'){
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);

        }

        $rule=[
            'appointment_id'=>'required|exists:doctor_appointments,id',
            'service_id'=>'required',
            'date'=>'required',
            'time_id'=>'required',
        ];
        $message=[
            'appointment_id.required'=>'select appointment',
            'service_id.required'=>'select services',
               'date.required'=>'select appointment date',
               'time_id.required'=>'choose appointment time id'
        ];

        $validator=Validator::make($request->all(),$rule,$message);

        if($validator->fails()){
            $response['return']=false;
            $response['errors']=$validator->errors()->toArray();
            $response['errors_key']=array_keys($validator->errors()->toArray());
            return response()->json($response,400);
        }
        $totalAmountofSelectedServices=DB::select('SELECT sum(rate) AS total FROM services where id IN('.$request->service_id.')')[0];


        $appointmetis=DoctorAppointment::where('id',$request->appointment_id)->first();

        if($appointmetis->cancel=='y'){
            $response['return']=true;
            $response['message']='Opps it is cancled by '.$appointmetis->by_whom.'';
            return response()->json($response, 400);
        }
        $appointmetis->service_id=$request->service_id;
        $appointmetis->appointment_time_id=$request->time_id;
        $appointdate= $request->date;
        $appointmentDate=date('Y-m-d',strtotime($appointdate));
        $appointmetis->appointment_date=$appointmentDate;
        $appointmetis->service_amount=$totalAmountofSelectedServices->total;
        $appointmetis->replan='y';

        if($user->id==$appointmetis->doctor_id||$user->id==$appointmetis->patient_id){
            $appointmetis->by_whom=$user->name;
        }else{
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
        }

        $appointmetis->save();

//        Hey %STAFF_FIRSTNAME%,
//
//Your %REASON% appointment with %CLIENT_FULLNAME% for %REASON% on %APPT_DATE_TIME% at %LOCATION_NAME% has been rescheduled. The appointment is now set for %APPT_DATE_TIME_STAFF%

//
        $getDate =DoctorAppointment::where('id',$request->appointment_id)->with('doctorDetails','patientDetails','timeDetails')->first();


        $message='Hello, '.$getDate->patientDetails->name.'
your appointment with '.$getDate->doctorDetails->name.' has been rescheduled/replan. the appointment is now set for '.$getDate->appointment_date.', '.$getDate->timeDetails->start_time.' to '.$getDate->timeDetails->end_time.'

appointment rescheduled by : '.$user->name.'';
        $number= $getDate->patientDetails->phone_no;


        sendMessages($message,$number);

        $getDate =DoctorAppointment::where('id',$request->appointment_id)->with('doctorDetails','patientDetails','timeDetails')->first();


        $message='Hello, '.$getDate->doctorDetails->name.'
your appointment with '.$getDate->patientDetails->name.' has been rescheduled/replan. the appointment is now set for '.$getDate->appointment_date.', '.$getDate->timeDetails->start_time.' to '.$getDate->timeDetails->end_time.'
appointment rescheduled by : '.$user->name.'';
        $number= $getDate->doctorDetails->phone_no;


        sendMessages($message,$number);


        $response['return']=true;
        $response['message']='sucessfully Re-Plan your appointment';
        $response['data']=$appointmetis;
        return response()->json($response, 200);

    }

    public function rePlanTest(Request $request){
        $user=Auth::user();
        if($user->user_type=='d'){
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);

        }
        $rule=[
            'appointment_id'=>'required|exists:laboratory_appointments,id',
            'service_id'=>'required',
            'date'=>'required',
            'time_id'=>'required',
        ];
        $message=[
            'appointment_id.required'=>'select appointment',
            'service_id.required'=>'select services',
            'date.required'=>'select appointment date',
            'time_id.required'=>'choose appointment time id'
        ];

        $validator=Validator::make($request->all(),$rule,$message);

        if($validator->fails()){
            $response['return']=false;
            $response['errors']=$validator->errors()->toArray();
            $response['errors_key']=array_keys($validator->errors()->toArray());
            return response()->json($response,400);
        }
        $totalAmountofSelectedServices=DB::select('SELECT sum(rate) AS total FROM services where id IN('.$request->service_id.')')[0];


        $appointmetis=LaboratoryAppointment::where('id',$request->appointment_id)->first();

        if($appointmetis->cancel=='y'){
            $response['return']=true;
            $response['message']='Opps it is canceled by '.$appointmetis->by_whom.'';

            return response()->json($response, 200);
        }
        $appointmetis->service_id=$request->service_id;
        $appointmetis->appointment_time_id=$request->time_id;
        $appointdate= $request->date;
        $appointmentDate=date('Y-m-d',strtotime($appointdate));
        $appointmetis->appointment_date=$appointmentDate;
        $appointmetis->service_amount=$totalAmountofSelectedServices->total;
        $appointmetis->replan='y';

        if($user->id==$appointmetis->lab_id||$user->id==$appointmetis->patient_id){
            $appointmetis->by_whom=$user->name;
        }else{
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);
        }

        $appointmetis->save();

        $getDate =LaboratoryAppointment::where('id',$request->appointment_id)->with('labDetails','patientDetails','timeDetails')->first();


        $message='Hello, '.$getDate->patientDetails->name.'
your appointment with '.$getDate->labDetails->name.' has been rescheduled/replan. the appointment is now set for '.$getDate->appointment_date.', '.$getDate->timeDetails->start_time.' to '.$getDate->timeDetails->end_time.'

appointment rescheduled by : '.$user->name.'';
        $number= $getDate->patientDetails->phone_no;


        sendMessages($message,$number);

        $getDate =DoctorAppointment::where('id',$request->appointment_id)->with('labDetails','patientDetails','timeDetails')->first();


        $message='Hello, '.$getDate->labDetails->name.'
your appointment with '.$getDate->patientDetails->name.' has been rescheduled/replan. the appointment is now set for '.$getDate->appointment_date.', '.$getDate->timeDetails->start_time.' to '.$getDate->timeDetails->end_time.'

appointment rescheduled by : '.$user->name.'';
        $number= $getDate->labDetails->phone_no;


        sendMessages($message,$number);


        $response['return']=true;
        $response['message']='sucessfully Re-Plan your appointment';
        $response['data']=$appointmetis;
        return response()->json($response, 200);

    }

    public function viewReport(Request $request){
        $user=Auth::user();

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

            $prescriptions = PatientReport::orderBy('id','desc')->where('user_id',$request->patient_id)->get();


            if(count($prescriptions) > 0){
                $response['return']=true;
                $response['message'] = "All Reports";
                $response['data']=$prescriptions;
                return response()->json($response, 200);
                
            }

                $response['return']=true;
                $response['message'] = "No Records Found";
                $response['data']=$prescriptions;
                return response()->json($response, 200);
    }
}
