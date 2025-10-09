<?php

namespace App\Http\Controllers\Frontend\ClinicUser;

use App\Http\Controllers\Controller;
use App\Models\Order;

class ProfileController extends Controller {

    public function orders(){

        $orders = Order::where('clinic_user_id', auth('clinic')->user()->id)
        ->latest()->get();

        return view('frontend.pages.profile.orders', compact('orders'));

    }
    // order details
    public function orderDetails($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        return response()->json($order);
    }
}