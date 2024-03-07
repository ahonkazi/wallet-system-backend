<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Package;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;

class orderController extends Controller
{
    //

    public function placeOrder(Request $request)
    {

        $request->validate([
            'package_id' => 'numeric|required',
            'success_url' => 'string|required',
            'cancel_url'=>'string|required'
        ]);
        $user = Auth::user();
        $pendingOrder = Order::where('user_id', $user->id)->where('status','pending')->first();
        if($pendingOrder){
            $pendingOrder->delete();
        }
        $running_package = Order::where('user_id', $user->id)->where('status','complete')->first();
        if ($running_package) {

            return response()->json(['message' => 'One package is already running.'], 409);
        }

        // check package id validity
        $package = Package::where('id', $request->package_id)->first();
        if (!$package) {
            return response()->json(['message' => 'No package found.'], 404);
        }
      
        // create new order
       $session = $this->createOrderAndCheckoutSession($request,$user,$package);
        if($session['status']==200){
            return response()->json(['session'=>$session['data']],200);
        }else{
            return response()->json(['message'=>'Something went wrong'],500);

        }

    }

    public function verifyOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'string|required',
            'session_id' => 'string|required'
        ]);

//               verify order id
        $order_id = decrypt($request->order_id);
        $user_id = Auth::user()->id;
        $order = Order::where('id', $order_id)->where('user_id', $user_id)->first();
        if (!$order) {
            return response()->json(['message' => "Order id not found"], 404);
        }
        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $session = $stripe->checkout->sessions->retrieve($request->session_id);

        if($session['status']=='complete'){
            $order->status = 'complete';
            $order->save();
            return response()->json(['message' => 'Order completed.'],200);

        }


    }


    public function upgradePackage(Request $request){
        $request->validate([
            'package_id' => 'numeric|required',
            'success_url' => 'string|required',
            'cancel_url'=>'string|required'
        ]);
        $user = Auth::user();
        // check package id validity
        $package = Package::where('id', $request->package_id)->first();
        if (!$package) {
            return response()->json(['message' => 'No package found.'], 404);
        }
        $order = Order::where('package_id',$request->package_id)->where('user_id',$user->id)->first();
        if($order){
            return response()->json(['message' => 'The order has been purchased by you.'], 409);
        }
        $pendingOrder = Order::where('status','pending')->where('user_id',$user->id)->first();
        if($pendingOrder){
            return response()->json(['message' => 'Another order is pending,so you can not upgrade now.'], 409);
        }

        // create new order
        $session = $this->createOrderAndCheckoutSession($request,$user,$package);
        if($session['status']==200){
            return response()->json(['session'=>$session['data']],200);
        }else{
            return response()->json(['message'=>'Something went wrong'],500);

        }


    }

    public function verifyUpgrade(Request $request)
    {
        $request->validate([
            'order_id' => 'string|required',
            'session_id' => 'string|required'
        ]);

//               verify order id
        $order_id = decrypt($request->order_id);
        $user_id = Auth::user()->id;
        $order = Order::where('id', $order_id)->where('user_id', $user_id)->first();
        if (!$order) {
            return response()->json(['message' => "Order id not found"], 404);
        }
        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $session = $stripe->checkout->sessions->retrieve($request->session_id);

        if($session['status']=='complete'){
            $order->status = 'complete';
            $order->save();
            $allOrders = Order::all()->where('user_id',$user_id)->whereNotIn('id',[$order->id]);
            foreach ($allOrders as $o){
               $o->delete();
            }

            return response()->json(['message' => 'Order completed.','order'=>$order],200);

        }


    }



    public function getAllOrders(Request $request)
    {
        $orders = Order::all();
        return response()->json(['message'=>'Order List','orders'=>$orders],200);
    }

    public function getMyOrders(Request $request)
    {
       $orders = Auth::user()->orders;

       return response()->json(['message'=>'Order List','orders'=>$orders],200);


    }

//    helper function

function createOrderAndCheckoutSession($request,$user,$package){

return \Illuminate\Support\Facades\DB::transaction(function () use($request,$user,$package){
    $order = new Order();
    $order->user_id = $user->id;
    $order->status = 'pending';
    $order->package_id = $package->id;
    if ($package->discounted_price) {
        $order->price = $package->discounted_price;
    } else {
        $order->price = $package->price;
    }
    $order->save();
    $stripe = new StripeClient(env('STRIPE_SECRET'));
    $session = $stripe->checkout->sessions->create([
        'success_url' =>
            $request->success_url . '?order_id=' . encrypt($order->id) . '&session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' =>
            $request->cancel_url . '?order_id=' . encrypt($order->id),

        'line_items' => [
            [
                'price_data' => [
                    'currency' => 'USD',
                    'product_data' => [
                        'name' => $package->name,
                    ],
                    'unit_amount' => $order->price * 100,
                ],
                'quantity' => 1,]
        ],
        'mode' => 'payment'
    ]);

    return ['data'=>$session,'status'=>200];


});



}

}
