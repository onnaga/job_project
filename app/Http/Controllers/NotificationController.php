<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\notification;
use Illuminate\Http\Request;
use stdClass;

class NotificationController extends Controller
{

    public function show(Request $request)
    {
        // try {


        $user_id=$request->id;
        $all_notes=notification::all()->where('notifiable_id',$user_id);
            $i=0;
            $all_data = [];
            $obj= new stdClass();
        foreach ($all_notes as $note) {
            $data =json_decode($note->data) ;
            $company_id= $data->the_order->company_id;
            $company_name=Company::where('id',$company_id)->first()->name;
            $report= $data->the_order->company_report;
            $obj->status=$data->the_order->status;
            $obj->company_name=$company_name;
            $obj->report=$report;
            $obj->complete_order=$data->the_order;
            $all_data[$i]=$obj;
            $i++;
        }

        return response()->json( $all_data);

// } catch (\Throwable $th) {
//     return response()->json(['th'=>$th]);
// }
    }
}
