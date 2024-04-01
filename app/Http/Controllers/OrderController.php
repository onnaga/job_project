<?php

namespace App\Http\Controllers;

use App\Models\order;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File ;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function make_order(Request $request)
    {

    //Define your validation rules here.
    $rules = [
        'company_name' => 'required',
        'job'=>'required',
        'user_cv'=> "required|mimetypes:application/pdf|max:15000",

    ];
    //Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
    $validated = Validator::make($request->all(), $rules);

    //Check if the validation failed, return your custom formatted code here.
    if($validated->fails())
    {
        return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
    }
//If not failed, the code will reach here

//save the data to the database
        $user_id = Auth::user()->id;
        try {
        $company_id = Company::all()->where('name', $request->company_name)[0]->id;
        } catch (\Throwable $th) {
            return response()->json(['error' =>'the name of the company is not correct ' ]);
        }

        $job = $request->job;
        $status ='pending';
        $save_path=storage_path('\users_cv');
        $new_order=null;

        $pdf =  $request->file('user_cv')->getContent();
        //we add the file name using the unique id and the job
        // if the user send the same order to the same company we will delete the first
        //pdf from storage file
        //if the user change the job but same order sended it will not effect the existed
        //file
        $file_name_in_DB=$user_id .$company_id ;



        try {
            $deleted= File::delete($save_path.'\\' .$file_name_in_DB .'.pdf');
        } catch (\Throwable $th) {
            $deleted='no path in the DB';
        }







                    if (!file_exists($save_path)) {
                        mkdir($save_path, 777, true);
                    }


                    $stored =File::put($save_path.'\\'. $file_name_in_DB.'.pdf',$pdf);


                    //we create the order in database only when there is no order like this
                    $old_order =order::where([['user_id', '=', $user_id],['company_id', '=', $company_id]])->first() ;
                    if($old_order==null){
                    $new_order= order::create([
                        'user_id'=>$user_id,
                        'company_id' => $company_id,
                        'the_job'=>$job,
                        'user_cv'=>$file_name_in_DB,
                        'status'=>$status,
                        'company_report'=>'nothing'
                    ]);
                }
                else{
                      order::find($old_order->id)->update([
                    'the_job'=>$job,
                    'user_cv'=>$file_name_in_DB,
                ]);
                $updated_order =order::where([['user_id', '=', $user_id],['company_id', '=', $company_id]])->first() ;
                }

                return response()->json(['new order'=>$new_order ,'url' =>$save_path.'\\'.$file_name_in_DB .'pdf'  ,'deleted'=>$deleted , 'job'=>$job ,'stored'=>$stored ,'old order '=>$old_order,'updated_order '=>$updated_order]);
            }



    /**
     * Display the specified resource.
     */
    public function show(order $order)
    {
        // $headers = ['Content-Type' => 'application/pdf'];
        // return response()->file($save_path.'\\'. $file_name_in_DB , $headers);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(order $order)
    {
        //
    }
}
