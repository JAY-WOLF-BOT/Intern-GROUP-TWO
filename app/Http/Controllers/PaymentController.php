<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Initiate a MoMo payment transaction.
     */
    public function initiatePayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'listing_id' => 'required|exists:listings,id',
            'payment_type' => 'required|in:deposit,viewing_fee',
            'momo_network' => 'required|in:MTN,Vodafone,AirtelTigo',
            'phone_number' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $listing = Listing::findOrFail($request->listing_id);

        // Determine payment amount based on type
        $amount = match($request->payment_type) {
            'deposit' => $listing->price * 0.1, // 10% deposit
            'viewing_fee' => 25.00, // Fixed viewing fee
        };

        // Check if user already has a pending payment for this type and listing
        $existingPayment = Payment::where('tenant_id', Auth::id())
            ->where('listing_id', $request->listing_id)
            ->where('payment_type', $request->payment_type)
            ->where('payment_status', 'pending')
            ->first();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending payment for this service.',
                'payment_id' => $existingPayment->id
            ], 409);
        }

        try {
            // Generate unique transaction ID
            $transactionId = 'HOUSING_' . strtoupper($request->payment_type) . '_' . time() . '_' . Str::random(4);

            // Create payment record
            $payment = Payment::create([
                'tenant_id' => Auth::id(),
                'landlord_id' => $listing->landlord_id,
                'listing_id' => $request->listing_id,
                'amount' => $amount,
                'payment_type' => $request->payment_type,
                'payment_method' => 'momo',
                'payment_status' => 'pending',
                'transaction_id' => $transactionId,
                'momo_network' => $request->momo_network,
                'description' => $this->getPaymentDescription($request->payment_type, $listing->title),
            ]);

            // Simulate MoMo API call (replace with actual MoMo API integration)
            $momoResponse = $this->initiateMoMoPayment($payment, $request->phone_number);

            if ($momoResponse['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment initiated successfully. Please check your phone for MoMo prompt.',
                    'data' => [
                        'payment_id' => $payment->id,
                        'transaction_id' => $transactionId,
                        'amount' => $amount,
                        'momo_reference' => $momoResponse['reference'] ?? null,
                        'instructions' => 'Please complete the payment on your mobile device.'
                    ]
                ], 201);
            } else {
                // Update payment status to failed
                $payment->update(['payment_status' => 'failed']);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initiate MoMo payment.',
                    'error' => $momoResponse['message']
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status.
     */
    public function checkStatus($paymentId): JsonResponse
    {
        $payment = Payment::findOrFail($paymentId);

        // Check if user owns this payment
        if ($payment->tenant_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied.'
            ], 403);
        }

        // In a real implementation, you would check with MoMo API
        // For now, we'll simulate status checking

        return response()->json([
            'success' => true,
            'data' => [
                'payment_id' => $payment->id,
                'status' => $payment->payment_status,
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'paid_at' => $payment->paid_at,
            ]
        ]);
    }

    /**
     * Get payment description based on type.
     */
    private function getPaymentDescription(string $type, string $listingTitle): string
    {
        return match($type) {
            'deposit' => "Security deposit for: {$listingTitle}",
            'viewing_fee' => "Viewing fee for: {$listingTitle}",
        };
    }

    /**
     * Simulate MoMo payment initiation (replace with actual API call).
     */
    private function initiateMoMoPayment(Payment $payment, string $phoneNumber): array
    {
        // This is a simulation - replace with actual MoMo API integration
        // For Ghana, you would integrate with:
        // - MTN MoMo API
        // - Vodafone Cash API
        // - AirtelTigo Money API

        // Simulate API call
        $success = rand(1, 10) > 2; // 80% success rate for simulation

        if ($success) {
            // Simulate successful initiation
            return [
                'success' => true,
                'reference' => 'MOMO_REF_' . time() . rand(1000, 9999),
                'message' => 'Payment request sent to ' . $phoneNumber
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to initiate payment. Please try again.'
            ];
        }
    }
}
