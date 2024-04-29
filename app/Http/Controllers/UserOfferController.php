<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\offers;
use App\Models\order;
use App\Models\user_offer;
use Illuminate\Http\Request;
use App\Notifications\WorkNote;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserOfferController extends Controller
{
    public function accept_company_offer(Request $request){
        try {
        $offer_id=$request->offer_id;
        $the_offer=offers::where('id',$offer_id)->first();
        $user_id=Auth::user()->id;
        User::find($user_id);
        $company_id=$the_offer->company_id;
        $is_accepted =user_offer::where([['user_id','=',$user_id],['offer_id','=',$offer_id],['company_id','=',$company_id]])->first();
        if($is_accepted==null){
        $user_offer_relation=user_offer::create([
        "user_id"=>$user_id,
        "offer_id"=>$offer_id,
        "company_id"=>$company_id,
        ]);
        $the_response=$user_offer_relation;
        $note= new WorkNote($the_response);
        $user=Company::where('id',$company_id)->first();
        $user->notify($note);
        return response()->json(['user_offer_relation'=>$user_offer_relation]);

    }



        return response()->json(['message'=>'you accebted it before']);



    } catch (\Throwable $th) {
        return response()->json(['error' =>'the offer id is wrong','message'=>$th->getMessage()]);
    }

    }

    public function show_accepters(Request $request)
    {
    $offer_id =$request->offer_id;
        $i=0;
    $the_offers=user_offer::where('offer_id',$offer_id)->get();
    if ($the_offers->isEmpty()) {
        return response()->json(['message'=>'there is no accepters or offer_id is wrong']);
    }
        $users_array=[];
    foreach ($the_offers as $single) {
        $obj =new \stdClass();
        $obj->id =$single->user_id;
        $user_name=User::find($single->user_id)->name;

        $obj->name=$user_name;

        $users_array[$i]=$obj;
        $i++;
    }
    return response()->json($users_array);
    }


    public function offer_orders(Request $request){
        $validated = Validator::make($request->all(), ['offer_id' => 'required',]);

        //Check if the validation failed, return your custom formatted code here.
        if ($validated->fails()) {
            return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
        }
        $offer_id = $request->offer_id;
        $the_orders=order::where('offer_id', $offer_id)->get();
        return response()->json($the_orders);

    }
    }
