<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Models\Offer;
use Illuminate\Support\Str;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view purchase requests'), 403, __('You are not authorized to view purchase requests'));
        return view('backend.dashboards.admin.pages.purchase-requests.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view purchase requests'), 403, __('You are not authorized to view purchase requests'));
        $requests = Request::with(['categories', 'clinic', 'offers'])->latest();

        return datatables()->of($requests)
            ->addColumn('clinic_name', fn($item) => $item->clinic->name ?? 'N/A')
            ->addColumn('categories', fn($item) => $item->categories->pluck('name')->join(', ') ?: 'N/A')
            ->addColumn('description', fn($item) => Str::limit((string) $item->description, 80))
            ->addColumn('quantity', fn($item) => $item->quantity)
            ->addColumn('offers_count', fn($item) => $item->offers->count())
            ->addColumn('status', function ($item) {
                $badges = [
                    'open' => 'success',
                    'closed' => 'primary',
                    'canceled' => 'danger',
                ];
                $class = $badges[$item->status] ?? 'secondary';
                return '<span class="badge bg-' . $class . '">' . ucfirst($item->status) . '</span>';
            })
            ->addColumn('timeline', fn($item) => $item->timeline ? $item->timeline->format('Y-m-d') : 'N/A')
            ->addColumn('created_at', fn($item) => $item->created_at->format('Y-m-d H:i'))
            ->addColumn('action', function ($item) {
                $url = route('admin.purchase-requests.offers', $item->id);
                return '<a href="' . $url . '" class="btn btn-sm btn-success" title="View Offers"><i class="fa fa-eye"></i></a>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function offers($id)
    {
        // apply permissions
        abort_if(!hasPermission('view purchase requests'), 403, __('You are not authorized to view purchase requests'));
        $request = Request::with(['categories', 'clinic'])->findOrFail($id);
        return view('backend.dashboards.admin.pages.purchase-requests.offers', compact('request'));
    }

    public function offersData($id)
    {
        // apply permissions
        abort_if(!hasPermission('view purchase requests'), 403, __('You are not authorized to view purchase requests'));
        $offers = Offer::with(['supplier'])
            ->where('request_id', $id)
            ->latest();

        return datatables()->of($offers)
            ->addColumn('supplier_name', fn($item) => $item->supplier->name ?? 'N/A')
            ->addColumn('price', fn($item) => number_format($item->price, 2))
            ->addColumn('discount', fn($item) => $item->discount ? number_format($item->discount, 2) : '0.00')
            ->addColumn('final_price', fn($item) => number_format($item->final_price, 2))
            ->addColumn('delivery_time', fn($item) => optional($item->delivery_time)->format('Y-m-d'))
            ->addColumn('status', function ($item) {
                $badges = [
                    'pending' => 'warning',
                    'accepted' => 'success',
                    'declined' => 'danger',
                ];
                $class = $badges[$item->status] ?? 'secondary';
                return '<span class="badge bg-' . $class . '">' . ucfirst($item->status) . '</span>';
            })
            ->addColumn('created_at', fn($item) => $item->created_at->format('Y-m-d H:i'))
            ->addColumn('action', function ($item) {
                return '';
            })
            ->rawColumns(['status'])
            ->make(true);
    }
}