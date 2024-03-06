<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Package;
use App\Models\PackageFeatures;
use DB;
use Illuminate\Support\Facades\Auth;
class packageController extends Controller
{
    //

         public function createPackage(Request $request)
     {
         // validate...
        $request->validate([
            'name'=>'string|required',
            'price'=>'numeric|required',
            'discounted_price'=>'numeric|nullable',
            'description'=>'string|nullable',
            'features' => 'required|array',
            'features.*.name'=>'string|required',
            'features.*.description'=>'string',
        ]);

        $user = Auth::user();
       try {
            if(!$user->can('package-create')){
            return response()->json(['message'=>'Unauthorized'],401);
        }
           return DB::transaction(function () use ($request) {
            // creating package
            $package = new Package();
            $package->name   = $request->name;
            $package->price = $request->price;
            $package->discounted_price = $request->input('discounted_price',null);
            $package->description = $request->input('description','');
            $package->save();

            // create package features
            $features =[];
            foreach ($request->features as $feature) {

               $createdFeature= PackageFeatures::create([
                    'name'=>$feature['name'],
                    'description'=>$feature['description'] ?? '',
                    'package_id'=>$package->id
                ]);
                $features[] = $createdFeature; 
            }

            return response()->json(['message'=>'Package created','package'=>$package,'features'=>$features],201);

        });
       } catch (Exception $e) {
          return response()->json(['message'=>'Something went wrong'],500);   
       }

     } 
}
