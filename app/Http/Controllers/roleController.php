<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class roleController extends Controller
{
    //

    public function createRole(Request $request)
     {

        return response()->json(['mgs'=>'ok']);
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
