<?php

namespace App\Http\Controllers\Backend\Dashboards\Supplier;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\SupplierPayoutProfile;
use App\Models\SupplierPayoutRequest;
use App\Notifications\Admin\SupplierPayoutRequestedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class PayoutController extends Controller
{
    /**
     * Display payout dashboard for supplier
     */
    public function index()
    {
        $supplier = auth('supplier')->user()->supplier;

        // Get payout profile
        $payoutProfile = $supplier->payoutProfile;

        // Get eligible orders and amount
        $eligibleOrders = $supplier->getEligibleOrdersForPayout();
        $eligibleAmount = (float) $eligibleOrders->sum('subtotal');

        // Get payout history
        $payoutRequests = $supplier->payoutRequests()
            ->with(['paidByAdmin', 'orderSuppliers.order'])
            ->latest()
            ->paginate(10);

        // Check if can request payout
        $cooldownInfo = SupplierPayoutRequest::canSupplierRequestPayout($supplier->id);
        $minimumAmount = SupplierPayoutRequest::getMinimumPayoutAmount();
        $cooldownWeeks = SupplierPayoutRequest::getPayoutCooldownWeeks();

        // Check if has pending request
        $hasPendingRequest = $supplier->payoutRequests()
            ->where('status', 'pending')
            ->exists();

        $payoutMethods = SupplierPayoutProfile::payoutMethods();

        return view('backend.dashboards.supplier.pages.payouts.index', compact(
            'supplier',
            'payoutProfile',
            'eligibleOrders',
            'eligibleAmount',
            'payoutRequests',
            'cooldownInfo',
            'minimumAmount',
            'cooldownWeeks',
            'hasPendingRequest',
            'payoutMethods'
        ));
    }

    /**
     * Store payout profile
     */
    public function storeProfile(Request $request)
    {
        $supplier = auth('supplier')->user()->supplier;

        $validated = $request->validate([
            'payout_method' => 'required|string|max:100',
            'payout_details' => 'required|string|max:2000',
            'notes' => 'nullable|string|max:2000',
        ]);

        SupplierPayoutProfile::updateOrCreate(
            ['supplier_id' => $supplier->id],
            $validated
        );

        return redirect()->back()->with('success', __('Payout profile saved successfully.'));
    }

    /**
     * Request a payout
     */
    public function requestPayout(Request $request)
    {
        $supplier = auth('supplier')->user()->supplier;
        $minimumAmount = SupplierPayoutRequest::getMinimumPayoutAmount();

        // Get eligible orders and amount
        $eligibleOrders = $supplier->getEligibleOrdersForPayout();
        $eligibleAmount = (float) $eligibleOrders->sum('subtotal');

        // Check minimum amount
        if ($eligibleAmount < $minimumAmount) {
            return redirect()->back()->with('error', __('Minimum payout amount is :amount EGP.', ['amount' => $minimumAmount]));
        }

        // Check cooldown
        $cooldownInfo = SupplierPayoutRequest::canSupplierRequestPayout($supplier->id);
        if (!$cooldownInfo['can_request']) {
            return redirect()->back()->with('error', __('You can request a payout in :days days.', [
                'days' => $cooldownInfo['days_remaining']
            ]));
        }

        // Check for pending request
        $hasPendingRequest = $supplier->payoutRequests()
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            return redirect()->back()->with('error', __('You already have a pending payout request.'));
        }

        // Check if payout profile exists
        $payoutProfile = $supplier->payoutProfile;
        if (!$payoutProfile) {
            return redirect()->back()->with('error', __('Please set up your payout profile first.'));
        }

        $validated = $request->validate([
            'supplier_note' => 'nullable|string|max:2000',
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:order_suppliers,id',
        ]);

        // Verify orders belong to supplier
        $orderSupplierIds = collect($validated['order_ids']);
        $validOrders = $eligibleOrders->whereIn('id', $orderSupplierIds);

        if ($validOrders->count() !== $orderSupplierIds->count()) {
            return redirect()->back()->with('error', __('Some selected orders are not eligible for payout.'));
        }

        $totalAmount = (float) $validOrders->sum('subtotal');

        if ($totalAmount < $minimumAmount) {
            return redirect()->back()->with('error', __('Selected orders total is less than minimum payout amount.'));
        }

        // Create payout request
        $payoutRequest = DB::transaction(function () use ($supplier, $payoutProfile, $validated, $validOrders, $totalAmount) {
            $payoutRequest = SupplierPayoutRequest::create([
                'supplier_id' => $supplier->id,
                'amount' => $totalAmount,
                'payout_method' => $payoutProfile->payout_method,
                'payout_details' => $payoutProfile->payout_details,
                'supplier_note' => $validated['supplier_note'] ?? null,
                'status' => 'pending',
            ]);

            // Attach orders
            foreach ($validOrders as $orderSupplier) {
                $payoutRequest->orderSuppliers()->attach($orderSupplier->id, [
                    'amount' => $orderSupplier->subtotal,
                ]);
            }

            return $payoutRequest;
        });

        // Notify admins
        $admins = Admin::all();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new SupplierPayoutRequestedNotification($payoutRequest));
        }

        return redirect()->back()->with('success', __('Payout request submitted successfully. You will be notified within 2-5 business days.'));
    }

    /**
     * Show payout request details
     */
    public function show($id)
    {
        $supplier = auth('supplier')->user()->supplier;

        $payoutRequest = $supplier->payoutRequests()
            ->with(['paidByAdmin', 'orderSuppliers.order'])
            ->findOrFail($id);

        return view('backend.dashboards.supplier.pages.payouts.show', compact('payoutRequest'));
    }
}
