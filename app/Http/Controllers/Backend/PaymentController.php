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
        $success = $request->get('success') === 'true' || $request->get('success') === true;

        if ($gateway === 'paymob') {
            // Paymob returns data in query params after payment
            // Check for various possible success indicators
            $success = $request->get('success') === 'true'
                || $request->get('success') === true
                || $request->get('success') === '1'
                || $request->get('txn_response_code') === 'APPROVED';

            // Get transaction ID from various possible fields
            $transactionId = $request->get('id')
                ?? $request->get('transaction_id')
                ?? $request->get('obj.id')
                ?? null;

            // Log the request for debugging
            Log::info('Paymob return request', [
                'all_params' => $request->all(),
                'success' => $success,
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

            // If payment failed, restore stock and mark order as failed
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

            session()->forget('payment_order_id');

            return redirect()->route('checkout.failed')
                ->with('error', 'Payment failed. Please try again.');
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

        $order->update([
            'payment_status' => 'failed',
        ]);

        return redirect()->route('checkout.failed')
            ->with('error', $paymentResponse->message ?? 'Payment failed');
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
        $success = $request->get('success') === 'true' || $request->get('success') === true;

        if ($gateway === 'paymob') {
            // Paymob returns data in query params after payment
            // Check for various possible success indicators
            $success = $request->get('success') === 'true'
                || $request->get('success') === true
                || $request->get('success') === '1'
                || $request->get('txn_response_code') === 'APPROVED';

            // Get transaction ID from various possible fields
            $transactionId = $request->get('id')
                ?? $request->get('transaction_id')
                ?? $request->get('obj.id')
                ?? null;

            // Log the request for debugging
            Log::info('Paymob return request (offer)', [
                'all_params' => $request->all(),
                'success' => $success,
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

            // If payment failed, mark offer payment as failed
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
                ->with('error', 'Payment failed. Please try again.');
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

        // If payment failed, mark offer payment as failed
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
            ->with('error', $paymentResponse->message ?? 'Payment failed');
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
