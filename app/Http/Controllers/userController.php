<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $orders = $user->orders;
        $user_order = Order::where('user_id', $user->id)->where('status', 'complete')->first();
        $highest_price = 0;
        foreach ($packages as $package) {
            $effective_price = $package->price;
            if (isset($package->discounted_price) && $package->discounted_price < $package->price) {
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
            'orders'=>$orders

        ], 200);

    }

    public function editUserInformation(Request $request)
    {
        $request->validate([
            'name'=>'string',
            'gender'=>'string',
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
}
