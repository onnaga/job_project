<?php

namespace App\Http\Controllers;

use App\Models\now_worker;
use App\Models\old_work;
use Illuminate\Http\Request;

class NowWorkerController extends Controller
{


    public function index()
    {

    }


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


    public function store(Request $request)
    {
        //
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

    public function edit(now_worker $now_worker)
    {
        //
    }


    public function update(Request $request, now_worker $now_worker)
    {
        //
    }
    public function show_employees(Request $request ){

        $company_id=$request->company_id;

        try {
            $company_id=$request->company_id;
            $now_worker=now_worker::where('company_id', $company_id)->first();
            $old_works=old_work::where('company_id',$company_id)->get();
            return response(["id"=>$company_id , 'recent work'=>$now_worker , 'old work'=>$old_works]);
        } catch (\Throwable $th) {
            return response([  'th'=>$th  ,"error"=>'maybe the id is not exist']);
        }



    }
    public function destroy(now_worker $now_worker)
    {
        //
    }
}
