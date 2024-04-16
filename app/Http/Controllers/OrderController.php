<?php

namespace App\Http\Controllers;

use App\Models\order;
use App\Models\Company;
use App\Models\User;
use App\Notifications\WorkNote;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class OrderController extends Controller
{
    //for users

    public function make_order(Request $request)
    {

        //Define your validation rules here.
        $rules = [
            'company_name' => 'required',
            'job' => 'required',
            'user_cv' => "required|mimetypes:application/pdf|max:15000",

        ];
        //Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
        $validated = Validator::make($request->all(), $rules);

        //Check if the validation failed, return your custom formatted code here.
        if ($validated->fails()) {
            return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
        }
        //If not failed, the code will reach here

        //save the data to the database
        $user_id = Auth::user()->id;
        try {
            $company_id = Company::where('name', $request->company_name)->get()->first()->id;
        } catch (\Throwable $th) {
            return response()->json(['error' => 'the name of the company is not correct ']);
        }

        $job = $request->job;
        $status = 'pending';
        $save_path = storage_path('\users_cv');
        $new_order = null;
        $updated_order=null;
        $pdf =  $request->file('user_cv')->getContent();
        //we add the file name using the unique id and the job
        // if the user send the same order to the same company we will delete the first
        //pdf from storage file
        //if the user change the job but same order sended it will not effect the existed
        //file
        $file_name_in_DB = $user_id . $company_id;



        try {
            $deleted = File::delete($save_path . '\\' . $file_name_in_DB . '.pdf');
        } catch (\Throwable $th) {
            $deleted = 'no path in the DB';
        }







        if (!file_exists($save_path)) {
            mkdir($save_path, 777, true);
        }


        $stored = File::put($save_path . '\\' . $file_name_in_DB . '.pdf', $pdf);


        //we create the order in database only when there is no order like this
        $old_order = order::where([['user_id', '=', $user_id], ['company_id', '=', $company_id] ,['status', '=','pending']])->first();

        if ($old_order == null) {
            $new_order = order::create([
                'user_id' => $user_id,
                'company_id' => $company_id,
                'the_job' => $job,
                'user_cv' => $file_name_in_DB,
                'status' => $status,
                'company_report' => 'nothing'
            ]);


        } else {
            order::find($old_order->id)->update([
                'the_job' => $job,
                'user_cv' => $file_name_in_DB,
            ]);
            $updated_order = order::where([['user_id', '=', $user_id], ['company_id', '=', $company_id]])->first();
        }

        return response()->json(['new order' => $new_order, 'url' => $save_path . '\\' . $file_name_in_DB . 'pdf', 'deleted from storage file' => $deleted, 'job' => $job, 'number of characters stored' => $stored, 'old order ' => $old_order, 'updated_order ' => $updated_order ]);
    }

    public function show_all_Mine()
    {


        $user_id = Auth::user()->id;
        $all_in_DB = order::where([['user_id', '=', $user_id]],['status', '=','pending'])->get();
        $all = (object)[];
        $save_path = storage_path('\users_cv');
        //there we will change the content
        //user_cv_pdf will be the pdf encoded base64
        //we add company name property
        foreach ($all_in_DB as $key => $value) {

            //you can comment this for clearer key
            $key = 'order_' . $key;
            $company_id = $value->company_id;

            $company_name = Company::find($company_id)->name;

            $value->company_name = $company_name;

            //$file_name_in_DB = $value->user_cv;

            //$pdf = base64_encode(file_get_contents($save_path . '\\' . $file_name_in_DB . '.pdf'));

            //$value->user_cv_pdf = $pdf;

            $all->$key = $value;
        }





        $responded_order=order::where([['user_id', '=', $user_id],['status', '!=','pending']])->get();
        return response()->json(['user_orders' => $all ,'responded order'=>$responded_order]);
    }

    public function show_specified_order($company_id)
    {
try {
        $user_id = Auth::user()->id;
        $order_in_DB = order::where([['user_id', '=', $user_id], ['company_id', '=', $company_id],['status', '=','pending']])->get()->first();
        $file_name_in_DB = $order_in_DB->user_cv;
        $save_path = storage_path('\users_cv');
        $headers = ['Content-Type' => 'application/pdf'];

        return response()->file($save_path.'\\'. $file_name_in_DB . '.pdf', $headers);

            // $pdf = base64_encode(file_get_contents($save_path . '\\' . $file_name_in_DB . '.pdf'));
        // return response()->json([
        //     'pdf' => $pdf,
        //     'id' => $id
        // ]);

    } catch (\Throwable $th) {
        $responded_order=order::where([['user_id', '=', $user_id],['company_id', '=', $company_id],['status', '!=','pending']])->get();
         return response()->json([
            'EXP' => $th,
            'message'=>'maybe you dont add company_id or you dont send an unresponded order to this company',
            'responded order to this company'=>$responded_order

        ]);
    }


    }

    public function delete_order($company_id)
    {
        try {
                $user_id = Auth::user()->id;
                $order_in_DB = order::where([['user_id', '=', $user_id], ['company_id', '=', $company_id],['status', '=','pending']])->get()->first();
                $file_name_in_DB = $order_in_DB->user_cv;
                $save_path = storage_path('\users_cv');

                try {
                    $deleted = File::delete($save_path . '\\' . $file_name_in_DB . '.pdf');
                } catch (\Throwable $th) {
                    $deleted = 'no path in the DB';
                }

                $deleted_from_DB=order::find($order_in_DB->id)->delete();
                return response()->json([
                    'deleted from storage file '=>$deleted,
                    'delete from database '=>$deleted_from_DB
                ]);

            } catch (\Throwable $th) {
                $responded_order=order::where([['user_id', '=', $user_id],['company_id', '=', $company_id],['status', '!=','pending']])->get();
                 return response()->json([
                    'EXP' => $th,
                    'message'=>'maybe you dont add company_id or you dont send an unresponded order to this company',
                    'responded order to this company'=>$responded_order

                ]);
            }
    }




    //for companies

    public function show_all_Mine_company()
    {


        $company_id = auth()->user()->id;
        $all_in_DB = order::where([['company_id', '=', $company_id],['status', '=','pending']])->get();
        $all = (object)[];
        $save_path = storage_path('\users_cv');
        //there we will change the content
        //user_cv_pdf will be the pdf encoded base64
        //we add company name property
        foreach ($all_in_DB as $key => $value) {

            //you can comment this for clearer key
            $key = 'order_' . $key;
            $user_id = $value->user_id;

            $user_name = User::find($user_id)->name;

            $value->user_name = $user_name;

            //$file_name_in_DB = $value->user_cv;

            //$pdf = base64_encode(file_get_contents($save_path . '\\' . $file_name_in_DB . '.pdf'));

            //$value->user_cv_pdf = $pdf;

            $all->$key = $value;
        }





        $responded_order=order::where([['company_id', '=', $company_id],['status', '!=','pending']])->get();
        return response()->json(['unresponded_orders' => $all ,'responded orders'=>$responded_order]);
    }

    public function show_specified_cv(Request $request)
    {
try {
    $user_id=$request->user_id;
        $company_id = auth()->user()->id;
        $order_in_DB = order::where([['user_id', '=', $user_id], ['company_id', '=', $company_id],['status', '=','pending']])->get()->first();
        $file_name_in_DB = $order_in_DB->user_cv;
        $save_path = storage_path('\users_cv');
        $headers = ['Content-Type' => 'application/pdf'];

        return response()->file($save_path.'\\'. $file_name_in_DB . '.pdf', $headers);

            // $pdf = base64_encode(file_get_contents($save_path . '\\' . $file_name_in_DB . '.pdf'));
        // return response()->json([
        //     'pdf' => $pdf,
        //     'id' => $id
        // ]);

    } catch (\Throwable $th) {
         return response()->json([

            'EXP' => $th,
            'message'=>'maybe you dont add user_id or the user havent unresponded order to your company',
            'responded orders'=>order::where([['company_id', '=', $company_id],['status', '!=','pending']])->get(),
        ]);
    }


    }


    public function answer_to_order(Request $request)
    {

        $user_id=$request->user_id;
        $salary=$request->salary;

        $save_path = storage_path('\users_cv');

// try {
        $company_id = auth()->user()->id;

        $order_in_DB = order::where([['user_id', '=', $user_id], ['company_id', '=', $company_id],['status','=','pending']])->get()->first();
        $file_name_in_DB = $order_in_DB->user_cv;
        $save_path = storage_path('\users_cv');

        $validated = Validator::make($request->all(), ['status' => 'required','report' => 'required',]);

        //Check if the validation failed, return your custom formatted code here.
        if ($validated->fails()) {
            return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
        }




        $old_order =order::where([
        ['user_id', '=', $user_id],
        ['company_id', '=', $company_id],
        ['company_report', '=', 'nothing']
        ])->first();


        $order_id=$old_order->id;
        $pdf = base64_encode(file_get_contents($save_path . '\\' . $file_name_in_DB . '.pdf'));

        $updated_order=order::find($order_id)->update(['status'=>$request->status ,'user_cv'=>strlen($pdf), 'company_report'=>$request->report]);

        //here we will store the work insided the new worker table
        if($request->status=='accepted'){
            $a=new NowWorkerController();
            $array=['job'=>$old_order->the_job,'salary'=>$salary];
            $a->create($array,$user_id,$company_id);

            }


            $the_response=order::where('id',$order_id)->first();
            $note= new WorkNote($the_response);
            $user=User::where('id',$user_id)->first();
            $user->notify($note);

       return response()->json(['updated'=>$updated_order,'status'=>$request->status , 'report'=>$request->report]);


    // } catch (\Throwable $th) {
    //     $responded_order=order::where([['user_id','=',$user_id],['company_id', '=', $company_id],['status', '!=','pending']])->get();
    //      return response()->json([
    //         'EXP' => $th,
    //         'message'=>'maybe you dont add user_id ',
    //         'message 2'=>'there is no unresponded orders for this user',
    //         'responded order for the user'=>$responded_order,

    //     ]);
    // }


    }



}
