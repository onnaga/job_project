<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\follow;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function create(Request $request){
        $validated = Validator::make($request->all(), ['company_id' => 'required']);

        //Check if the validation failed, return your custom formatted code here.
        if ($validated->fails()) {
            return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
        }
        $user_id=auth()->user()->id;
        $company_id=$request->company_id;
        $comp_in_db=Company::find($company_id);
        if(!$comp_in_db){
            return response()->json(['message'=>'the company id is not true']);
        }
        $in_DB=follow::where([['user_id','=',$user_id],['company_id','=',$company_id]])->first();
        if($in_DB==null){
           $in_DB= follow::create([
                'user_id'=>$user_id,
                'company_id'=>$company_id
            ]);

        }
        return response()->json(['in_db'=>$in_DB]);



    }


    public function delete(Request $request){
        $validated = Validator::make($request->all(), ['company_id' => 'required']);

        //Check if the validation failed, return your custom formatted code here.
        if ($validated->fails()) {
            return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
        }
        $user_id=auth()->user()->id;
        $unfollow=false;
        $company_id=$request->company_id;
        $comp_in_db=Company::find($company_id);
        if(!$comp_in_db){
            return response()->json(['message'=>'the company id is not true']);
        }
        $in_DB=follow::where([['user_id','=',$user_id],['company_id','=',$company_id]])->first();
        if($in_DB!=null){
            $unfollow=$in_DB->delete();
        }
        return response()->json(['deleted'=>$unfollow]);
    }
}
