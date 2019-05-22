<?php

namespace App\Http\Controllers\Api;

use App\ForgotPassword;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use phpDocumentor\Reflection\Types\Null_;

class LoginController extends Controller
{
    public function login(Request $request){
        $validationArray = array();
        $validationArray['user_type']= 'required';
        $validationArray['email']='required|exists:users';
        $validationArray['password']='required';
        $validator=Validator::make($request->all(),$validationArray);
        if ($validator->fails()) {
            $response['return'] = false;
            $response['errors'] = $validator->errors()->toArray();
            $response['errors_key'] = array_keys($validator->errors()->toArray());
            return response()->json($response, 400);
        }
            $user = User::where('email',$request->email)->where('user_type',$request->user_type)->first();
        if($user==Null){
            $response['return'] = false;
            $response['message']="The email address or user type that you've entered doesn't match any account. SignUp for an account.";
            return response()->json($response, 400);
        }
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $response['return']=true;
            $response['message'] = "Sucessfully login welcome";
            $response['data'] = $user;
            return response()->json($response, 200);
        } else {
            $response['return']=false;
            $response['message'] = "You entered wrong password, please confirm your password";
            return response()->json($response, 400);
        }
    }

    public function otpGenerate(Request $request){
        $rule = [
            'user_type' => 'required',
            'email' => 'required|exists:users',
        ];
        $message = [
            'user_type.required' => 'select account Type',
            'email.required'=>'enter your email address',

        ];

        $validator = Validator::make($request->all(), $rule, $message);

        if ($validator->fails()) {
            $response['return'] = false;
            $response['errors'] = $validator->errors()->toArray();
            $response['errors_key'] = array_keys($validator->errors()->toArray());
            return response()->json($response, 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user){
        $response['return'] = false;
        $response['message'] = "No user found";
        $response['errors'] = "";
        $response['errors_key'] = "";
        return response()->json($response, 400);
        }
    



        $num_str = sprintf("%06d", mt_rand(1, 999999));

        $alreadyforgot= ForgotPassword::where('email_id',$request->email)->first();

        if(!empty($alreadyforgot)){
            $alreadyforgot->o_t_p=$num_str;
            $alreadyforgot->time=time();
            $alreadyforgot->save();
            $message='Your OTP is '.$num_str.'
valid for 30 minutes';
            $number= $user->phone_no;
//            sendMessages($message,$number);

            $response['return']=true;
            $response['message'] = "OTP send your registered mobile number";
            return response()->json($response, 200);
        }

        $forgot = new ForgotPassword();
        $forgot->email_id=$request->email;
        $forgot->o_t_p=$num_str;
        $forgot->time=time();
        $forgot->save();

        $message='Your OTP is '.$num_str.'
valid for 30 minutes';
        $number= $user->phone_no;
        sendMessages($message,$number);

        $response['return']=true;
        $response['message'] = "OTP send your registered mobile number";
        return response()->json($response, 200);

    }

    public function changePassword(Request $request){
        $validationArray = array();
        $validationArray['email']='required|exists:users';
        $validationArray['otp']='required';
        $validator=Validator::make($request->all(),$validationArray);
        if ($validator->fails()) {
            $response['return'] = false;
            $response['errors'] = $validator->errors()->toArray();
            $response['errors_key'] = array_keys($validator->errors()->toArray());
            return response()->json($response, 400);
        }
        $user= ForgotPassword::where('email_id',$request->email)->first();

        $user_time= date('Y-m-d H:i',$user->time);

        $add_time = date('Y-m-d H:i',strtotime('+30 minutes', $user->time));

        $cur_time=date('Y-m-d H:i',time());

        if($add_time < $cur_time ){
            $response['return'] = false;
            $response['message'] = "OTP is expired";
            $response['errors'] = "";
            $response['errors_key'] = "";
            return response()->json($response, 400);
        }
        if($user->o_t_p == $request->otp){
            $validationArray = array();
            $validationArray['new_password']='required';
            $validationArray['c_password']='required';
            $validator=Validator::make($request->all(),$validationArray);
            if ($validator->fails()) {
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }
            $get_user= User::where('email',$request->email)->first();
            if($request->new_password == $request->c_password){
                $get_user->password=bcrypt($request->new_password);
                $get_user->save();
                $response['return']=true;
                $response['message'] = "Your password sucessfully change";
                return response()->json($response, 200);
            }else{
                $response['return'] = false;
                $response['message'] = "Confirm password";
                $response['errors'] = "";
                $response['errors_key'] = "";
                return response()->json($response, 400);
            }

        }
        $response['return'] = false;
        $response['message'] = "Invalid OTP";
        $response['errors'] = "";
        $response['errors_key'] = "";
        return response()->json($response, 400);

    }

}
