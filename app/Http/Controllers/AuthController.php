<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\specialization;
use App\Models\level;
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
        return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
    }

    //If not failed, the code will reach here
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please sign up first ',
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
            ]);

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
        return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
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
                return response()->json(['error' =>$th ]);
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
                return response()->json(['error' =>$th ]);
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
    $token = Auth::login($user);
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
            'status' => 'success',
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

        ]);
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
        ]);
    }




}
