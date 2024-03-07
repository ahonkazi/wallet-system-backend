<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class authController extends Controller
{
    //

    public function register(Request $request)
    {
        // code...
        $request->validate([
            'name'=>'string|required',
            'email'=>'email|required|unique:users',
            'password'=>'string|min:6|max:16|required',
            'gender'=>'string',
            'address'=>'string',
            'phone'=>'string',
            'date_of_birth'=>'date'
        ]);

        //create a new order
        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        if($request->has('gender')){
            $user->gender = $request->gender;
        }

         if($request->has('address')){
            $user->address = $request->address;
        }
          if($request->has('phone')){
            $user->phone = $request->phone;
        }
          if($request->has('date_of_birth')){
            $user->date_of_birth = $request->date_of_birth;
        }
        try {
            $user->assignRole('user');
            $user->save();
            $token = $user->createToken('access-token')->plainTextToken;
            return response()->json(['message'=>'Registration successfull.','user'=>$user,'token'=>$token],201);

        } catch (Exception $e) {
            
        return response()->json(['message'=>'Something went wrong'],500);
        }
    }
  public function login(Request $request)
    {
        // code...
        $request->validate([
            'email'=>'email|required',
            'password'=>'string|min:6|max:16|required',

        ]);

        //check email exists or not

        $user = User::where('email',$request->email)->first();
        if(!$user){
          return response()->json(['message'=>'No account found with the email'],404);
        }
        // check password
        if(!Hash::check($request->password, $user->password)){
          return response()->json(['message'=>'Incorrect password.'],401);
        }
        //create token
        $token = $user->createToken('access-token')->plainTextToken; 
        return response()->json(['message'=>'Logged in successfull.','user'=>$user,'token'=>$token],200);
     
    }


public function logout(Request $request)
{
    // code...
    $user = Auth::user();
    $user->currentAccessToken()->delete();
            return response()->json(['message'=>'Logged out successfull.'],200);

}
}
