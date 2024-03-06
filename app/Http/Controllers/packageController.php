<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class packageController extends Controller
{
    //

         public function createPackage(Request $request)
     {
         // code...
        $request->validate([
            'name'=>'string|required'
            'price'=>'numeric|required',
            'discounted_price'=>'numeric|nullable',
            'description'=>'string|nullable',
            'features.*.name'=>'string|required',
            'features.*.description'=>'string|required',
            'features.*.package_id'=>'numeric|required'
        ]);
     } 
}
