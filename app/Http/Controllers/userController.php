<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
class userController extends Controller
{
    //

    public function getUserSettings(Request $request)
    {
        $user = Auth::user();
        $permissions = $user->getAllPermissions()->pluck('name');
        $showIdentitySection = false;
        $roles = $user->getRoleNames();
        $packages = Package::all();
        $user_order = Order::where('user_id', $user->id)->where('status', 'complete')->first();
        $highest_price = 0;
        foreach ($packages as $package) {
            $effective_price = $package->price;
            if (isset($package->discounted_price) &&($package->discounted_price >0) && $package->discounted_price < $package->price) {
                $effective_price = $package->discounted_price;
            }
            $highest_price = max($highest_price, $effective_price);
        }
        if ($user_order) {
            if ($user_order->price >= $highest_price) {
                $showIdentitySection = true;
            }
        }
        return response()->json([
            'user' => $user,
            'logged_in' => true,
            'roles' => $roles,
            'permissions' => $permissions,
            'can_see_identity_section' => $showIdentitySection,
            'orders'=>$user->orders

        ], 200);

    }

    public function editUserInformation(Request $request)
    {
        $request->validate([
            'name'=>'string',
            'gender' => [
                'string',
                Rule::in(['male', 'female', 'others','Male','Female','Others']),
            ],
            'phone'=>'string',
            'date_of_birth'=>'date',
            'address'=>'string'

        ]);

        $user = Auth::user();
        try {
            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('gender')) {
                $user->gender = $request->gender;
            }

            if ($request->has('phone')) {
                $user->phone = $request->phone;
            }

            if ($request->has('date_of_birth')) {
                $user->date_of_birth = $request->date_of_birth;
            }
            if ($request->has('address')) {
                $user->address = $request->address;
            }

            $user->save();
            return response()->json(['message' => 'Updated successfully.', 'user' => $user], 200);

        } catch (\Exception $exception) {
            return response()->json(['message' => 'Something went wrong.'], 500);

        }

    }

    public function getUserInformation(Request $request){
        $user = Auth::user();
        return response()->json(['user' => $user], 200);

    }

    public function getAllUser(Request $request){
        $user = Auth::user();
        if(!$user->can('user-list')){
            return response()->json(['message'=>'Access denied.'],401);
        }
        $users = User::with("roles")->whereHas("roles")->get();
        return response()->json(['users'=>$users],200);

    }
}
