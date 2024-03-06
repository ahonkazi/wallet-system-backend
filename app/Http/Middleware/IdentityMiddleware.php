<?php

namespace App\Http\Middleware;

use App\Models\Order;
use App\Models\Package;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IdentityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $packages = Package::all();
        $user_order = Order::where('user_id', $user->id)->where('status', 'complete')->first();

        $highest_price = 0;
        foreach ($packages as $package){
            $effective_price = $package->price;
            if(isset($package->discounted_price) && $package->discounted_price < $package->price){
                $effective_price = $package->discounted_price;
            }
            $highest_price = max($highest_price,$effective_price);
        }
        if(!$user_order){
            return response()->json(['message'=>'Purchase a package to see NID and Passport section.'],401);
        }
        if($user_order->price < $highest_price){
            return response()->json(['message'=>'Purchase highest package to see NID and Passport section.'],401);
        }
        return $next($request);
    }
}
