<?php

namespace App\Http\Controllers;

use App\Models\companies_photo;
use App\Models\Company;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class CompaniesPhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth:apiCompany');
    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    //Define your validation rules here.
    $rules = [
        'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];
    //Create a validator, unlike $this->validate(), this does not automatically redirect on failure, leaving the final control to you :)
    $validated = Validator::make($request->all(), $rules);

    //Check if the validation failed, return your custom formatted code here.
    if($validated->fails())
    {
        return response()->json(['status' => 'error', 'messages' => 'The given data was invalid.', 'errors' => $validated->errors()]);
    }
//If not failed, the code will reach here

//save the data to the database
$user_id = auth()->user()->id;
$image=null;
$filename=null;
$deleted='no path in the DB';
$save_path=storage_path('\uploadsComp');
$photo_id_in_company_table = Company::find($user_id)->photo_id ;

// there we delete the old image from the Storage file
//if the user send the photo then we store the new
//if the user dont send a photo then he dont want a profile photo so he want to
//delete the old image if it exist
$photo_name_in_DB=companies_photo::find($photo_id_in_company_table)->path;
if($photo_name_in_DB !=null){
    $deleted= File::delete($save_path.'\\' .$photo_name_in_DB);

}

            if($request->hasFile('photo')){
            $image = $request->file('photo');
            $filename = time() . '.' . rand(1,99999999999)  .'.' . $image->getClientOriginalExtension();

            if (!file_exists($save_path)) {
                mkdir($save_path, 777, true);
            }
            Image::make($image)->resize(300, 300)->save( $save_path.'\\' . $filename ) ;

            };


            //we create the photo only when the user dont have an id to any photo
            if($photo_id_in_company_table==null){
            $new_photo= companies_photo::create([
                'user name'=>auth()->user()->name,
                'path' => $filename,

            ]);

            $is_created= Company::find($user_id)->update(['photo_id'=>$new_photo->id]);

        }
            else{
                $is_created= companies_photo::find($photo_id_in_company_table)->update(['path'=>$filename,]);


                $new_photo=companies_photo::all()->where('id',$photo_id_in_company_table);
            }



        return response()->json(['new photo'=>$new_photo[0] ,'url' =>$save_path.'\\'.$filename ,'is created'=>$is_created ,'photo id in the company table'=>Company::find($user_id)->photo_id  ,'photo in DB'=>$photo_name_in_DB ,'deleted'=>$deleted ,'delete path'=>$save_path.'\\' .$photo_name_in_DB]);
    }



    /**
     * Display the specified resource.
     */
    public function showMine()
    {

        $photo_id=auth()->user()->photo_id;
        $photo_obj = companies_photo::find($photo_id);
        $path=storage_path('\uploadsComp\\');
        $name_from_DB = $photo_obj->path;
        $headers = ['Content-Type' => 'image/png'];

        if($name_from_DB==null){
            return response()->json([null]);
        }




        try {
            return response()->file($path.$name_from_DB , $headers);
        } catch (\Throwable $th) {
            return response()->json(['errore ' => 'the data is deleted from the server please restore the image ' , 'exception' =>$th]);
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(companies_photo $companies_photo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, companies_photo $companies_photo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(companies_photo $companies_photo)
    {
        //
    }
}
