<?php

namespace App\Http\Controllers;

use App\Models\areas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Company;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\facades\JWTAuth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Models\specialization;
 use Carbon\Carbon;
class CompanyController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:apiCompany', ['except' => ['login','register']]);
    }

     public function login(Request $request)
    {
        //Define your validation rules here.
    $rules = [
        'email' => 'required | email',
        'password' => 'required|min:6'
    ];
    //Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
    $validated = Validator::make($request->all(), $rules);

    //Check if the validation failed, return your custom formatted code here.
    if($validated->fails())
    {
        return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()],403);
    }

    //If not failed, the code will reach here
        $credentials = $request->only('email', 'password');

        $token = auth('apiCompany')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'The password is wrong OR you dont signed up',
            ], 401);
        }


        $user = auth('apiCompany')->user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ],200);

    }




    public function register(Request $request){


        //Define your validation rules here.
        $rules = [
            'name' => 'required',
            'email' => 'required | email | unique:companies,email',
            'password' => 'required|min:6',
            'phone'=>'min:10',
        ];
        //Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
        $validated = Validator::make($request->all(), $rules);

        //Check if the validation failed, return your custom formatted code here.
        if($validated->fails())
        {
            return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()],403);
        }

        //If not failed, the code will reach here

        $specialization_id=null;
        if($request->has('specialization') ){
            try {
                $specialization_id = specialization::select('id')->where('specialization' ,$request->specialization)->get()[0]->id;
            } catch (\Throwable $th) {
                if (($specialization_id==null)){
                specialization::create(['specialization' =>$request->specialization]);
                $specialization_id = specialization::select('id')->where('specialization' ,$request->specialization)->get()[0]->id;
                }
                else {
                    return response()->json(['error' =>$th ],400);
                }

            }


        }


        $area_id=null;
        if($request->has('area') ){
            try {
                $area_id = areas::select('id')->where('area' ,$request->area)->get()[0]->id;
            } catch (\Throwable $th) {
                if (($area_id==null)){
                areas::create(['area' =>$request->area]);
                $area_id = areas::select('id')->where('area' ,$request->area)->get()[0]->id;
                }
                else {
                    return response()->json(['error' =>$th ],400);
                }

            }


        }

        $Age =null;
        $Born=null;
        // to calculate the priod  from the date
        if ($request->has('found_date')) {
            # code...
            $date=$request->found_date;
            $seperated_date =explode('_',$date);
        $Born = Carbon::create($seperated_date[0],$seperated_date[1],$seperated_date[2]);
$Age = $Born->diff(Carbon::now())->format('%Y year _%M month_%D day');
}
        $company = Company::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'specialization_id'=> $specialization_id,
                    'area_id'=> $area_id,
                    'phone'=>$request->phone,
                    'founded_in'=>$request->found_date,
                ]);

        //This would be your own error response, not linked to validation
        if (!$company) {
            return response()->json(['status'=>'error','message'=>'failed_to_create_new_user'], 500);
        }

        //All went well

        auth('apiCompany')->login($company);
        $credentials = $request->only('email', 'password');
        $token = auth('apiCompany')->attempt($credentials);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'company_age'=>$Age,
                'user' => $company,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]

            ]);
    }

    public function logout()
    {



        auth('apiCompany')->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);


    }

    public function me()
    {
        //by this we try to return a level and specialization
        // from another models because we only have a ids in Auth::user()
        $specialization =null;
        $specialization_id=auth('apiCompany')->user()->specialization_id;

        if (!($specialization_id==null)) {
            $specialization = specialization::select('specialization')->where('id' ,$specialization_id)->get()[0]->specialization;
        }


        return response()->json([
            'status' => 'success',
            'user'=>[
            "id"=> Auth::id(),
            "name"=> auth('apiCompany')->user()->name,
            "email"=> auth('apiCompany')->user()->email,
            "specialization"=> $specialization,
            "photo_id"=>auth('apiCompany')->user()->photo_id,
            "phone"=> auth('apiCompany')->user()->phone,
            "company_age"=> auth('apiCompany')->user()->age,
            "created_at"=> auth('apiCompany')->user()->created_at,
            "updated_at"=> auth('apiCompany')->user()->updated_at,
            ]
        ]);
    }

    public function refresh()
    {
        $token = JWTAuth::getToken();
    if(!$token){
        return response()->json(["error"=>'Token not provided']);
    }
    try{
        $token = JWTAuth::refresh($token);
    }catch(\Throwable $e){
        throw new AccessDeniedHttpException('The token is invalid');
    }
    try {
        //code...
    } catch (\Throwable $th) {
        //throw $th;
    }
        return response()->json([
            'status' => 'success',
            'user' => auth('apiCompany')->user(),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    public function add_personal_data(Request $request)
    {
        $company_id = auth('apiCompany')->user()->id;
        $the_company = Company::find($company_id);
        $specialization_id = $the_company->specialization_id;
        $area_id = $the_company->area_id;
        $name = $the_company->name;
        $phone =$the_company->phone;
        $found_date = $the_company->found_date;




            //insert the specialization in specializations table
            if($request->has('specialization')&& !empty($request->specialization) ){
                $specialization = $request->specialization;
            $specialization_in_DB = specialization::where(['specialization'=>$specialization])->first();
            if(!empty($specialization_in_DB)){
                $specialization_id =$specialization_in_DB->id;
            }
            else{
                $specialization_in_DB =  specialization::create([
                    'specialization'=>$specialization
                ]);
                $specialization_id =$specialization_in_DB->id;
            }
            }
            //insert the area in specializations table
            if($request->has('area') && !empty($request->area)){
                $area = $request->area;
                $area_in_DB = areas::where(['area'=>$area])->first();
                if(!empty($area_in_DB)){
                    $area_id =$area_in_DB->id;
                }
                else{
                    $area_in_DB =  areas::create([
                        'area'=>$area
                    ]);
                    $area_id =$area_in_DB->id;
                }
            }

            if($request->has('name')&& !empty($request->name)){
                $name = $request->name;
            }
            if($request->has('phone') && !empty($request->phone) ){
                $phone = $request->phone;

            }
            if($request->has('found_date') && !empty($request->found_date)){
                $found_date = $request->found_date;
            }

        $the_company = Company::find($company_id)->update([
            'name'=>$name,
            "specialization_id"=> $specialization_id,
            "area_id"=> $area_id,
            "phone"=> $phone,
            "founded_in"=> $found_date,
        ]);

    return response()->json(["Phone"=>$phone , 'founded_in '=> $found_date]);


    }



}
