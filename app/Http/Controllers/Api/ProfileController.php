<?php

namespace App\Http\Controllers\Api;

use App\Doctor;
use App\Laboratories;
use App\Patitent;
use App\User;;
use App\BloodGroup;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function registerProfileDetails(Request $request){
        $user = Auth::user();

        if($user->user_type=='d'){

            $rules = [
                'clinic_name'=>'required',
                'clinic_reg_no'=>'required',
                'clinic_addr'=>'required',
                'profile_pic'=>'required',
                'd_o_b'=>'required',
                'email'=>'reqired',
                'phone_no'=>'required',
                'highest_qualification_id'=>'required',
                'speciality_id'=>'required',

            ];
            $messages=[
                'clinic_name.required'=>'Clinic name required',
                'clinic_reg_no.required'=>'Clinic registration number required',
                'clinic_addr.required'=>'Clinic address required',
                'profile_pic.required'=>'Profile pictur required',
                'd_o_b.required'=>'Date Of Birth reqiured',
                'email.required'=>'Email required',
                'mobile_no.required'=>'phonr number required',
                'highest_qualification_id.required'=>'higest qualification required',
                'speciality_id.required'=>'speciality required',
            ];
            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }
            $insertDoctorProfile= Doctor::where('user_id',$user->id)->first();

            $insertDoctorProfile->clinic_name=$request->clinic_name;
            $insertDoctorProfile->clinic_reg_no=$request->clinic_reg_no;
            $insertDoctorProfile->clinic_addr=$request->clinic_addr;

            if(!empty($request->profile_pic)){ //optional
                $file = $request->file('profile_pic');
                $name = time().'.'.$file->getClientOriginalExtension();
                $file->move(public_path().'/storage/images/doctorProfileImages/',$name);
                $path_doc_profi_pic = '/storage/images/doctorProfileImages/'.$name;
                $insertDoctorProfile->profile_pic = $path_doc_profi_pic;
            }
            $insertDoctorProfile->d_o_b=$request->d_o_b;

            if(!empty($request->email)){

                if($request->email == $user->email){
                    $user->email=$request->email;
                    $user->save();
                }else{

                }
            }
            $user->phone_no=$request->mobile_no;
            $user->save();
            $insertDoctorProfile->specialty_id=$request->speciality_id;
            $insertDoctorProfile->highest_quali_id=$request->highest_qualification_id;

            $insertDoctorProfile->save();
            $response['return']=true;
            $response['message'] = "Profile details sucessfully update";
            $response['data'] = $insertDoctorProfile;
            return response()->json($response, 200);
        }

        if($user->user_type=='l'){

            $rules = [
                'lab_reg_no'=>'required',
                'lab_name'=>'required',
                'lab_addr'=>'required',
                'd_o_b'=>'required',
                'specialty_id'=>'required',
               'lab_logo'=>'required',
               'email'=>'required',
               'phone_no'=>'required',


            ];
            $messages=[
                'lab_reg_no.required'=>'Laboratory licence no is Required',
                'lab_name.required'=>'Laboratory name required',
                'lab_addr.required'=>'Laboratory address required',
                'd_o_b.required'=>'Datae of Birth required',
                'specialty_id.required'=>'speciality required',
                'lab_logo.required'=>'Lab logo required',
                'email.required'=>'email required',
                'phone_no.required'=>'phone number required ',
            ];

            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $insertLabProfile = Laboratories::where('user_id',$user->id)->first();
            $insertLabProfile->lab_reg_no=$request->lab_reg_no;
            $insertLabProfile->lab_name=$request->lab_name;
            $insertLabProfile->lab_address=$request->lab_addr;
            $insertLabProfile->d_o_b=$request->d_o_b;
            $insertLabProfile->specialty_id=$request->specialty_id;

            if(!empty($request->lab_logo)){
                $file = $request->file('lab_logo');
                $name = time().'.'.$file->getClientOriginalExtension();
                $file->move(public_path().'/storage/images/labLogoImages/',$name);
                $path_lab_logo_pic = '/storage/images/labLogoImages/'.$name;
                $insertLabProfile->lab_logo = $path_lab_logo_pic;
            }

            $user->phone_no=$request->phone_no;

            if($request->email == $user->email){
                $user->email=$request->email;
            }else{

            }
            $user->save();
            $insertLabProfile->save();
            $response['return']=true;
            $response['message'] = "Profile details sucessfully update";
            $response['data'] = $insertLabProfile;
            return response()->json($response, 200);

        }

        if($user->user_type=='p'){
            $rules = [
                // 'profile_pic'=>'required',
                'blood_group'=>'required',
                'dob' => 'required',
                'address' => 'required',
            ];
            $messages=[
                'profile_pic.required'=>'Choose profile picture',
                'blood_group.required'=>'Select Blood Group',
                'dob.required'=>'DOB is required',
                'address.required'=>'Address  is required',
            ];

            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $insertPatProfile = Patitent::where('user_id',$user->id)->first();
            // dd($insertPatProfile);

            if(!empty($request->profile_pic)){
                $file = $request->file('profile_pic');
                $name = time().'.'.$file->getClientOriginalExtension();
                $file->move(public_path().'/storage/images/patientProfile/',$name);
                $path_patient_pic = '/storage/images/patientProfile/'.$name;

                $data=[
                    'profile_pic'=>$path_patient_pic,
                    'blood_group'=>$request->blood_group,
                    'd_o_b' => $request->dob,
                    'address' => $request->address,
                    'gender' => $request->gender,
                ];

                Patitent::where('user_id',$user->id)->update($data);

                // $insertPatProfile->profile_pic = $path_patient_pic;
            }
            $response['return']=true;
            $response['message'] = "Profile details sucessfully update";
            $response['data'] = $insertPatProfile;
            return response()->json($response, 200);

        }

    }

    public function addBloodGroup(Request $request)
    {
        $user=Auth::user();
        if($user->user_type=='p'){

            $bloodGroup= BloodGroup::get();
            $response['return']=true;
            $response['message']='Blood Groups';
            $response['data']=$bloodGroup;
            return response()->json($response, 200);

        }

            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";
            return response()->json($response, 400);

    }

    public function myProfile(Request $request){

        $user=Auth::user();
        if($user->user_type=='d'){

            $profile =Doctor::where('user_id',$user->id)->with('doctorName','clicnic_type','sepcility','qualification')->first();

            $response['return']=true;
            $response['message']='My profile';
            $response['data']=$profile;
            return response()->json($response, 200);
        }

        if($user->user_type=='l'){

            $profile =Laboratories::where('user_id',$user->id)->with('labName')->first();

            $response['return']=true;
            $response['message']='My profile';
            $response['data']=$profile;
            return response()->json($response, 200);
        }

        if($user->user_type=='p'){

            $profile =Patitent::where('user_id',$user->id)->with('patientName')->first();

            $response['return']=true;
            $response['message']='My profile';
            $response['data']=$profile;
            return response()->json($response, 200);
        }
    }

    public function doctorDetail(Request $request){
        $user=Auth::user();
        $rule=[
            'doctor_id'=>'required',
        ];
        $message=[
            'doctor_id.required'=>'select doctor',
        ];

        $validator=Validator::make($request->all(),$rule,$message);
        if($validator->fails()){
            $response['return']=false;
            $response['errors']=$validator->errors()->toArray();
            $response['errors_key']=array_keys($validator->errors()->toArray());
            return response()->json($response, 400);
        }
        $detailDoctor = User::where('id',$request->doctor_id)->with('doctorDetails')->get();

        $response['return']=true;
        $response['message']='Doctor';
        $response['data']=$detailDoctor;
        return response()->json($response, 200);
    }

    public function laboratoryDetail(Request $request){

        $user=Auth::user();
        $rule=[
            'lab_id'=>'required',
        ];
        $message=[
            'lab_id.required'=>'select laboratory',
        ];

        $validator=Validator::make($request->all(),$rule,$message);

        if($validator->fails()){
            $response['return']=false;
            $response['errors']=$validator->errors()->toArray();
            $response['errors_key']=array_keys($validator->errors()->toArray());
            return response()->json($response, 400);

        }

        $detailLaboratory=User::where('id',$request->lab_id)->with('labDetails')->get();

        $response['return']=true;
        $response['message']='Laboratory';
        $response['data']=$detailLaboratory;
        return response()->json($response, 200);
    }

    public function patientDetail(Request $request){

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
            return response()->json($response, 400);
        }

        $detailPatient=User::where('id',$request->patient_id)->with('patientDetails')->first();

            $response['return']=true;
            $response['message']='Patient Detail';
            $response['data']=$detailPatient;
            return response()->json($response, 200);

        if($user->user_type=='p'){
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";               
            return response()->json($response, 400);
        }
        
    }
}
