<?php

namespace App\Http\Controllers;

use App\Models\now_worker;
use App\Models\old_work;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class NowWorkerController extends Controller
{
    public function create($details ,$id,$company_id)
    {


        try {
        $now_worker=now_worker::where('user_id',$id)->first();
        $old_worker= null;
        $updated_now_worker=null;
        if($now_worker!=null ){
            //we check if the work is in DB
            // before we create it
            if($now_worker->company_id!=$company_id
            || $now_worker->job!=$details->job
            ||$now_worker->salary!=$details->salary){
       $old_worker= old_work::create([
            'user_id'=>$now_worker->user_id,
            'company_id'=>$now_worker->company_id,
            'job'=>$now_worker->job,
            'salary'=>$now_worker->salary,
        ]);
        if(!$details->job)
        $updated_now_worker=now_worker::where('user_id',$id)->update([
            'company_id'=>$company_id,
            'job'=>$now_worker->job,
            'salary'=>$details->salary,
        ]);
        else
        $updated_now_worker=now_worker::where('user_id',$id)->update([
            'company_id'=>$company_id,
            'job'=>$details->job,
            'salary'=>$details->salary,
        ]);

        $now_worker=now_worker::where('user_id',$id)->first();
            }}
        else {
           $now_worker= now_worker::create([
                'user_id'=>$id,
                'company_id'=>$company_id,
                'job'=>$details->job,
                'salary'=>$details->salary,
            ]);
        }

        return response(['id'=>$id,'company_id'=>$company_id , 'now worker'=>$now_worker,'old worker'=>$old_worker , 'updated'=>$updated_now_worker]);

    } catch (\Throwable $th) {
        return response(['error'=>$th]);
    }
}


public function update(Request $request )
{


    $validated = Validator::make($request->all(), ['user_id' => 'required','salary' => 'required',]);

    //Check if the validation failed, return your custom formatted code here.
    if ($validated->fails()) {
        return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
    }

    $company_id=auth()->user()->id;
    $user_id = $request->user_id;
    $details=(object)['job'=>$request->the_job,'salary'=>$request->salary];
    $a=new NowWorkerController();
    $the_return_from_NowWorkerController =$a->create($details,$user_id,$company_id);

    return response()->json(['the_return_from_NowWorkerController' =>$the_return_from_NowWorkerController]);

}


    public function show(Request $request )
    {

        try {
            $id=$request->id;
            $now_worker=now_worker::where('user_id',$id)->first();
            $old_works=old_work::where('user_id',$id)->get();
            return response(["id"=>$id , 'recent work'=>$now_worker , 'old work'=>$old_works]);
        } catch (\Throwable $th) {
            return response([  'th'=>$th  ,"error"=>'maybe the id is not exist']);
        }


    }




    public function show_employees(Request $request ){

        $company_id=$request->company_id;

        try {
            $company_id=$request->company_id;
            $now_worker=now_worker::where('company_id', $company_id)->first();
            $old_works=old_work::where('company_id',$company_id)->get();
            return response(["id"=>$company_id , 'recent workers'=>$now_worker , 'old workers'=>$old_works]);
        } catch (\Throwable $th) {
            return response([  'th'=>$th  ,"error"=>'maybe the id is not exist']);
        }



    }


}
