<?php

namespace App\Http\Controllers;

use App\Models\offers;
use App\Models\specialization;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

use Throwable;

class OffersController extends Controller
{

//for user only

public function show_company_orders(Request $request){

    $company_id=$request->company_id;

    $ended_offers=offers::where([['company_id',$company_id],['offer end at','<=',Carbon::now()]])->get();

    $recent_offers=offers::where([['company_id',$company_id],['offer end at','>',Carbon::now()]])->get();
    return response()->json(['$ended_offers'=>$ended_offers , 'recent offers'=>$recent_offers]);

}


// for company

    public function create(Request $request){

              //Define your validation rules here.
              $rules = [
                'specialization_wanted' => 'required',
                'the_job' => 'required',
                'offer_end_at'=>'required',
            ];
            //Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
            $validated = Validator::make($request->all(), $rules);

            //Check if the validation failed, return your custom formatted code here.
            if ($validated->fails()) {
                return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
            }
            //If not failed, the code will reach here

            //save the data to the database
            $company_id = auth()->user()->id;
            $spec_name = $request->specialization_wanted;
            $spec_in_DB=specialization::where('specialization',$spec_name)->first();
            if($spec_in_DB==null){
                $spec_in_DB=specialization::create([
                    'specialization'=>$spec_name,
                ]);

            }
            $the_offer=offers::create([
                'the job'=>$request->the_job,
                'company_id'=>$company_id,
                'specialization_wanted'=>$spec_in_DB->id,
                'salary'=>$request->salary,
                'the days'=>$request->the_days,
                'hour begin'=>$request->hour_begin,
                'period'=>$request->period,
                'official holidays'=>$request->official_holidays,
                'offer end at'=>$request->offer_end_at
            ]);

            return response()->json(['the_offer_created'=>$the_offer]);

    }

    public function show(){
        $company_id=auth()->user()->id;

        $ended_offers=offers::where([['company_id',$company_id],['offer end at','<=',Carbon::now()]])->get();

        $recent_offers=offers::where([['company_id',$company_id],['offer end at','>',Carbon::now()]])->get();
        return response()->json(['$ended_offers'=>$ended_offers , 'recent offers'=>$recent_offers]);

    }


    public function show_all(){
        $all_offers=offers::all();

        $recent_offers=offers::where([['offer end at','>',Carbon::now()]])->get();
        return response()->json(['all_offers'=>$all_offers , 'recent offers'=>$recent_offers]);


    }

    public function update(Request $request){
        try {

            $offer_id=$request->offer_id;
            $the_offer=offers::where('id',$offer_id)->first();
            $company_id=auth()->user()->id;
            if($the_offer->company_id == $company_id)
            {
          //Define your validation rules here.
        $rules = [
                'specialization_wanted' => 'required',
                'the_job' => 'required',
                'offer_end_at'=>'required',
            ];
            //Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
            $validated = Validator::make($request->all(), $rules);

            //Check if the validation failed, return your custom formatted code here.
            if ($validated->fails()) {
                return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
            }


        $spec_name = $request->specialization_wanted;
        $spec_in_DB=specialization::where('specialization',$spec_name)->first();
        if($spec_in_DB==null){
            $spec_in_DB=specialization::create([
                'specialization'=>$spec_name,
            ]);
        }
            $updated_offer=$the_offer->update([

                    'the job'=>$request->the_job,
                    'company_id'=>$company_id,
                    'specialization_wanted'=>$spec_in_DB->id,
                    'salary'=>$request->salary,
                    'the days'=>$request->the_days,
                    'hour begin'=>$request->hour_begin,
                    'period'=>$request->period,
                    'official holidays'=>$request->official_holidays,
                    'offer end at'=>$request->offer_end_at


            ]);

            return response()->json(['update'=>$updated_offer,'the updated offer'=>$the_offer ]);

            }

            else{
                return response()->json(['error'=>'you cant update this offer Only the company that owned it','the offer'=>$the_offer ,'req'=>$company_id ,'cmp in BD'=>$the_offer->company_id ]);
            }

        } catch (\Throwable $th) {
                    return response()->json(['message1'=>"the offer is not exist" , 'th'=>$th->getMessage()]);

                }
    }


    public function delete(Request $request){
        try {

            $offer_id=$request->offer_id;
            $the_offer=offers::where('id',$offer_id)->first();
            $company_id=auth()->user()->id;
            if($the_offer->company_id == $company_id)
            {
                $delete_the_offer=$the_offer->delete();

            return response()->json(['delete'=>$delete_the_offer]);

            }

            else{
                return response()->json(['error'=>'you cant delete this offer Only the company that owned it','the offer'=>$the_offer ,'req'=>$company_id ,'cmp in BD'=>$the_offer->company_id ]);
            }

    }
    catch(Throwable $th){


    }


}


}
