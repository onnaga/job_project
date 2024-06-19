<?php


namespace App\Http\Controllers;

use App\Models\admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use App\Models\offers;
use App\Models\order;
use App\Models\specialization;
use App\Models\User;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\JWT;
use PHPOpenSourceSaver\JWTAuth\facades\JWTAuth;
use phpseclib\Crypt\Hash as hash2;
use Illuminate\Support\Carbon;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:web', ['except' => ['login']]);
    }
    public function register(Request $request){
        if(auth('web')->user()->id == 1){
        //Define your validation rules here.
        $rules = [
            'name' => 'required',
            'email' => 'required | email | unique:admins,email',
            'password' => 'required|confirmed|min:6',

        ];
        //Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
        $validated = Validator::make($request->all(), $rules);

        //Check if the validation failed, return your custom formatted code here.
        if($validated->fails())
        {
            return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
        }

        //If not failed, the code will reach here
        $admin = admin::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' =>Hash::make($request->password)
                ]);

        //This would be your own error response, not linked to validation
        if (!$admin) {
            return response()->json(['status'=>'error','message'=>'failed_to_create_new_user'], 500);
        }

        //All went well
            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'admin' => $admin,

            ]);
    }else{
        return response()->json(['message'=>'you cant add an admin Only the major admin '],403);
    }



}

public function deleteAdmin(Request $request){
    if(auth('web')->user()->id == 1){
        //Define your validation rules here.
        $rules = [
            'email' => 'required '
        ];
        //Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
        $validated = Validator::make($request->all(), $rules);

        //Check if the validation failed, return your custom formatted code here.
        if($validated->fails())
        {
            return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
        }


        //If not failed, the code will reach here
        $deleted = admin::where('email','=',$request->email)->first()->delete();


        //All went well
            return response()->json([
                'message' => 'User deleted successfully',
            ]);
    }else{
        return response()->json(['message'=>'you cant delete an admin Only the major admin '],403);
    }


}
public function login(Request $request)
{
    if ($request->cookie('token')==null) {


    //Define your validation rules here.
$rules = [
    'email' => 'required | email',
    'password' => 'required|min:6',
    'remember_me'=>'required'
];
//Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
$validated = Validator::make($request->all(), $rules);

//Check if the validation failed, return your custom formatted code here.
if($validated->fails())
{
    return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
}

//If not failed, the code will reach here
    $credentials = $request->only('email', 'password');

    $token = auth('web')->attempt($credentials);
    if (!$token) {
        return response()->json([
            'status' => 'error',
            'message' => 'The password is wrong OR you dont signed up ',
        ], 401);
    }


    $user = auth('web')->user();

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'rememberMe' =>$request->remember_me,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
            ]);


    }



else{
    return response()->json(['has a cookie' , 'cookie'=>$request->cookie('token')]);

}

}

