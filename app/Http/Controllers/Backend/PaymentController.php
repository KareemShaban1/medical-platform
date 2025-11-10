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
        $hmacError = false;
        
        // Check Paymob specific error fields first (before HMAC verification)
        // Paymob can return error messages in data_message field
        if (isset($paymentData['data_message']) && !empty($paymentData['data_message'])) {
            $errorMessage = $paymentData['data_message'];
        } elseif (isset($paymentData['data']['message'])) {
            $errorMessage = $paymentData['data']['message'];
        } elseif (isset($paymentData['obj']['data']['message'])) {
            $errorMessage = $paymentData['obj']['data']['message'];
        } elseif (isset($paymentData['message'])) {
            $errorMessage = $paymentData['message'];
        } elseif (isset($paymentData['error_message'])) {
            $errorMessage = $paymentData['error_message'];
        }

        // Check for error codes - Paymob provides response codes
        $errorCode = $paymentData['acq_response_code']  // Acquirer response code (most important)
            ?? $paymentData['txn_response_code']  // Transaction response code
            ?? $paymentData['error_code'] 
            ?? $paymentData['data']['error_code'] 
            ?? $paymentData['obj']['data']['error_code'] 
            ?? null;
        
        // Map response codes to human-readable messages if available
        $responseCodeMessage = null;
        if ($paymentData['acq_response_code'] ?? null) {
            $responseCodeMessage = $this->getPaymobResponseCodeMessage($paymentData['acq_response_code']);
        } elseif ($paymentData['txn_response_code'] ?? null) {
            $responseCodeMessage = $this->getPaymobResponseCodeMessage($paymentData['txn_response_code']);
        }
        
        // Use response code message if no other error message found
        if (!$errorMessage && $responseCodeMessage) {
            $errorMessage = $responseCodeMessage;
        }

        // Check transaction status for clues
        $status = $paymentData['status'] 
            ?? $paymentData['obj']['status'] 
            ?? null;

        // Check if card was declined
        $isDeclined = isset($paymentData['is_voided']) && ($paymentData['is_voided'] === 'true' || $paymentData['is_voided'] === true)
            || isset($paymentData['is_refunded']) && ($paymentData['is_refunded'] === 'true' || $paymentData['is_refunded'] === true);

        // For Paymob, try to fetch detailed transaction info from API if we have transaction ID
        $transactionId = $paymentData['id'] ?? $paymentData['transaction_id'] ?? null;
        $analysis = null;
        
        if ($gatewayInstance && $transactionId && $errorOccurred) {
            try {
                // Try to get detailed transaction info from Paymob API
                if (method_exists($gatewayInstance, 'getTransactionDetails')) {
                    $transactionDetails = $gatewayInstance->getTransactionDetails((string)$transactionId);
                    
                    if ($transactionDetails) {
                        // Extract error information from transaction details
                        $apiErrorMessage = $transactionDetails['data']['message'] 
                            ?? $transactionDetails['message'] 
                            ?? $transactionDetails['error_message'] 
                            ?? null;
                        
                        $apiErrorCode = $transactionDetails['data']['error_code'] 
                            ?? $transactionDetails['error_code'] 
                            ?? $transactionDetails['data']['transaction_error_code']
                            ?? $transactionDetails['transaction_error_code']
                            ?? null;
                        
                        // Always analyze transaction details to infer failure reason
                        $analysis = $this->analyzePaymobTransactionFailure($transactionDetails);
                        
                        // Use API error message if available, otherwise use analysis
                        if ($apiErrorMessage) {
                            $errorMessage = $apiErrorMessage;
                        } elseif ($analysis['inferred_reason']) {
                            $errorMessage = $analysis['inferred_reason'];
                        }
                        
                        // Use API error code if available, otherwise use analysis
                        if ($apiErrorCode) {
                            $errorCode = $apiErrorCode;
                            $errorDetails['error_code'] = $apiErrorCode;
                        } elseif ($analysis['error_code']) {
                            $errorCode = $analysis['error_code'];
                            $errorDetails['error_code'] = $analysis['error_code'];
                        }
                        
                        // Always store analysis in error details for tracking
                        $errorDetails['analysis'] = $analysis;
                        $errorDetails['transaction_analysis'] = [
                            'payment_status' => $transactionDetails['order']['payment_status'] ?? null,
                            'is_3d_secure' => $transactionDetails['is_3d_secure'] ?? false,
                            'card_type' => $transactionDetails['card_type'] ?? null,
                            'card_pan' => isset($transactionDetails['source_data']['pan']) 
                                ? substr($transactionDetails['source_data']['pan'], -4) 
                                : null,
                            'payment_method' => $transactionDetails['order']['payment_method'] ?? null,
                            'is_voided' => $transactionDetails['is_voided'] ?? false,
                            'is_refunded' => $transactionDetails['is_refunded'] ?? false,
                            'amount_cents' => $transactionDetails['amount_cents'] ?? null,
                        ];
                        
                        // Log the detailed transaction info with analysis
                        Log::info('Paymob transaction details retrieved with analysis', [
                            'transaction_id' => $transactionId,
                            'error_message' => $errorMessage,
                            'error_code' => $errorCode,
                            'analysis' => $analysis,
                            'transaction_analysis' => $errorDetails['transaction_analysis'],
                            'has_transaction_details' => true,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch transaction details from Paymob API', [
                    'transaction_id' => $transactionId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // For Paymob, check if payment actually failed (success: false, error_occured: true)
        // This is the real payment failure, not just HMAC verification
        if ($errorOccurred && !$errorMessage) {
            // Payment failed but no specific message - provide generic message based on context
            if ($isDeclined) {
                $errorMessage = 'Payment was declined by the bank. Please check your card details or try a different payment method.';
            } else {
                $errorMessage = 'Payment failed. The transaction could not be processed. Please try again or contact support.';
            }
        }

        // Try to get additional info from verifyPayment, but don't let HMAC errors mask real payment errors
        if ($gatewayInstance && !$errorMessage) {
            try {
                $paymentResponse = $gatewayInstance->verifyPayment($paymentData);
                if (!$paymentResponse->success && $paymentResponse->message) {
                    // Only use verifyPayment message if it's not just an HMAC error
                    // HMAC errors should be logged separately, not as the main error
                    if (stripos($paymentResponse->message, 'HMAC') === false) {
                        $errorMessage = $paymentResponse->message;
                    } else {
                        $hmacError = true;
                        // Log HMAC error separately but don't use it as main error message
                        Log::warning('HMAC verification failed (but payment may have failed for other reasons)', [
                            'payment_data' => [
                                'success' => $paymentData['success'] ?? null,
                                'error_occured' => $errorOccurred,
                                'transaction_id' => $paymentData['id'] ?? null,
                            ],
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // If verification throws exception, log it but don't use as main error if we have payment failure
                if (!$errorMessage && !$errorOccurred) {
                    $errorMessage = $e->getMessage();
                } else {
                    Log::warning('Payment verification exception (payment already marked as failed)', [
                        'exception' => $e->getMessage(),
                        'error_occured' => $errorOccurred,
                    ]);
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
        $errorDetails['hmac_error'] = $hmacError;
        $errorDetails['response_codes'] = [
            'acq_response_code' => $paymentData['acq_response_code'] ?? null,
            'txn_response_code' => $paymentData['txn_response_code'] ?? null,
            'response_code_message' => $responseCodeMessage,
        ];
        $errorDetails['raw_data'] = [
            'success' => $paymentData['success'] ?? null,
            'error_occured' => $errorOccurred,
            'status' => $status,
            'is_voided' => $paymentData['is_voided'] ?? null,
            'is_refunded' => $paymentData['is_refunded'] ?? null,
            'pending' => $paymentData['pending'] ?? null,
            'source_data_type' => $paymentData['source_data_type'] ?? null,
            'source_data_pan' => isset($paymentData['source_data_pan']) ? substr($paymentData['source_data_pan'], -4) : null, // Last 4 digits only
            'data_message' => $paymentData['data_message'] ?? null,
        ];

        // Log all available fields from payment data for analysis
        // This helps identify what Paymob actually sends when payment fails
        if ($errorOccurred) {
            Log::info('Paymob payment failure - all available fields', [
                'transaction_id' => $transactionId,
                'all_payment_fields' => array_keys($paymentData),
                'payment_data_sample' => [
                    'success' => $paymentData['success'] ?? null,
                    'error_occured' => $paymentData['error_occured'] ?? null,
                    'pending' => $paymentData['pending'] ?? null,
                    'is_voided' => $paymentData['is_voided'] ?? null,
                    'is_refunded' => $paymentData['is_refunded'] ?? null,
                    'is_3d_secure' => $paymentData['is_3d_secure'] ?? null,
                    'integration_id' => $paymentData['integration_id'] ?? null,
                    'order' => $paymentData['order'] ?? null,
                    'amount_cents' => $paymentData['amount_cents'] ?? null,
                    'currency' => $paymentData['currency'] ?? null,
                    // Check for any fields that might contain error info
                    'data' => $paymentData['data'] ?? null,
                    'obj' => isset($paymentData['obj']) ? 'present' : null,
                ],
            ]);
        }

        return $errorDetails;
    }

    /**
     * Analyze Paymob transaction details to infer failure reason
     * Since Paymob doesn't always provide error messages, we analyze available data
     */
    protected function analyzePaymobTransactionFailure(array $transactionDetails): array
    {
        $analysis = [
            'inferred_reason' => null,
            'error_code' => null,
            'indicators' => [],
            'confidence' => 'low', // low, medium, high
        ];

        // Check payment status
        $paymentStatus = $transactionDetails['order']['payment_status'] ?? null;
        if ($paymentStatus === 'UNPAID') {
            $analysis['indicators'][] = 'Payment status: UNPAID';
        }

        // Check if it's a test card or invalid card
        $pan = $transactionDetails['source_data']['pan'] ?? null;
        // Known test card patterns: 1111, 0000, or very low numbers like 0001-0999
        $isTestCard = false;
        if ($pan) {
            if ($pan === '1111' || $pan === '0000') {
                $isTestCard = true;
            } elseif (is_numeric($pan) && strlen($pan) === 4) {
                $panNum = (int)$pan;
                // Test cards are typically 0000-0999 range
                if ($panNum >= 0 && $panNum < 1000) {
                    $isTestCard = true;
                }
            }
        }
        
        if ($isTestCard) {
            $analysis['indicators'][] = 'Test card detected (PAN: ' . $pan . ')';
            $analysis['inferred_reason'] = 'Payment failed. Test cards may not work in production. Please use a real card for payment.';
            $analysis['confidence'] = 'high';
        } elseif ($pan && strlen($pan) === 4) {
            // Real card but payment failed - check other indicators
            $analysis['indicators'][] = 'Real card used (PAN: ' . $pan . ')';
        }

        // Check 3D Secure status (only treat as error if 3D Secure is required)
        $is3DSecure = $transactionDetails['is_3d_secure'] ?? false;
        $sourceType = $transactionDetails['source_data']['type'] ?? null;
        $require3DSecure = config('payment_gateways.paymob.require_3d_secure', true);
        
        if (!$is3DSecure && $sourceType === 'card') {
            if ($require3DSecure) {
                // 3D Secure is required but not completed - this is an error
                $analysis['indicators'][] = '3D Secure not completed (is_3d_secure: false)';
                if (!$analysis['inferred_reason']) {
                    $analysis['inferred_reason'] = 'Payment failed. 3D Secure authentication was not completed. This could be due to: card not supporting 3D Secure, authentication cancelled, or bank security restrictions. Please try again or contact your bank.';
                    $analysis['confidence'] = 'medium';
                }
            } else {
                // 3D Secure is optional - just note it, don't treat as error
                $analysis['indicators'][] = '3D Secure not completed (optional, is_3d_secure: false)';
            }
        } elseif ($is3DSecure) {
            $analysis['indicators'][] = '3D Secure was completed';
        }

        // Check if card was voided or refunded
        if ($transactionDetails['is_voided'] ?? false) {
            $analysis['indicators'][] = 'Transaction was voided';
            if (!$analysis['inferred_reason']) {
                $analysis['inferred_reason'] = 'Payment was voided. The transaction was cancelled before completion.';
                $analysis['confidence'] = 'high';
            }
        }

        if ($transactionDetails['is_refunded'] ?? false) {
            $analysis['indicators'][] = 'Transaction was refunded';
            if (!$analysis['inferred_reason']) {
                $analysis['inferred_reason'] = 'Payment was refunded. Please contact support if this was not expected.';
                $analysis['confidence'] = 'high';
            }
        }

        // Check payment method
        $paymentMethod = $transactionDetails['order']['payment_method'] ?? null;
        if ($paymentMethod === 'tbc' || $paymentMethod === null) {
            $analysis['indicators'][] = 'Payment method not finalized';
        }

        // Check if amount is 0 or very small
        $amountCents = $transactionDetails['amount_cents'] ?? 0;
        if ($amountCents <= 0) {
            $analysis['indicators'][] = 'Invalid amount';
            if (!$analysis['inferred_reason']) {
                $analysis['inferred_reason'] = 'Payment failed due to invalid amount.';
                $analysis['confidence'] = 'high';
            }
        }

        // Check integration type
        $integrationType = $transactionDetails['integration_type'] ?? null;
        if ($integrationType && $integrationType !== 'online') {
            $analysis['indicators'][] = "Integration type: {$integrationType}";
        }

        // Check if it's a card issue
        $cardType = $transactionDetails['card_type'] ?? null;
        $sourceType = $transactionDetails['source_data']['type'] ?? null;
        if ($sourceType === 'card' && !$cardType) {
            $analysis['indicators'][] = 'Card type not recognized';
        }

        // If no specific reason found but error occurred, provide generic analysis
        if (!$analysis['inferred_reason'] && ($transactionDetails['error_occured'] ?? false)) {
            // Build detailed failure reason based on available indicators
            $reasons = [];
            
            if ($paymentStatus === 'UNPAID') {
                $reasons[] = 'Payment status: UNPAID';
            }
            
            if (!$is3DSecure && $sourceType === 'card') {
                $reasons[] = '3D Secure authentication not completed';
            }
            
            if ($paymentMethod === 'tbc') {
                $reasons[] = 'Payment method not finalized (tbc)';
            }
            
            // Build comprehensive message
            if ($paymentStatus === 'UNPAID' && !$is3DSecure) {
                $analysis['inferred_reason'] = 'Payment failed. The transaction was not completed. Likely causes: ' . 
                    '1) 3D Secure authentication was not completed, ' .
                    '2) Insufficient funds, ' .
                    '3) Card declined by bank, or ' .
                    '4) Bank security restrictions. ' .
                    'Please check your card details, ensure sufficient funds, and try again. If the issue persists, contact your bank.';
                $analysis['confidence'] = 'medium';
            } elseif ($paymentStatus === 'UNPAID') {
                $analysis['inferred_reason'] = 'Payment failed. Transaction status: UNPAID. Common reasons: ' .
                    '1) Insufficient funds, ' .
                    '2) Card declined by bank, ' .
                    '3) Bank security restrictions, or ' .
                    '4) Card expired/invalid. ' .
                    'Please verify your card details and try again, or contact your bank. Transaction ID: ' . ($transactionDetails['id'] ?? 'N/A');
                $analysis['confidence'] = 'medium';
            } else {
                $analysis['inferred_reason'] = 'Payment failed. The transaction could not be processed. ' .
                    'Please verify your card details and try again. ' .
                    'If the problem persists, contact support with transaction ID: ' . ($transactionDetails['id'] ?? 'N/A');
                $analysis['confidence'] = 'low';
            }
            
            // Add all indicators to the analysis
            if (!empty($reasons)) {
                $analysis['indicators'] = array_merge($analysis['indicators'], $reasons);
            }
        }

        return $analysis;
    }

    /**
     * Get human-readable message for Paymob response codes
     */
    protected function getPaymobResponseCodeMessage(string $code): ?string
    {
        // Common Paymob/Acquirer response codes
        $codeMessages = [
            // Approved
            'APPROVED' => 'Payment approved',
            '00' => 'Payment approved',
            
            // Declined codes
            '05' => 'Payment declined - Do not honor',
            '14' => 'Payment declined - Invalid card number',
            '51' => 'Payment declined - Insufficient funds',
            '54' => 'Payment declined - Expired card',
            '57' => 'Payment declined - Transaction not permitted',
            '61' => 'Payment declined - Exceeds withdrawal limit',
            '62' => 'Payment declined - Restricted card',
            '65' => 'Payment declined - Exceeds withdrawal frequency',
            '91' => 'Payment declined - Issuer or switch is inoperative',
            '96' => 'Payment declined - System malfunction',
            
            // 3D Secure related
            'AUTHENTICATION_FAILED' => '3D Secure authentication failed',
            'AUTHENTICATION_CANCELLED' => '3D Secure authentication was cancelled',
            
            // General errors
            'INVALID_CARD' => 'Invalid card number',
            'INSUFFICIENT_FUNDS' => 'Insufficient funds',
            'CARD_EXPIRED' => 'Card expired',
            'CARD_DECLINED' => 'Card declined by bank',
        ];
        
        // Check exact match first
        if (isset($codeMessages[$code])) {
            return $codeMessages[$code];
        }
        
        // Check if code contains known patterns
        $codeUpper = strtoupper($code);
        foreach ($codeMessages as $pattern => $message) {
            if (stripos($codeUpper, $pattern) !== false) {
                return $message;
            }
        }
        
        // Return null if no match found
        return null;
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
            
            // Log comprehensive error details for debugging with analysis prominently displayed
            Log::error('Payment failed (offer)', [
                'offer_id' => $offerId,
                'order_number' => $orderNumber,
                'transaction_id' => $transactionId,
                'failure_analysis' => $errorDetails['analysis'] ?? null,
                'transaction_analysis' => $errorDetails['transaction_analysis'] ?? null,
                'inferred_reason' => $errorDetails['analysis']['inferred_reason'] ?? null,
                'failure_indicators' => $errorDetails['analysis']['indicators'] ?? [],
                'confidence_level' => $errorDetails['analysis']['confidence'] ?? null,
                'error_message' => $errorDetails['error_message'] ?? null,
                'error_code' => $errorDetails['error_code'] ?? null,
                'error_type' => $errorDetails['error_type'] ?? null,
                'full_error_details' => $errorDetails,
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