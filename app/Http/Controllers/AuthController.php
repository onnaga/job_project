<?php

namespace App\Http\Controllers;

use App\Models\areas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\specialization;
use App\Models\level;

use function PHPUnit\Framework\isEmpty;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
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

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'The password is wrong OR you dont signed up  ',
            ], 401);
        }

        $user = Auth::user();
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
        'email' => 'required | email | unique:users,email',
        'password' => 'required|min:6',
        'phone'=>'min:10'
    ];
    //Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
    $validated = Validator::make($request->all(), $rules);

    //Check if the validation failed, return your custom formatted code here.
    if($validated->fails())
    {
        return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()],403);
    }
//If not failed, the code will reach here

//there we try to check if the user enter a level and specialization
//if not return and store null
//if yes we chech if it exist inside the models
// {if not we create them }
    $specialization_id=null;
    $level_id=null;


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
    if($request->has('level') ){

        try {
            $level_id=level::select('id')->where('level' ,$request->level)->get()[0]->id;
        } catch (\Throwable $th) {
            if (($level_id==null)){
            level::create(['level' =>$request->level]);
            $level_id=level::select('id')->where('level' ,$request->level)->get()[0]->id;
            }
            else {
                return response()->json(['error' =>$th ],400);
            }
        }




    }

    $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'specialization_id'=> $specialization_id,
                'level_id'=>$level_id,
                'phone'=>$request->phone,
                'age'=>$request->age,
            ]);

    //This would be your own error response, not linked to validation
    if (!$user) {
        return response()->json(['status'=>'error','message'=>'failed_to_create_new_user'], 500);
    }

    //All went well
     Auth::login($user);
    $credentials = $request->only('email', 'password');
    $token = Auth::attempt($credentials);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
}



    public function logout()
    {



        Auth::logout();
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
        $level =null;
        $specialization_id=Auth::user()->specialization_id;
        $level_id =Auth::user()->level_id;
        if (!($specialization_id==null)) {
            $specialization = specialization::select('specialization')->where('id' ,$specialization_id)->get()[0]->specialization;
        }
        if (!($level_id==null)) {
            $level = level::select('level')->where('id' ,$level_id)->get()[0]->level;
           }

        return response()->json([
            'user'=>[
            "id"=> Auth::id(),
        "name"=> Auth::user()->name,
        "email"=> Auth::user()->email,
        "email_verified_at"=> Auth::user()->email_verified_at,
        "specialization"=> $specialization,
        "level"=>$level,
        "phone"=> Auth::user()->phone,
        "photo_id"=>Auth::user()->photo_id,
        "age"=> Auth::user()->age,
        "created_at"=> Auth::user()->created_at,
        "updated_at"=> Auth::user()->updated_at,
            ]

        ],200);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ],200);
    }

    public function add_personal_data(Request $request)
    {
        $user_id = Auth::user()->id;
        $the_user = User::find($user_id);
        $specialization_id = $the_user->specialization_id;
        $area_id = $the_user->area_id;
        $level_id = $the_user->level_id;
        $name = $the_user->name;
        $phone =$the_user->phone;
        $age = $the_user->age;




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
            //insert the level in specializations table
            if($request->has('level')&& !empty($request->level) ){
                $level = $request->level;
                $level_in_DB = level::where(['level'=>$level])->first();
                if(!empty($level_in_DB)){
                    $level_id =$level_in_DB->id;
                }
                else{
                    $level_in_DB =  level::create([
                        'level'=>$level
                    ]);
                    $level_id =$level_in_DB->id;
                }
            }
            if($request->has('name')&& !empty($request->name)){
                $name = $request->name;
            }
            if($request->has('phone'&& !empty($request->phone)) ){
                $phone = $request->phone;
            }
            if($request->has('age') && !empty($request->age)){
                $age = $request->age;
            }

        $the_user = User::find($user_id)->update([
            'name'=>$name,
            "specialization_id"=> $specialization_id,
            "area_id"=> $area_id,
            "level_id"=> $level_id,
            "phone"=> $phone,
            "age"=> $age,
        ]);

    return response()->json(["updated"=>$the_user]);


    }




}
