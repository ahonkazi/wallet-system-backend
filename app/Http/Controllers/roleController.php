<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;


class roleController extends Controller
{
    //

    public function createRole(Request $request)
     {
        $request->validate([
        'name'=>'string|unique:roles|required'
        ]);
        $user = Auth::user();
        if(!$user->can('role-create')){
            return response()->json(['message'=>'You are not allowed to create role'],401);

        }
        $role = Role::create(['name' => $request->name]);
        return response()->json(['message'=>'Role created','role'=>['id'=>$role->id,'name'=>$role->name]],201);

     }

        public function createPermission(Request $request)
     {
         // code...
     }
         public function assignRole(Request $request)
     {
         // code...


     }

           public function assignPermission(Request $request)
     {
         // code...
     }
}
