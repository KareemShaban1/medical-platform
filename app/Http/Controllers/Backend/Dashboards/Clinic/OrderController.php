<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view orders'), 403, __('You are not authorized to view orders'));

        $user = Auth::guard('clinic')->user();

        $orders = Order::where('clinic_user_id', $user->id)
            ->where('clinic_id', $user->clinic_id)
            ->with(['items.product', 'items.supplier'])
            ->latest()
            ->paginate(15);

        return view('backend.dashboards.clinic.pages.orders.index', compact('orders'));
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view orders'), 403, __('You are not authorized to view orders'));

        $user = Auth::guard('clinic')->user();

        $order = Order::with(['items.product', 'items.supplier'])
            ->where('clinic_user_id', $user->id)
            ->where('clinic_id', $user->clinic_id)
            ->findOrFail($id);

        return response()->json($order);
    }
}
