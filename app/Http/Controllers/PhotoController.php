<?php

namespace App\Http\Controllers;

use Intervention\Image\ImageManagerStatic as Image;
use App\Models\photo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;




class PhotoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


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
$user_id = Auth::user()->id;
$image=null;
$filename=null;
$save_path=storage_path('\uploads');
$photo_id_in_user_table = User::find($user_id)->photo_id ;
$photo_name_in_DB=null;
$deleted='no path in the DB';

// there we delete the old image from the Storage file
//if the user send the photo then we store the new
//if the user dont send a photo then he dont want a profile photo so he want to
//delete the old image if it exist
if ($photo_id_in_user_table!=null) {
    $photo_name_in_DB=photo::find($photo_id_in_user_table)->path;
    if($photo_name_in_DB !=null){
        $deleted= File::delete($save_path.'\\' .$photo_name_in_DB);

    }
}



            if($request->hasFile('photo')){
              $image = $request->file('photo');
              $filename = time() . '.' . $image->getClientOriginalExtension();

              if (!file_exists($save_path)) {
                mkdir($save_path, 777, true);
            }
              Image::make($image)->resize(300, 300)->save( $save_path.'\\' . $filename ) ;

            };
            //we create the photo only when the user dont have an id
            if($photo_id_in_user_table==null){
            $new_photo= photo::create([
                'user name'=>Auth::user()->name,
                'path' => $filename,

            ]);
            $is_created= User::find($user_id)->update(['photo_id'=>$new_photo->id]);
        }
            else{
                $is_created= photo::find($photo_id_in_user_table)->update(['path'=>$filename,]);


                $new_photo=photo::all()->where('id',$photo_id_in_user_table);
            }



        return response()->json(['new photo'=>$new_photo[0] ,'url' =>$save_path.'\\'.$filename ,'is created in database'=>$is_created ,'photo id in the users table'=>User::find($user_id)->photo_id  ,'photo deleted in DB'=>$photo_name_in_DB ,'deleted from storage file'=>$deleted ,'delete path'=>$save_path.'\\' .$photo_name_in_DB]);
    }


    public function showMine(photo $photo)
    {
        $photo_id=Auth::user()->photo_id;
        $photo_obj = photo::find($photo_id);
        $path=storage_path('\uploads\\');
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




    public function edit(photo $photo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, photo $photo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(photo $photo)
    {
        //
    }
}
