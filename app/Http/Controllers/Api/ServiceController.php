<?php

namespace App\Http\Controllers\Api;

use App\Service;
use App\TimeTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DB;


class ServiceController extends Controller
{
    public function serviceRegister(Request $request){
        $user=Auth::user();
        if($user->user_type=='d'){
            $rules=[
                'service_name'=>'required',
                'service_prize'=>'required',
                'service_time'=>'required',

            ];
            $messages=[
                'service_name.required'=>'Type Service name',
                'service_prize.required'=>'Service prize required',
                'service_time.required'=>'Service time required(in Minutes)',

            ];
            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $doctor_service_insert = new Service();
            $doctor_service_insert->user_id=$user->id;
            $doctor_service_insert->name=$request->service_name;
            $doctor_service_insert->rate=$request->service_prize;
            $doctor_service_insert->service_time=$request->service_time;
            $doctor_service_insert->time=time();
            $doctor_service_insert->save();
            $response['return']=true;
            $response['message'] = "Service details sucessfully update";
            $response['data'] = $doctor_service_insert;
            return response()->json($response, 200);

        }

        if($user->user_type=='l'){

            $rules=[
                'service_name'=>'required',
                'service_prize'=>'required',
                'service_time'=>'required',
               'service_pickup'=>'required',
               'service_package'=>'required'

            ];
            $messages=[
                'service_name.required'=>'Type Service name',
                'service_prize.required'=>'Service prize required',
                'service_time.required'=>'Service time required(in Minutes)',
               'service_pickup.required'=>'Pickup action required',
               'service_package.required'=>'Do you want to add in package ',

            ];
            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $lab_service_insert= new Service();
            $lab_service_insert->user_id=$user->id;
            $lab_service_insert->name=$request->service_name;
            $lab_service_insert->rate=$request->service_prize;
            $lab_service_insert->pick_up=$request->service_pickup;
            $lab_service_insert->service_time=$request->service_time;
            if(!empty($request->service_package)){

                $lab_service_insert->in_package=$request->service_package;
            }
            $lab_service_insert->time=time();
            $lab_service_insert->save();
            $response['return']=true;
            $response['message'] = "Laboratory services sucessfully update";
            $response['data'] = $lab_service_insert;
            return response()->json($response, 200);

        }


        if($user->user_type=='p'){
            $response['return']=false;
            $response['message']='Something went wrong';
            $response['errors']="";
            $response['errors_key']="";

        }


    }

    public function myServices(Request $request){
        $user=Auth::user();

        if($user->user_type=='d'){
            $allServices= Service::orderBy('id','desc')->where('user_id',$user->id)->get();

            if(count($allServices)<=0){
                $response['return']=false;
                $response['message']='There is no services Added';
                return response()->json($response, 400);
            }

            $response['return']=true;
            $response['message']='All services';
            $response['data']=$allServices;
            return response()->json($response, 200);
        }

        if($user->user_type=='l'){
            $allServices= Service::orderBy('id','desc')->where('user_id',$user->id)->get();
            if(count($allServices)<=0){
                $response['return']=false;
                $response['message']='There is no services Added';
                return response()->json($response, 400);
            }
            $response['return']=true;
            $response['message']='All services';
            $response['data']=$allServices;
            return response()->json($response, 200);
        }
    }


    public function deleteServices(Request $request)
    {
        $user=Auth::user();

        $rules=[
                'service_id'=>'required|exists:services,id',
               

            ];
            $messages=[
                'service_id.required'=>'select Service id',
                
            ];
            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $checkService= Service::where('id',$request->service_id)->where('user_id',$user->id)->first();

            if(!empty($checkService)){
                Service::where('id',$request->service_id)->where('user_id',$user->id)->delete();
            $response['return']=true;
            $response['message']='Delete Service';
            $response['data']= $checkService;
            return response()->json($response, 200);
            }else{
            $response['return']=true;
            $response['message']='service not found';
            $response['data']=$checkService; 
            return response()->json($response, 200);
            }

            if($user->user_type=='p'){
                $response['return']=false;
                $response['message']='Something went wrong';
                $response['errors']="";
                $response['errors_key']="";               
                return response()->json($response, 400);
        }
    }

    public function editService(Request $request)
    {
        $user=Auth::user();
        if($user->user_type=='p'){
                $response['return']=false;
                $response['message']='Something went wrong';
                $response['errors']="";
                $response['errors_key']="";               
                return response()->json($response, 400);
        }
        $rules=[
                'service_id'=>'required|exists:services,id',
                'service_name'=>'required',
                'service_prize'=>'required',
                'service_time'=>'required',
                'service_pickup'=>'required',
                'service_inpackage'=>'required'
            ];
            $messages=[
                'service_id.required'=>'select Service id',
                'service_name.required'=>'Service name required',
                'service_prize.required'=>'service amount required',
                'service_time.required'=>'service time required',
                'service_pickup.required'=>'pickup action required',
                'service_inpackage.required'=>'Do you want to add in package',
                
            ];
            $validator=Validator::make($request->all(),$rules,$messages);
            if($validator->fails()){
                $response['return'] = false;
                $response['errors'] = $validator->errors()->toArray();
                $response['errors_key'] = array_keys($validator->errors()->toArray());
                return response()->json($response, 400);
            }

            $checkService= Service::where('id',$request->service_id)->where('user_id',$user->id)->first();

            if(!empty($checkService)){
                $checkService->name=$request->service_name;
                $checkService->rate=$request->service_prize;
                $checkService->pick_up=$request->service_pickup;
                $checkService->service_time=$request->service_time;
                $checkService->in_package=$request->service_inpackage;
                $checkService->save();

                $response['return']=true;
                $response['message']='update Service';
                $response['data']=$checkService;
                return response()->json($response, 200);
            }else{

                $response['return']=true;
                $response['message']='service not found';
                $response['data']=$checkService;
                return response()->json($response, 200);
            }
    }
    public function time_table(Request $req){
        // dd("Sadasdas");
        $auth = Auth::user();
        
        $data= DB::select(DB::raw(
            "SELECT time_table.*,(SELECT count(id) FROM doctor_schedule WHERE time_table_id = time_table.id AND user_id = $auth->id) flag FROM time_table
            "
        ));
        // $select_slot=DB::select('SELECT t.id,t.start_time,t.end_time, COUNT(da.appointment_time_id) AS app FROM time_table AS t LEFT JOIN doctor_appointments AS da ON t.id=da.appointment_time_id AND da.doctor_id='.$request->doctor_id.' AND da.appointment_date='.$request->date.' GROUP BY t.id HAVING app < 30 AND t.id NOT IN (SELECT ds.time_table_id FROM doctor_schedule AS ds WHERE ds.time_table_id is NOT null AND ds.user_id ='.$request->doctor_id.')');
        
        return \response()->json($data,200);
    }
}
