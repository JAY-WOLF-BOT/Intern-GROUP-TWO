<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Initiate a payment for viewing a listing or deposit.
     */
    public function initiate(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'listing_id' => 'required|integer|exists:listings,id',
            'payment_type' => 'required|in:viewing_fee,deposit_holding',
            'payment_method' => 'required|in:momo', // Future: card, bank_transfer
            'momo_network' => 'required_if:payment_method,momo|in:MTN,Vodafone,AirtelTigo',
            'phone_number' => 'required_if:payment_method,momo|regex:/^(\+233|0)[0-9]{9,10}$/', // Ghana format
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $listing = Listing::findOrFail($request->listing_id);

            // Determine payment amount based on type
            $amount = $request->payment_type === 'viewing_fee' ? 20 : ($listing->price * 0.1); // 20 GHS for viewing, 10% deposit

            // Generate unique payment ID
            $paymentId = 'PAY-' . time() . '-' . Auth::id();
            $transactionId = 'TXN-' . uniqid();

            // Create payment record
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'listing_id' => $request->listing_id,
                'payment_id' => $paymentId,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'payment_type' => $request->payment_type,
                'payment_method' => $request->payment_method,
                'momo_network' => $request->momo_network ?? null,
                'payment_status' => 'pending',
                'description' => $request->payment_type === 'viewing_fee' 
                    ? "Viewing fee for {$listing->title}" 
                    : "Deposit holding for {$listing->title}",
            ]);

            // In production: Call MoMo API to initiate payment
            // For now: Simulate payment initiation
            // $this->initiateMoMoPayment($payment, $request->phone_number, $request->momo_network);

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated successfully. Please complete the MoMo transaction.',
                'data' => [
                    'payment' => new PaymentResource($payment),
                    'instructions' => [
                        'network' => $request->momo_network,
                        'amount' => number_format($amount, 2),
                        'ussd_code' => $this->getUSSDAcessCode($request->momo_network),
                        'description' => "Pay {$paymentId} to {config('app.name')}",
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status.
     */
    public function checkStatus($paymentId): JsonResponse
    {
        try {
            $payment = Payment::where('payment_id', $paymentId)->firstOrFail();

            // In production: Check with MoMo API
            // $mobileMoneyStatus = $this->checkMoMoStatus($payment->transaction_id);

            return response()->json([
                'success' => true,
                'data' => new PaymentResource($payment)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found.',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Get payment history for authenticated user.
     */
    public function paymentHistory(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated.'
            ], 401);
        }

        try {
            $query = Payment::where('user_id', Auth::id());

            // Filter by payment status
            if ($request->has('status') && !empty($request->status)) {
                $query->where('payment_status', $request->status);
            }

            // Filter by payment type
            if ($request->has('type') && !empty($request->type)) {
                $query->where('payment_type', $request->type);
            }

            // Filter by date range
            if ($request->has('from_date') && !empty($request->from_date)) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->has('to_date') && !empty($request->to_date)) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $payments = $query->with('listing')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => PaymentResource::collection($payments),
                'meta' => [
                    'total' => $payments->total(),
                    'per_page' => $payments->perPage(),
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'total_spent' => Payment::where('user_id', Auth::id())
                        ->where('payment_status', 'completed')
                        ->sum('amount'),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment history.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm payment completion (webhook from MoMo provider).
     * In production, this should verify the webhook signature.
     */
    public function confirmPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|string|exists:payments,payment_id',
            'transaction_id' => 'required|string',
            'status' => 'required|in:completed,failed,cancelled',
            'amount_paid' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $payment = Payment::where('payment_id', $request->payment_id)->firstOrFail();

            // Verify transaction amount matches
            if ($request->amount_paid != $payment->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amount mismatch. Payment not confirmed.'
                ], 400);
            }

            // Update payment status
            $payment->update([
                'payment_status' => $request->status,
                'transaction_id' => $request->transaction_id,
                'paid_at' => $request->status === 'completed' ? now() : null,
            ]);

            // If viewing fee: grant access to listing details
            if ($request->status === 'completed' && $payment->payment_type === 'viewing_fee') {
                // Notify landlord that someone wants to view
                // Send viewing approval to tenant
            }

            // If deposit holding: reserve the listing
            if ($request->status === 'completed' && $payment->payment_type === 'deposit_holding') {
                $payment->listing->update(['is_available' => false]);
                // Notify landlord of deposit holding
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully.',
                'data' => new PaymentResource($payment)
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm payment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get USSD access code for specific network.
     */
    private function getUSSDAcessCode(string $network): string
    {
        $codes = [
            'MTN' => '*170#',
            'Vodafone' => '*110#',
            'AirtelTigo' => '*555#',
        ];

        return $codes[$network] ?? '*170#';
    }

    /**
     * Todo: Implement actual MoMo API integration.
     */
    private function initiateMoMoPayment(Payment $payment, string $phoneNumber, string $network): bool
    {
        // Call MoMo API endpoint
        // $response = Http::post('https://momo-api.example.com/v1/requesttopay', [
        //     'amount' => $payment->amount,
        //     'currency' => 'GHS',
        //     'externalId' => $payment->payment_id,
        //     'payer' => ['partyIdType' => 'MSISDN', 'partyId' => $phoneNumber],
        //     'payerMessage' => $payment->description,
        //     'payeeNote' => config('app.name'),
        // ]);

        return false;
    }

    /**
     * Todo: Check MoMo payment status from provider.
     */
    private function checkMoMoStatus(string $transactionId): string
    {
        // Call MoMo API to check status
        // $response = Http::get("https://momo-api.example.com/v1/requesttopay/{$transactionId}");

        return 'pending';
    }
}
