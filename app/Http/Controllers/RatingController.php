<?php

namespace App\Http\Controllers;

use App\Models\offers;
use App\Models\rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
class RatingController extends Controller
{
    public function create(Request $request){
        $offer_id= $request->offer_id;
        $the_offer = offers::find($offer_id);
        if($the_offer==null){
            return response()->json(['message'=>'the offer id is wrong']);
        }
        $validated = Validator::make($request->all(), ['offer_id' => 'required','rating' => 'required','comment'=>'required']);

        //Check if the validation failed, return your custom formatted code here.
        if ($validated->fails()) {
            return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
        }
        $user_id = auth()->user()->id;
        $in_db=rating::where([['user_id','=',$user_id],['offer_id','=',$offer_id]])->first();
        if ($in_db==null) {
        $the_rate=rating::create([
            'user_id'=>$user_id,
            'offer_id' =>$offer_id,
            'comments'=>$request->comment,
            'star_rating'=>$request->rating
        ]);
}
else{

    return response()->json(['message'=>'you have rated this offer try to update your rate']);
}





        return response()->json(['the rate in DB'=>$the_rate]);

    }

    public function update(Request $request){

        $offer_id= $request->offer_id;
        $the_offer = offers::find($offer_id);

        $validated = Validator::make($request->all(), ['offer_id' => 'required','rating' => 'required','comment'=>'required']);

        //Check if the validation failed, return your custom formatted code here.
        if ($validated->fails()) {
            return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
        }
        if($the_offer==null){
            return response()->json(['message'=>'the offer id is wrong']);
        }
        $user_id = auth()->user()->id;
        $in_db=rating::where([['user_id','=',$user_id],['offer_id','=',$offer_id]])->first();
        if ($in_db==null) {
    return response()->json(['message'=>'you did not rate this offer try to create your rate']);
}
else{
    $the_rate=$in_db->update([
        'user_id'=>$user_id,
        'offer_id' =>$offer_id,
        'comments'=>$request->comment,
        'star_rating'=>$request->rating
    ]);

    $in_db=rating::where([['user_id','=',$user_id],['offer_id','=',$offer_id]])->first();

}





        return response()->json(['updated'=>$the_rate,'the rate in DB'=>$in_db]);


    }
    public function show_rate_for_offer(Request $request){
        $offer_id= $request->offer_id;

        $all_rates= rating::where(['offer_id'=>$offer_id])->get();
        $rate_star=0;
        $sum=0;
        $i=0;
        foreach ($all_rates as $rate) {
        $sum+=$rate->star_rating;
        $i++;
        }
        $rate_star=$sum / $i;



        return response()->json(['star_rate'=>$rate_star,'all_rates'=>$all_rates]);

    }

    // public function delete( Request $request){
    //     $rate_id= $request->rate_id;


    // }

}
