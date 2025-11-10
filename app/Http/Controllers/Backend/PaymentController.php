<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Offer;
use App\Models\Order;
use App\PaymentGateways\PaymentGatewayManager;
use App\Repository\Clinic\RequestRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentGatewayManager;

    protected $requestRepository;

    public function __construct(PaymentGatewayManager $paymentGatewayManager, RequestRepository $requestRepository)
    {
        $this->paymentGatewayManager = $paymentGatewayManager;
        $this->requestRepository = $requestRepository;
    }

    /**
     * Extract error details from payment response
     */
    protected function extractPaymentError(array $paymentData, $gatewayInstance = null): array
    {
        $errorDetails = [
            'error_occurred' => false,
            'error_message' => null,
            'error_code' => null,
            'error_type' => null,
            'raw_data' => [],
        ];

        // Check for error_occured field (Paymob typo)
        $errorOccurred = $paymentData['error_occured'] ?? $paymentData['error_occurred'] ?? false;
        if ($errorOccurred === 'true' || $errorOccurred === true || $errorOccurred === '1') {
            $errorDetails['error_occurred'] = true;
        }

        // Extract error message from various possible locations
        $errorMessage = null;
        
        // Check Paymob specific error fields
        if (isset($paymentData['data']['message'])) {
            $errorMessage = $paymentData['data']['message'];
        } elseif (isset($paymentData['obj']['data']['message'])) {
            $errorMessage = $paymentData['obj']['data']['message'];
        } elseif (isset($paymentData['message'])) {
            $errorMessage = $paymentData['message'];
        } elseif (isset($paymentData['error_message'])) {
            $errorMessage = $paymentData['error_message'];
        }

        // Check for error codes
        $errorCode = $paymentData['error_code'] 
            ?? $paymentData['data']['error_code'] 
            ?? $paymentData['obj']['data']['error_code'] 
            ?? null;

        // Check transaction status for clues
        $status = $paymentData['status'] 
            ?? $paymentData['obj']['status'] 
            ?? null;

        // Check if card was declined
        $isDeclined = isset($paymentData['is_voided']) && ($paymentData['is_voided'] === 'true' || $paymentData['is_voided'] === true)
            || isset($paymentData['is_refunded']) && ($paymentData['is_refunded'] === 'true' || $paymentData['is_refunded'] === true);

        // Try to get error from verifyPayment if gateway instance is provided
        if ($gatewayInstance && !$errorMessage) {
            try {
                $paymentResponse = $gatewayInstance->verifyPayment($paymentData);
                if (!$paymentResponse->success && $paymentResponse->message) {
                    $errorMessage = $paymentResponse->message;
                }
            } catch (\Exception $e) {
                // If verification throws exception, that's also an error
                if (!$errorMessage) {
                    $errorMessage = $e->getMessage();
                }
            }
        }

        // Determine error type
        $errorType = 'unknown';
        if ($isDeclined) {
            $errorType = 'declined';
        } elseif ($errorCode) {
            $errorType = 'error_code';
        } elseif ($errorOccurred) {
            $errorType = 'payment_error';
        } elseif ($status === 'failed' || $status === 'declined') {
            $errorType = 'failed';
        }

        $errorDetails['error_message'] = $errorMessage;
        $errorDetails['error_code'] = $errorCode;
        $errorDetails['error_type'] = $errorType;
        $errorDetails['raw_data'] = [
            'success' => $paymentData['success'] ?? null,
            'error_occured' => $errorOccurred,
            'status' => $status,
            'is_voided' => $paymentData['is_voided'] ?? null,
            'is_refunded' => $paymentData['is_refunded'] ?? null,
            'pending' => $paymentData['pending'] ?? null,
        ];

        return $errorDetails;
    }

    /**
     * Handle payment gateway return (user redirect after payment)
     */
    public function paymentReturn(Request $request, string $gateway)
    {
        try {
            // Get all request data (query params or POST)
            $paymentData = $request->all();

            Log::info('Payment return data', [
                'payment_data' => $paymentData,
            ]);

            // For Paymob, get order ID from query or session
            // $orderId = $request->get('order_id') ?? session()->get('payment_order_id');
            $orderNumber = $request->get('merchant_order_id') ?? session()->get('payment_order_number');

            Log::info('Payment return order id', [
                'order_number' => $orderNumber,
            ]);

            if (! $orderNumber) {
                return redirect()->route('checkout.failed')
                    ->with('error', 'Order not found');
            }

            // Check if this is an order (ORD-) or offer (OFFER-) payment
            if (strpos($orderNumber, 'ORD-') === 0) {
                // Handle order flow
                return $this->handleOrderPayment($request, $gateway, $orderNumber, $paymentData);
            } elseif (strpos($orderNumber, 'OFFER-') === 0) {
                // Handle offer flow
                return $this->handleOfferPayment($request, $gateway, $orderNumber, $paymentData);
            } else {
                // Try to find as order (backward compatibility)
                $order = Order::where('number', $orderNumber)->first();
                if ($order) {
                    return $this->handleOrderPayment($request, $gateway, $orderNumber, $paymentData);
                }

                return redirect()->route('checkout.failed')
                    ->with('error', 'Invalid order number format');
            }
        } catch (\Exception $e) {
            Log::error('Payment return error: '.$e->getMessage(), [
                'gateway' => $gateway,
                'request' => $request->all(),
            ]);

            return redirect()->route('checkout.failed')
                ->with('error', 'Payment verification failed');
        }
    }

    /**
     * Handle order payment return
     */
    protected function handleOrderPayment(Request $request, string $gateway, string $orderNumber, array $paymentData)
    {
        $order = Order::where('number', $orderNumber)->first();

        Log::info('Payment return order', [
            'order' => $order,
        ]);

        if (! $order) {
            return redirect()->route('checkout.failed')
                ->with('error', 'Order not found');
        }

        // Verify payment with gateway
        $gatewayInstance = $this->paymentGatewayManager->gateway($gateway);

        // For Paymob, check if payment was successful
        // Paymob can return data via query params (redirect) or POST body
        $successValue = $paymentData['success'] ?? $request->get('success');
        $errorOccurred = $paymentData['error_occured'] ?? $paymentData['error_occurred'] ?? $request->get('error_occured') ?? $request->get('error_occurred');
        
        // Check for various possible success indicators
        $success = $successValue === 'true'
            || $successValue === true
            || $successValue === '1'
            || $request->get('txn_response_code') === 'APPROVED';
        
        // If error_occured is true, payment definitely failed
        if ($errorOccurred === 'true' || $errorOccurred === true || $errorOccurred === '1') {
            $success = false;
        }

        if ($gateway === 'paymob') {
            // Get transaction ID from various possible fields
            $transactionId = $paymentData['id'] 
                ?? $paymentData['transaction_id']
                ?? $request->get('id')
                ?? $request->get('transaction_id')
                ?? $request->get('obj.id')
                ?? null;

            // Log the request for debugging
            Log::info('Paymob return request', [
                'all_params' => $request->all(),
                'payment_data' => $paymentData,
                'success_value' => $successValue,
                'success' => $success,
                'error_occured' => $errorOccurred,
                'transaction_id' => $transactionId,
                'order_number' => $orderNumber,
            ]);

            // Try to verify payment if we have the data
            try {
                $paymentResponse = $gatewayInstance->verifyPayment($paymentData);

                if ($paymentResponse->success) {
                    $order->update([
                        'payment_status' => 'paid',
                        'transaction_id' => $paymentResponse->transactionId ?? $transactionId,
                    ]);

                    // Clear cart after successful payment confirmation
                    $cart = Cart::where('clinic_user_id', $order->clinic_user_id)
                        ->where('clinic_id', $order->clinic_id)
                        ->first();
                    if ($cart) {
                        $cart->items()->delete();
                        $cart->calculateTotals();
                    }

                    session()->forget('payment_order_number');

                    return redirect()->route('checkout.success', ['order' => $order->id])
                        ->with('success', 'Payment processed successfully');
                }
            } catch (\Exception $e) {
                Log::warning('Paymob verification failed, but checking success flag', [
                    'error' => $e->getMessage(),
                    'success_flag' => $success,
                ]);
            }

            // If verification failed but we have success flag, still update order
            if ($success && $transactionId) {
                $order->update([
                    'payment_status' => 'paid',
                    'transaction_id' => $transactionId,
                ]);

                // Clear cart after successful payment confirmation
                $cart = Cart::where('clinic_user_id', $order->clinic_user_id)
                    ->where('clinic_id', $order->clinic_id)
                    ->first();
                if ($cart) {
                    $cart->items()->delete();
                    $cart->calculateTotals();
                }

                session()->forget('payment_order_number');

                return redirect()->route('checkout.success', ['order' => $order->id])
                    ->with('success', 'Payment processed successfully');
            }

            // If payment failed, extract error details and restore stock
            $errorDetails = $this->extractPaymentError($paymentData, $gatewayInstance);
            
            // Log comprehensive error details for debugging
            Log::error('Payment failed (order)', [
                'order_id' => $order->id,
                'order_number' => $orderNumber,
                'error_details' => $errorDetails,
                'payment_data_summary' => [
                    'success' => $paymentData['success'] ?? null,
                    'error_occured' => $paymentData['error_occured'] ?? null,
                    'transaction_id' => $transactionId,
                    'amount_cents' => $paymentData['amount_cents'] ?? null,
                ],
            ]);
            
            // Build user-friendly error message
            $errorMessage = 'Payment failed. Please try again.';
            
            if ($errorDetails['error_message']) {
                $errorMessage = $errorDetails['error_message'];
            } elseif ($errorDetails['error_occurred']) {
                if ($errorDetails['error_type'] === 'declined') {
                    $errorMessage = 'Your payment was declined. Please check your card details or try a different payment method.';
                } elseif ($errorDetails['error_code']) {
                    $errorMessage = 'Payment failed (Error Code: ' . $errorDetails['error_code'] . '). Please try again or contact support.';
                } else {
                    $errorMessage = 'Payment failed due to an error. Please try again or contact support.';
                }
            }
            
            $order->update([
                'payment_status' => 'failed',
            ]);

            // Restore stock for failed payment
            foreach ($order->items as $orderItem) {
                $product = $orderItem->product;
                if ($product) {
                    $product->increment('stock', $orderItem->quantity);
                }
            }

            session()->forget('payment_order_number');

            return redirect()->route('checkout.failed')
                ->with('error', $errorMessage);
        }

        // For other gateways, use verifyPayment
        $paymentResponse = $gatewayInstance->verifyPayment($paymentData);

        if ($paymentResponse->success) {
            $order->update([
                'payment_status' => 'paid',
                'transaction_id' => $paymentResponse->transactionId,
            ]);

            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('success', 'Payment processed successfully');
        }

        // Extract error details for logging
        $errorDetails = $this->extractPaymentError($paymentData, $gatewayInstance);
        
        // Log comprehensive error details
        Log::error('Payment failed (order - other gateway)', [
            'order_id' => $order->id,
            'order_number' => $orderNumber,
            'gateway' => $gateway,
            'error_details' => $errorDetails,
            'payment_response' => [
                'success' => $paymentResponse->success,
                'message' => $paymentResponse->message,
                'transaction_id' => $paymentResponse->transactionId,
            ],
        ]);
        
        // Build error message
        $errorMessage = $paymentResponse->message 
            ?? $errorDetails['error_message'] 
            ?? 'Payment failed. Please try again.';
        
        $order->update([
            'payment_status' => 'failed',
        ]);

        return redirect()->route('checkout.failed')
            ->with('error', $errorMessage);
    }

    /**
     * Handle offer payment return
     */
    protected function handleOfferPayment(Request $request, string $gateway, string $orderNumber, array $paymentData)
    {
        // Extract offer ID from order number (OFFER-{id}-{timestamp}-{uniqid})
        // Format: OFFER-{offer_id}-{timestamp}-{uniqid}
        $parts = explode('-', $orderNumber);
        if (count($parts) < 2 || $parts[0] !== 'OFFER') {
            Log::error('Invalid offer order number format', [
                'order_number' => $orderNumber,
            ]);
            return redirect()->route('clinic.requests.index')
                ->with('error', 'Invalid order number format');
        }
        
        $offerId = $parts[1]; // The offer ID is the second part
        $offer = Offer::find($offerId);

        Log::info('Payment return offer', [
            'offer_id' => $offerId,
            'offer' => $offer,
        ]);

        if (! $offer) {
            Log::error('Offer not found for payment return', [
                'offer_id' => $offerId,
                'order_number' => $orderNumber,
            ]);

            return redirect()->route('clinic.requests.index')
                ->with('error', 'Offer not found');
        }

        // Verify payment with gateway
        $gatewayInstance = $this->paymentGatewayManager->gateway($gateway);

        // For Paymob, check if payment was successful
        // Paymob can return data via query params (redirect) or POST body
        $successValue = $paymentData['success'] ?? $request->get('success');
        $errorOccurred = $paymentData['error_occured'] ?? $paymentData['error_occurred'] ?? $request->get('error_occured') ?? $request->get('error_occurred');
        
        // Check for various possible success indicators
        $success = $successValue === 'true'
            || $successValue === true
            || $successValue === '1'
            || $request->get('txn_response_code') === 'APPROVED';
        
        // If error_occured is true, payment definitely failed
        if ($errorOccurred === 'true' || $errorOccurred === true || $errorOccurred === '1') {
            $success = false;
        }

        if ($gateway === 'paymob') {
            // Get transaction ID from various possible fields
            $transactionId = $paymentData['id'] 
                ?? $paymentData['transaction_id']
                ?? $request->get('id')
                ?? $request->get('transaction_id')
                ?? $request->get('obj.id')
                ?? null;

            // Log the request for debugging
            Log::info('Paymob return request (offer)', [
                'all_params' => $request->all(),
                'payment_data' => $paymentData,
                'success_value' => $successValue,
                'success' => $success,
                'error_occured' => $errorOccurred,
                'transaction_id' => $transactionId,
                'order_number' => $orderNumber,
                'offer_id' => $offerId,
            ]);

            // Try to verify payment if we have the data
            try {
                $paymentResponse = $gatewayInstance->verifyPayment($paymentData);

                if ($paymentResponse->success) {
                    // Accept offer and update payment status
                    $this->requestRepository->acceptOffer($offer->request_id, $offer->id, [
                        'payment_method' => 1, // Online payment
                        'payment_status' => 'paid',
                        'payment_gateway' => $gateway,
                        'transaction_id' => $paymentResponse->transactionId ?? $transactionId,
                    ]);

                    // Clear payment session data
                    session()->forget([
                        'payment_order_number',
                        'offer_payment_offer_id',
                        'offer_payment_request_id',
                        'offer_payment_gateway',
                        'offer_payment_transaction_id'
                    ]);

                    return redirect()->route('clinic.requests.show', ['id' => $offer->request_id])
                        ->with('success', 'Payment processed successfully and offer accepted');
                }
            } catch (\Exception $e) {
                Log::warning('Paymob verification failed (offer), but checking success flag', [
                    'error' => $e->getMessage(),
                    'success_flag' => $success,
                ]);
            }

            // If verification failed but we have success flag, still update offer
            if ($success && $transactionId) {
                // Accept offer and update payment status
                $this->requestRepository->acceptOffer($offer->request_id, $offer->id, [
                    'payment_method' => 1, // Online payment
                    'payment_status' => 'paid',
                    'payment_gateway' => $gateway,
                    'transaction_id' => $transactionId,
                ]);

                // Clear payment session data
                session()->forget([
                    'payment_order_number',
                    'offer_payment_offer_id',
                    'offer_payment_request_id',
                    'offer_payment_gateway',
                    'offer_payment_transaction_id'
                ]);

                return redirect()->route('clinic.requests.show', ['id' => $offer->request_id])
                    ->with('success', 'Payment processed successfully and offer accepted');
            }

            // If payment failed, extract error details and mark offer payment as failed
            $errorDetails = $this->extractPaymentError($paymentData, $gatewayInstance);
            
            // Log comprehensive error details for debugging
            Log::error('Payment failed (offer)', [
                'offer_id' => $offerId,
                'order_number' => $orderNumber,
                'error_details' => $errorDetails,
                'payment_data_summary' => [
                    'success' => $paymentData['success'] ?? null,
                    'error_occured' => $paymentData['error_occured'] ?? null,
                    'transaction_id' => $transactionId,
                    'amount_cents' => $paymentData['amount_cents'] ?? null,
                ],
            ]);
            
            // Build user-friendly error message
            $errorMessage = 'Payment failed. Please try again.';
            
            if ($errorDetails['error_message']) {
                $errorMessage = $errorDetails['error_message'];
            } elseif ($errorDetails['error_occurred']) {
                if ($errorDetails['error_type'] === 'declined') {
                    $errorMessage = 'Your payment was declined. Please check your card details or try a different payment method.';
                } elseif ($errorDetails['error_code']) {
                    $errorMessage = 'Payment failed (Error Code: ' . $errorDetails['error_code'] . '). Please try again or contact support.';
                } else {
                    $errorMessage = 'Payment failed due to an error. Please try again or contact support.';
                }
            }
            
            $offer->update([
                'payment_status' => 'failed',
            ]);

            // Clear payment session data
            session()->forget([
                'payment_order_number',
                'offer_payment_offer_id',
                'offer_payment_request_id',
                'offer_payment_gateway',
                'offer_payment_transaction_id'
            ]);

            return redirect()->route('clinic.requests.show', ['id' => $offer->request_id])
                ->with('error', $errorMessage);
        }

        // For other gateways, use verifyPayment
        $paymentResponse = $gatewayInstance->verifyPayment($paymentData);

        if ($paymentResponse->success) {
            // Accept offer and update payment status
            $this->requestRepository->acceptOffer($offer->request_id, $offer->id, [
                'payment_method' => 1, // Online payment
                'payment_status' => 'paid',
                'payment_gateway' => $gateway,
                'transaction_id' => $paymentResponse->transactionId,
            ]);

            // Clear payment session data
            session()->forget([
                'payment_order_number',
                'offer_payment_offer_id',
                'offer_payment_request_id',
                'offer_payment_gateway',
                'offer_payment_transaction_id'
            ]);

            return redirect()->route('clinic.requests.show', ['id' => $offer->request_id])
                ->with('success', 'Payment processed successfully and offer accepted');
        }

        // If payment failed, extract error details
        $errorDetails = $this->extractPaymentError($paymentData, $gatewayInstance);
        
        // Log comprehensive error details
        Log::error('Payment failed (offer - other gateway)', [
            'offer_id' => $offerId,
            'order_number' => $orderNumber,
            'gateway' => $gateway,
            'error_details' => $errorDetails,
            'payment_response' => [
                'success' => $paymentResponse->success,
                'message' => $paymentResponse->message,
                'transaction_id' => $paymentResponse->transactionId,
            ],
        ]);
        
        // Build error message
        $errorMessage = $paymentResponse->message 
            ?? $errorDetails['error_message'] 
            ?? 'Payment failed. Please try again.';
        
        // Mark offer payment as failed
        $offer->update([
            'payment_status' => 'failed',
        ]);

        // Clear payment session data
        session()->forget([
            'payment_order_number',
            'offer_payment_offer_id',
            'offer_payment_request_id',
            'offer_payment_gateway',
            'offer_payment_transaction_id'
        ]);

        return redirect()->route('clinic.requests.show', ['id' => $offer->request_id])
            ->with('error', $errorMessage);
    }

    /**
     * Handle payment gateway callback/webhook from the payment gateway
     */
    public function paymentCallback(Request $request, string $gateway)
    {
        try {
            $gatewayInstance = $this->paymentGatewayManager->gateway($gateway);
            $paymentResponse = $gatewayInstance->verifyPayment($request->all());

            // Find order by transaction ID or order number
            $order = null;
            if ($paymentResponse->transactionId) {
                $order = Order::where('transaction_id', $paymentResponse->transactionId)
                    ->orWhere('payment_gateway', $gateway)
                    ->first();
            }

            if ($order) {
                if ($paymentResponse->success) {
                    $order->update([
                        'payment_status' => 'paid',
                    ]);
                } else {
                    $order->update([
                        'payment_status' => 'failed',
                    ]);
                }
            }

            // For Paymob, return success response
            if ($gateway === 'paymob') {
                return response()->json(['success' => true]);
            }

            // Redirect to success page if payment successful
            if ($paymentResponse->success && $order) {
                return redirect()->route('checkout.success', ['order' => $order->id])
                    ->with('success', 'Payment processed successfully');
            }

            return redirect()->route('checkout.index')
                ->with('error', $paymentResponse->message);
        } catch (\Exception $e) {
            Log::error('Payment callback error: '.$e->getMessage(), [
                'gateway' => $gateway,
                'request' => $request->all(),
            ]);

            return redirect()->route('checkout.index')
                ->with('error', 'Payment verification failed');
        }
    }
}