<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\CreateRequestRequest;
use App\Http\Requests\Clinic\UpdateRequestRequest;
use App\Interfaces\Clinic\RequestRepositoryInterface;
use App\PaymentGateways\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestController extends Controller
{
    protected $requestRepository;

    protected $paymentGatewayManager;

    public function __construct(
        RequestRepositoryInterface $requestRepository,
        PaymentGatewayManager $paymentGatewayManager
    ) {
        $this->requestRepository = $requestRepository;
        $this->paymentGatewayManager = $paymentGatewayManager;
    }

    public function index()
    {
        // apply permissions
        abort_if(! hasPermission('view purchase requests'), 403, __('You are not authorized to view purchase requests'));

        return view('backend.dashboards.clinic.pages.requests.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(! hasPermission('view purchase requests'), 403, __('You are not authorized to view purchase requests'));

        return $this->requestRepository->data();
    }

    public function create()
    {
        // apply permissions
        abort_if(! hasPermission('create purchase request'), 403, __('You are not authorized to create purchase request'));

        $categories = $this->requestRepository->getCategories();

        return view('backend.dashboards.clinic.pages.requests.create', compact('categories'));
    }

    public function store(CreateRequestRequest $request)
    {

        // apply permissions
        abort_if(! hasPermission('create purchase request'), 403, __('You are not authorized to create purchase request'));

        try {
            $this->requestRepository->store($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Request created successfully and sent to suppliers.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view purchase requests'), 403, __('You are not authorized to show purchase request'));

        try {
            $request = $this->requestRepository->show($id);

            return view('backend.dashboards.clinic.pages.requests.show', compact('request'));
        } catch (\Exception $e) {
            return redirect()->route('clinic.requests.index')->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        // apply permissions
        abort_if(!hasPermission('update purchase request'), 403, __('You are not authorized to update purchase request'));

        try {
            $request = $this->requestRepository->show($id);
            $categories = $this->requestRepository->getCategories();

            return view('backend.dashboards.clinic.pages.requests.edit', compact('request', 'categories'));
        } catch (\Exception $e) {
            return redirect()->route('clinic.requests.index')->with('error', $e->getMessage());
        }
    }

    public function update(UpdateRequestRequest $request, $id)
    {

        // apply permissions
        abort_if(!hasPermission('update purchase request'), 403, __('You are not authorized to update purchase request'));

        try {
            $this->requestRepository->update($request->validated(), $id);

            return response()->json([
                'success' => true,
                'message' => 'Request updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete purchase request'), 403, __('You are not authorized to delete purchase request'));

        try {
            $this->requestRepository->destroy($id);

            return response()->json([
                'success' => true,
                'message' => 'Request deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function acceptOffer(Request $request, $requestId)
    {
        // apply permissions
        abort_if(!hasPermission('accept offer'), 403, __('You are not authorized to accept offer'));

        try {
            $offerId = $request->input('offer_id');
            $this->requestRepository->acceptOffer($requestId, $offerId);

            return response()->json([
                'success' => true,
                'message' => 'Offer accepted successfully. Request has been closed.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function cancel($id)
    {
        // apply permissions
        abort_if(!hasPermission('cancel request'), 403, __('You are not authorized to cancel request'));

        try {
            $this->requestRepository->cancelRequest($id);

            return response()->json([
                'success' => true,
                'message' => 'Request canceled successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function getCategories()
    {
        try {
            $categories = $this->requestRepository->getCategories();

            return response()->json([
                'success' => true,
                'data' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function invoice($offerId)
    {

        // apply permissions
        abort_if(!hasPermission('view invoices'), 403, __('You are not authorized to view invoice'));

        try {
            $offer = \App\Models\Offer::with(['request.clinic', 'supplier'])
                ->accepted()
                ->findOrFail($offerId);

            // Ensure the authenticated clinic owns the request
            if ($offer->request->clinic_id !== auth('clinic')->user()->clinic_id) {
                abort(403);
            }

            return view('backend.dashboards.clinic.pages.requests.invoice', [
                'offer' => $offer,
                'requestModel' => $offer->request,
                'clinic' => $offer->request->clinic,
                'supplier' => $offer->supplier,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function processOfferPayment(Request $request, $requestId)
    {
        try {
            // apply permissions
            abort_if(!hasPermission('process offer payment'), 403, __('You are not authorized to process offer payment'));

            $request->validate([
                'offer_id' => 'required|exists:offers,id',
                'payment_gateway' => 'required|string|in:cod,paymob',
                'pay_method' => 'nullable|string|in:card,wallet',
                'wallet_phone' => 'nullable|string',
            ]);

            $offer = \App\Models\Offer::where('request_id', $requestId)
                ->findOrFail($request->input('offer_id'));

            // Ensure the authenticated clinic owns the request
            $requestModel = \App\Models\Request::mine()->findOrFail($requestId);
            if ($requestModel->clinic_id !== auth('clinic')->user()->clinic_id) {
                abort(403);
            }

            if (! $offer->canBeAccepted()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Offer cannot be accepted',
                ], 400);
            }

            $gatewayName = $request->input('payment_gateway');
            $gateway = $this->paymentGatewayManager->gateway($gatewayName);

            if (! $gateway->isEnabled()) {
                return response()->json([
                    'success' => false,
                    'message' => "Payment gateway '{$gatewayName}' is not enabled.",
                ], 400);
            }

            // Calculate total amount
            $totalAmount = $offer->price - ($offer->discount ?? 0) + ($offer->shipping ?? 0) + ($offer->tax ?? 0);

            // For COD, accept offer directly
            if ($gatewayName === 'cod') {
                $this->requestRepository->acceptOffer($requestId, $offer->id, [
                    'payment_method' => 0, // COD
                    'payment_status' => 'pending',
                    'payment_gateway' => 'cod',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Offer accepted successfully. Payment will be collected on delivery.',
                ]);
            }

            // For online payment (Paymob)
            $payMethod = $request->input('pay_method', 'card');
            $walletPhone = $request->input('wallet_phone');

            if ($gatewayName === 'paymob' && $payMethod === 'wallet' && empty($walletPhone)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet phone is required for wallet payments',
                ], 422);
            }

            // Get clinic user info
            $clinicUser = auth('clinic')->user();
            $clinic = $clinicUser->clinic;

            $nameParts = explode(' ', $clinicUser->name ?? 'Clinic', 2);
            $firstName = $nameParts[0] ?? 'Clinic';
            $lastName = $nameParts[1] ?? 'User';

            // Prepare payment data
            // Generate unique order number to avoid Paymob duplicate errors on retries
            $uniqueOrderNumber = 'OFFER-' . $offer->id . '-' . time() . '-' . uniqid();

            $paymentData = [
                'amount' => $totalAmount,
                'order_id' => null, // Will be set after offer acceptance
                'order_number' => $uniqueOrderNumber,
                'currency' => 'EGP',
                'method' => $payMethod,
                'wallet_phone' => $walletPhone,
                'customer' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $clinicUser->email ?? 'clinic@example.com',
                    'phone' => $clinicUser->phone ?? $clinic->phone ?? '01000000000',
                    'city' => 'Cairo',
                    'country' => 'EG',
                    'street' => $clinic->address ?? 'NA',
                    'building' => 'NA',
                    'apartment' => 'NA',
                    'floor' => 'NA',
                    'postal_code' => 'NA',
                    'state' => 'NA',
                ],
            ];

            // Process payment
            $paymentResponse = $gateway->processPayment($paymentData);

            if (! $paymentResponse->success) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResponse->message,
                ], 400);
            }

            // Store offer ID and payment info in session for callback
            session()->put('offer_payment_offer_id', $offer->id);
            session()->put('offer_payment_request_id', $requestId);
            session()->put('offer_payment_gateway', $gatewayName);
            session()->put('offer_payment_transaction_id', $paymentResponse->transactionId);
            session()->put('payment_order_number', $uniqueOrderNumber); // For PaymentController routing

            // Also store in cache for reliability (2 hours)
            \Illuminate\Support\Facades\Cache::put(
                'pending_offer_' . $uniqueOrderNumber,
                [
                    'offer_id' => $offer->id,
                    'request_id' => $requestId,
                    'gateway' => $gatewayName,
                    'transaction_id' => $paymentResponse->transactionId,
                    'order_number' => $uniqueOrderNumber,
                ],
                now()->addHours(2)
            );

            return response()->json([
                'success' => true,
                'redirect_url' => $paymentResponse->redirectUrl,
                'message' => 'Redirecting to payment gateway...',
            ]);
        } catch (\Exception $e) {
            Log::error('Offer payment processing error: ' . $e->getMessage(), [
                'request_id' => $requestId,
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function offerPaymentReturn(Request $request, $requestId)
    {
        try {
            // apply permissions
            abort_if(!hasPermission('process offer payment'), 403, __('You are not authorized to process offer payment'));

            $offerId = session()->get('offer_payment_offer_id');
            $gatewayName = session()->get('offer_payment_gateway', 'paymob');

            if (! $offerId) {
                return redirect()->route('clinic.requests.show', $requestId)
                    ->with('error', 'Payment session expired. Please try again.');
            }

            $gateway = $this->paymentGatewayManager->gateway($gatewayName);
            $paymentData = $request->all();

            // For Paymob, check if payment was successful
            $success = $request->get('success') === 'true'
                || $request->get('success') === true
                || $request->get('success') === '1'
                || $request->get('txn_response_code') === 'APPROVED';

            if ($success) {
                // Try to verify payment
                try {
                    $paymentResponse = $gateway->verifyPayment($paymentData);

                    if ($paymentResponse->success) {
                        // Accept the offer with payment info
                        $this->requestRepository->acceptOffer($requestId, $offerId, [
                            'payment_method' => 1, // Online
                            'payment_status' => 'paid',
                            'payment_gateway' => $gatewayName,
                            'transaction_id' => $paymentResponse->transactionId ?? $request->get('id'),
                        ]);

                        // Clear session
                        session()->forget([
                            'offer_payment_offer_id',
                            'offer_payment_request_id',
                            'offer_payment_gateway',
                            'offer_payment_transaction_id',
                        ]);

                        return redirect()->route('clinic.requests.show', $requestId)
                            ->with('success', 'Payment successful and offer accepted!');
                    }
                } catch (\Exception $e) {
                    Log::warning('Payment verification failed in return handler', [
                        'error' => $e->getMessage(),
                        'success_flag' => $success,
                    ]);
                }
            }

            // If payment failed or verification failed, return to show page with error
            session()->forget([
                'offer_payment_offer_id',
                'offer_payment_request_id',
                'offer_payment_gateway',
                'offer_payment_transaction_id',
            ]);

            return redirect()->route('clinic.requests.show', $requestId)
                ->with('error', 'Payment was not successful. Please try again.');
        } catch (\Exception $e) {
            Log::error('Offer payment return error: ' . $e->getMessage());

            return redirect()->route('clinic.requests.show', $requestId)
                ->with('error', 'Payment processing error. Please contact support.');
        }
    }

    public function offerPaymentCallback(Request $request, $requestId)
    {
        try {
            // apply permissions
            abort_if(!hasPermission('process offer payment'), 403, __('You are not authorized to process offer payment'));

            $offerId = session()->get('offer_payment_offer_id');
            $gatewayName = session()->get('offer_payment_gateway', 'paymob');

            if (! $offerId) {
                Log::warning('Offer payment callback: No offer ID in session');

                return response()->json(['success' => false, 'message' => 'Session expired'], 400);
            }

            $gateway = $this->paymentGatewayManager->gateway($gatewayName);
            $paymentData = $request->all();

            // Verify payment
            $paymentResponse = $gateway->verifyPayment($paymentData);

            Log::info('Payment response: ' . json_encode($paymentResponse));

            if ($paymentResponse->success) {
                // Accept the offer with payment info
                $this->requestRepository->acceptOffer($requestId, $offerId, [
                    'payment_method' => 1, // Online
                    'payment_status' => 'paid',
                    'payment_gateway' => $gatewayName,
                    'transaction_id' => $paymentResponse->transactionId,
                ]);

                // Clear session
                session()->forget([
                    'offer_payment_offer_id',
                    'offer_payment_request_id',
                    'offer_payment_gateway',
                    'offer_payment_transaction_id',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful and offer accepted',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResponse->message ?? 'Payment verification failed',
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Offer payment callback error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment callback processing error',
            ], 500);
        }
    }
}
