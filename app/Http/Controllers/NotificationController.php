<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\notification;
use App\Models\offers;
use App\Models\User;
use Illuminate\Http\Request;
use stdClass;

class NotificationController extends Controller
{

    public function show(Request $request)
    {
        try {


        $user_id=$request->id;
        $all_notes=notification::where([['notifiable_id', '=', $user_id], ['notifiable_type', '=', 'App\Models\User']])->get();

            $all_data = [];
            $i=0;

        foreach ($all_notes as $note) {
            $obj= new stdClass();
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

        return response()->json( $all_data );

} catch (\Throwable $th) {
    return response()->json(['th'=>$th]);
}
    }
    public function show_company(Request $request)
    {
        try {


        $company_id=auth()->user()->id;
        $all_notes=notification::where([['notifiable_id', '=', $company_id], ['notifiable_type', '=', 'App\Models\Company']])->get();

            $all_data = [];
            $i=0;

        foreach ($all_notes as $note) {
            $obj= new stdClass();
            $data =json_decode($note->data) ;
            $_id= $data->the_order->user_id;
            $user_name=User::where('id',$company_id)->first()->name;
            $offer_id= $data->the_order->offer_id;
            $the_offer=offers::where('id',$offer_id)->first();
            $obj->user_name=$user_name;
            $obj->the_offer=$the_offer;
            $all_data[$i]=$obj;
            $i++;
        }

        return response()->json( $all_data );

} catch (\Throwable $th) {
    return response()->json(['error'=>$th->getMessage()]);
}
    }

}
