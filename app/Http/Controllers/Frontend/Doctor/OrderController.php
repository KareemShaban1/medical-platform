<?php

namespace App\Http\Controllers\Frontend\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $doctor = Auth::guard('clinic')->user();

        // Check if user is a standalone doctor
        if (!$doctor || $doctor->clinic_id !== null) {
            return redirect()->route('home');
        }

        // Get orders made by this doctor with items
        $orders = Order::where('clinic_user_id', $doctor->id)
            ->with('items.product')
            ->latest()
            ->paginate(10);

        return view('frontend.doctor.orders.index', compact('doctor', 'orders'));
    }

    public function orderDetails($id)
    {
        $doctor = Auth::guard('clinic')->user();

        // Check if user is a standalone doctor
        if (!$doctor || $doctor->clinic_id !== null) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $order = Order::with('items.product')
            ->where('clinic_user_id', $doctor->id)
            ->findOrFail($id);

        return response()->json($order);
    }
}