public function getAllOffers(Request $request){

      //Define your validation rules here.
$rules = [
    'search_about'=>'required'
];
//Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
$validated = Validator::make($request->all(), $rules);

//Check if the validation failed, return your custom formatted code here.
if($validated->fails())
{
    return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
}

$sort_by =$request->sort_by;
$company_id =$request->company_id;
$req_offer_id =$request->offer_id;
if (!$sort_by) {
    if ($request->search_about == 'Offers'){
        if ($company_id!='false') {
    $ended_offers=offers::where([['company_id',$company_id],['offer_end_at','<=',Carbon::now()]])->get();
    $recent_offers=offers::where([['company_id',$company_id],['offer_end_at','>',Carbon::now()]])->get();
        }else{
    $ended_offers=offers::where([['offer_end_at','<=',Carbon::now()]])->get();
    $recent_offers=offers::where([['offer_end_at','>',Carbon::now()]])->get();
        }
    return response()->json([
        'number_of_ended'=>sizeof($ended_offers),
        'ended_offers'=>$ended_offers ,
        'number_of_recent'=>sizeof($recent_offers),
         'recent_offers'=>$recent_offers
        ]);

}
    elseif($request->search_about == 'Orders'){
        if($req_offer_id!='false')
        {
            $response =order::where([['offer_id',$req_offer_id]])->get();
        }else{
            $response =order::all();
        }


}
else{
    return response()->json([
        "invalid Data requested not order nor offer in field SearchAbout"
    ],402);

}

    return response()->json([
        'number'=>sizeof($response),
        'data'=>$response
    ]);


}
//if sort by added to the request
else{
    if ($request->search_about == 'Offers'){
        if ($company_id!='false') {
            $ended_offers=offers::where([['company_id',$company_id],['offer_end_at','<=',Carbon::now()]])->get();
            $recent_offers=offers::where([['company_id',$company_id],['offer_end_at','>',Carbon::now()]])->get();
                }else{
            $ended_offers=offers::where('offer_end_at','<=',Carbon::now())->get();
            $recent_offers=offers::where('offer_end_at','>',Carbon::now())->get();
                }
        foreach ($ended_offers as $offer) {
            $offer_id = $offer->id;
            $offer->rating = app('App\Http\Controllers\RatingController')->show_rate_for_offer($offer_id)->original['star_rate'];
        }
        foreach ($recent_offers as $offer) {
            $offer_id = $offer->id;
            $offer->rating = app('App\Http\Controllers\RatingController')->show_rate_for_offer($offer_id)->original['star_rate'];
        }
        return response()->json([
            'number_of_ended'=>sizeof($ended_offers),
            'ended_offers'=>$ended_offers ,
            'number_of_recent'=>sizeof($recent_offers),
             'recent_offers'=>$recent_offers
            ]);




    }

    elseif($request->search_about == 'Orders'){
        if($req_offer_id!='false')
        {
            $response =order::where([['offer_id',$req_offer_id],['status',$sort_by]])->get();
        }else{
            $response =order::where('status','=',$sort_by)->get();
        }



    }
    else{
    return response()->json([
        "invalid Data requested not order nor offer in field SearchAbout"
    ],402);

}


return response()->json([
    'number'=>sizeof($response),
    'data'=>$response
]);


}
}
public function getAllCompanies(Request $request){
    $company_id =$request->company_id;
    if($company_id!='false'){
        $ended_offers=offers::where([['company_id',$company_id],['offer_end_at','<=',Carbon::now()]])->get();
        $recent_offers=offers::where([['company_id',$company_id],['offer_end_at','>',Carbon::now()]])->get();
        foreach ($ended_offers as $offer) {
                //make specialization_id name
                $specId =$offer->specialization_wanted;
                $offer->specialization_wanted=specialization::find($specId)->specialization;

            $offer_id = $offer->id;
            $offer->rating = app('App\Http\Controllers\RatingController')->show_rate_for_offer($offer_id)->original['star_rate'];
        }
        foreach ($recent_offers as $offer) {
            $specId =$offer->specialization_wanted;
            $offer->specialization_wanted=specialization::find($specId)->specialization;
            $offer_id = $offer->id;
            $offer->rating = app('App\Http\Controllers\RatingController')->show_rate_for_offer($offer_id)->original['star_rate'];
        }
        return response()->json([
            'number_of_ended'=>sizeof($ended_offers),
            'ended_offers'=>$ended_offers ,
            'number_of_recent'=>sizeof($recent_offers),
             'recent_offers'=>$recent_offers
            ]);
    }
    else{
$companies = Company::all();
foreach ($companies as $company) {
    $specId =$company->specialization_id;
    $company->specialization_id=specialization::find($specId)->specialization;
}
return response()->json(['data'=>$companies,'number'=>sizeof($companies),]);
}
}

public function getAllSpecializations(){

$specializations=specialization::all();
foreach ($specializations as $specialization) {
    $companies= Company::where('specialization_id','=',$specialization->id)->get('name');
    $specialization->number_of_companies=sizeof($companies);
    $specialization->companies=$companies;
    $users= User::where('specialization_id','=',$specialization->id)->get('name');
    $specialization->number_of_users=sizeof($users);
    $specialization->users=$users;
}
$users_null=User::where('specialization_id','=',null)->get('name');
$companies_null=Company::where('specialization_id','=',null)->get('name');
return response()->json(['specializations'=>$specializations,'Num_users_null'=>sizeof($users_null),'Num_cmpanies_null'=>sizeof($companies_null)]);
}
}

