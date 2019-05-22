<?php

namespace App\Http\Controllers\Api;

use App\Doctor;
use App\Laboratories;
use App\Patitent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
            $rules = [

                'user_type'=>'required',
            
            ];
            $messages   =  [
                'user_type.required'=>'Select User Type',
            ];
            $validator=Validator::make($request->all(),$rules,$messages);
            if ($validator->fails()){
                $response['message']="errors";
                $response['errors']=$validator->errors()->toArray();
                $response['errors_key']=array_keys($validator->errors()->toArray());
                return response()->json($response,400);
            }else{

                if($request->user_type=='d'){

                        $rules = [

                            'name'=>'required',
                            'email'=>'required|unique:users',
                            'mobile_no'=>'required',
                            'user_type'=>'required',
                            'reg_no'=>'required|unique:doctors,doc_reg_no',
                            'clinic_type_id'=>'required',
                            'highest_qualification_id'=>'required',
                            'speciality_id'=>'required',
                            'd_o_b'=>'required',
                            'gender'=>'required',
                            'password'=>'required',
                            'c_password'=>'required',
                            'address' => 'required'
                        ];

                        $messages=[
                            'name.required'=>'Name field required',
                            'email.required'=>'Email-id field required',
                            'mobile_no.required'=>'Mobile field required',
                            'user_type.required'=>'Select account type',
                            'reg_no.required'=>'Licence no field required',
                            'clinic_type_id.required'=>'Select clinic type',
                            'highest_qualification_id.required'=>'select highest_qualification',
                            'speciality_id.required'=>'Specialization field required',
                            'd_o_b.required'=>'Date of Birth is required',
                            'gender.required'=>'Select gender',
                            'password.required'=>'Password field required',
                            'c_password.required'=>'Confirm password field required',
                            'addresss.required'=>'Address field required',
                        ];

                        $validator=Validator::make($request->all(),$rules,$messages);
                            if ($validator->fails()){
                                $response['message']="errors";
                                $response['errors']=$validator->errors()->toArray();
                                $response['errors_key']=array_keys($validator->errors()->toArray());
                                return response()->json($response,400);
                            }else{
                                $user = new User();
                                $user->name = $request->name;
                                $user->email = $request->email;
                                $user->phone_no=$request->mobile_no;

                                if(!empty($request->c_password)){

                                    if($request->password == $request->c_password){

                                        $user->password = Hash::make($request->password);
                                    }else{
                                        $response['return']=false;
                                        $response['message']="Confirm Password";
                                        return response()->json($response,400);
                                    }
                                }

                                $user->user_type=$request->user_type;
                                $user->api_token= sha1(time());
                                $user->time= time();
                                $user->save();

                                $registerUser = new Doctor();
                                $registerUser->user_id=$user->id;
                                $registerUser->doc_reg_no=$request->reg_no;
                                $registerUser->clinic_type_id=$request->clinic_type_id;
                                $registerUser->highest_quali_id=$request->highest_qualification_id;
                                $registerUser->specialty_id=$request->speciality_id;
                                $registerUser->d_o_b=$request->d_o_b;
                                $registerUser->gender=$request->gender;
                                $registerUser->clinic_addr=$request->address;
                                // $registerUser->address=$request->address;
                                $registerUser->save();

                                if (!empty($user) && !empty($registerUser)) {
                                    $response['return']=true;
                                    $response['message'] = "Account created.";
                                    $response['data'] = null;
                                    return response()->json($response,200);
                                }
                                    $response['true'] = false;
                                    $response['message']="OOPS! Something went Wrong!!";
                                    return response()->json($response, 400);

                            }
                }

                if($request->user_type=='l'){
                    $rules = [
                        'name'=>'required',
                        'email'=>'required|unique:users',
                        'mobile_no'=>'required',
                        'user_type'=>'required',
                        'd_o_b'=>'required',
                        'gender'=>'required',
                        'speciality_id'=>'required',
                        'password'=>'required',
                        'c_password'=>'required',
                        'reg_no' => 'required',
                        'address' => 'required'
                    ];
                    $messages=[
                        'name.required'=>'Name field required',
                        'email.required'=>'Email-id field required',
                        'mobile_no.required'=>'Mobile field required',
                        'user_type.required'=>'Select account type',            
                        'd_o_b.required'=>'Date of Birth is required',
                        'gender.required'=>'Select gender',
                        'speciality_id'=>'Select Speciality',
                        'password.required'=>'Password field required',
                        'c_password.required'=>'Confirm password field required',
                        'reg_no.required'=>'Registeration number field required',
                        'address.required'=>'Address  field is required',
                    ];
                    $validator=Validator::make($request->all(),$rules,$messages);

                    if ($validator->fails()){
                        $response['message']="errors";
                        $response['errors']=$validator->errors()->toArray();
                        $response['errors_key']=array_keys($validator->errors()->toArray());
                        return response()->json($response,400);
                    }else{
                        $user = new User();
                        $user->name = $request->name;
                        $user->email = $request->email;
                        $user->phone_no=$request->mobile_no;

                        if(!empty($request->c_password)){
                            if($request->password == $request->c_password){
                                $user->password = Hash::make($request->password);
                            }else{
                                $response['return']=false;
                                $response['message']="Confirm Password";
                                return response()->json($response,400);
                            }
                        }

                        $user->user_type=$request->user_type;
                        $user->api_token= sha1(time());
                        $user->time= time();
                        $user->save();

                        $registerUser = new Laboratories();
                        $registerUser->user_id=$user->id;
                        $registerUser->d_o_b=$request->d_o_b;
                        $registerUser->gender=$request->gender;

                        $registerUser->reg_no=$request->reg_no;
                        $registerUser->lab_name=$request->clinic_lab_name;
                        $registerUser->lab_address=$request->address;
                        $registerUser->lab_type=$request->highest_qualification_id;

                        $registerUser->specialty_id=$request->speciality_id;

                        $registerUser->save();

                        if (!empty($user) && !empty($registerUser)) {
                            $response['user']=$user;
                            $response['userdata']=$registerUser;
                            $response['message']="$user->name Sucessfully register";
                            return response()->json($response,200);
                        }
                            $response['message']="OOPS! Something went Wrong!!";
                            return response()->json($response, 400);
                    }
                }
                
                if($request->user_type=='p'){
                    $rules = [
                        'name'=>'required',
                        'email'=>'required|unique:users',
                        'mobile_no'=>'required',
                        'user_type'=>'required',
                        'd_o_b'=>'required',
                        'gender'=>'required',
                        'password'=>'required',
                        'c_password'=>'required',
                    ];
                    $messages=[
                        'name.required'=>'Name field required',
                        'email.required'=>'Email-id field required',
                        'mobile_no.required'=>'Mobile field required',
                        'user_type.required'=>'Select account type',
                        'd_o_b.required'=>'Date of Birth is required',
                        'gender.required'=>'Select gender',
                        'password.required'=>'Password field required',
                        'c_password.required'=>'Confirm password field required',
                    ];
                    $validator=Validator::make($request->all(),$rules,$messages);
                    if ($validator->fails()){
                        $response['message']="errors";
                        $response['errors']=$validator->errors()->toArray();
                        $response['errors_key']=array_keys($validator->errors()->toArray());
                        return response()->json($response,400);
                    }else{
                        $user = new User();
                        $user->name = $request->name;
                        $user->email = $request->email;
                        $user->phone_no=$request->phone_no;
                        if(!empty($request->c_password)){
                            if($request->password == $request->c_password){
                                $user->password = Hash::make($request->password);
                            }else{
                                $response['return']=false;
                                $response['message']="Confirm Password";
                                return response()->json($response,400);
                            }
                        }

                            $user->user_type=$request->user_type;
                            $user->api_token= sha1(time());
                            $user->time= time();
                            $user->save();

                            $registerUser = new Patitent();
                            $registerUser->user_id=$user->id;
                            $registerUser->d_o_b=$request->d_o_b;
                            $registerUser->gender=$request->gender;
                            $registerUser->save();

                            if (!empty($user) && !empty($registerUser)) {
                                $response['user']=$user;
                                $response['userdata']=$registerUser;
                                $response['message']="$user->name Sucessfully register";
                                return response()->json($response,200);
                            }
                                $response['message']="OOPS! Something went Wrong!!";
                                return response()->json($response, 400);
                        }
                }
            }  
    }
}
