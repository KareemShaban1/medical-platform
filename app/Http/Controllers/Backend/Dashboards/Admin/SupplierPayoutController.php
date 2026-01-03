<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayoutRequest;
use App\Notifications\Supplier\SupplierPayoutPaidNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierPayoutController extends Controller
{
    /**
     * Display all supplier payout requests
     */
    public function index(Request $request)
    {
        abort_if(!hasPermission('view supplier payouts'), 403, __('You are not authorized to view supplier payouts'));

        $query = SupplierPayoutRequest::with(['supplier', 'paidByAdmin'])
            ->latest();

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $requests = $query->paginate(20)->withQueryString();

        $statusCounts = [
            'all' => SupplierPayoutRequest::count(),
            'pending' => SupplierPayoutRequest::where('status', 'pending')->count(),
            'approved' => SupplierPayoutRequest::where('status', 'approved')->count(),
            'paid' => SupplierPayoutRequest::where('status', 'paid')->count(),
            'rejected' => SupplierPayoutRequest::where('status', 'rejected')->count(),
        ];

        return view('backend.dashboards.admin.pages.supplier-payouts.index', compact('requests', 'statusCounts'));
    }

    /**
     * Show payout request details with orders
     */
    public function show($id)
    {
        abort_if(!hasPermission('view supplier payouts'), 403, __('You are not authorized to view supplier payouts'));

        $payoutRequest = SupplierPayoutRequest::with([
            'supplier',
            'paidByAdmin',
            'orderSuppliers.order.clinic',
            'orderSuppliers.order.items',
        ])->findOrFail($id);

        return view('backend.dashboards.admin.pages.supplier-payouts.show', compact('payoutRequest'));
    }

    /**
     * Mark payout as paid
     */
    public function markPaid(Request $request, $id)
    {
        abort_if(!hasPermission('update supplier payouts'), 403, __('You are not authorized to update supplier payouts'));

        $payoutRequest = SupplierPayoutRequest::findOrFail($id);

        if (!$payoutRequest->canBeMarkedAsPaid()) {
            return redirect()->back()->with('error', __('This payout request cannot be marked as paid.'));
        }

        $validated = $request->validate([
            'admin_note' => 'nullable|string|max:2000',
            'proof_images' => 'nullable|array',
            'proof_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        DB::transaction(function () use ($payoutRequest, $validated, $request) {
            $payoutRequest->update([
                'status' => 'paid',
                'paid_at' => now(),
                'paid_by_admin_id' => auth('admin')->id(),
                'admin_note' => $validated['admin_note'] ?? null,
            ]);

            if ($request->hasFile('proof_images')) {
                foreach ($request->file('proof_images', []) as $image) {
                    $payoutRequest
                        ->addMedia($image)
                        ->toMediaCollection('payout_proofs');
                }
            }

            // Notify supplier users
            if ($payoutRequest->supplier && $payoutRequest->supplier->supplierUsers) {
                foreach ($payoutRequest->supplier->supplierUsers as $user) {
                    $user->notify(new SupplierPayoutPaidNotification($payoutRequest));
                }
            }
        });

        return redirect()->back()->with('success', __('Payout marked as paid successfully.'));
    }

    /**
     * Approve payout request
     */
    public function approve($id)
    {
        abort_if(!hasPermission('update supplier payouts'), 403, __('You are not authorized to update supplier payouts'));

        $payoutRequest = SupplierPayoutRequest::findOrFail($id);

        if ($payoutRequest->status !== 'pending') {
            return redirect()->back()->with('error', __('Only pending requests can be approved.'));
        }

        $payoutRequest->update(['status' => 'approved']);

        return redirect()->back()->with('success', __('Payout request approved.'));
    }

    /**
     * Reject payout request
     */
    public function reject(Request $request, $id)
    {
        abort_if(!hasPermission('update supplier payouts'), 403, __('You are not authorized to update supplier payouts'));

        $payoutRequest = SupplierPayoutRequest::findOrFail($id);

        if (!$payoutRequest->canBeRejected()) {
            return redirect()->back()->with('error', __('This payout request cannot be rejected.'));
        }

        $validated = $request->validate([
            'admin_note' => 'required|string|max:2000',
        ]);

        $payoutRequest->update([
            'status' => 'rejected',
            'admin_note' => $validated['admin_note'],
        ]);

        return redirect()->back()->with('success', __('Payout request rejected.'));
    }

    /**
     * Get data for DataTables
     */
    public function data()
    {
        abort_if(!hasPermission('view supplier payouts'), 403);

        $requests = SupplierPayoutRequest::with(['supplier', 'paidByAdmin']);

        return datatables()->of($requests)
            ->addColumn('supplier_name', fn($item) => $item->supplier->name ?? 'N/A')
            ->addColumn('status_badge', fn($item) => $this->statusBadge($item))
            ->addColumn('formatted_amount', fn($item) => number_format($item->amount, 2) . ' ' . __('EGP'))
            ->addColumn('formatted_date', fn($item) => $item->created_at->format('Y-m-d H:i'))
            ->addColumn('action', fn($item) => $this->actionButtons($item))
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    private function statusBadge($item): string
    {
        return '<span class="badge ' . $item->status_badge_class . '">' . $item->status_label . '</span>';
    }

    private function actionButtons($item): string
    {
        $showUrl = route('admin.supplier-payouts.show', $item->id);
        return '<a href="' . $showUrl . '" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>';
    }
}
