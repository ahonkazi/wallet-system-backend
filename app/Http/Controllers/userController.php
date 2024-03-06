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
        $permissions = $user->getAllPermissions();
        $showIdentitySection = false;
        $roles = $user->getRoleNames();
        $packages = Package::all();
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
            'roles'=>$roles,
            'permissions'=>$permissions,
            'can_see_identity_section'=>$showIdentitySection

        ], 200);

    }
}
