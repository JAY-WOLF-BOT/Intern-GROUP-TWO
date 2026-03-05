<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'amount' => (float) $this->amount,
            'payment_type' => $this->payment_type,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'momo_network' => $this->momo_network,
            'description' => $this->description,
            'listing' => [
                'id' => $this->listing->id,
                'title' => $this->listing->title,
                'price' => $this->listing->price,
            ],
            'paid_at' => $this->paid_at ? $this->paid_at->format('Y-m-d H:i:s') : null,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
