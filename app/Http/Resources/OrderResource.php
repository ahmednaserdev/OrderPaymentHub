<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'user_details' => json_decode($this->user_details),
            'items' => json_decode($this->items),
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'payments' => $this->payments->map(function ($payment) {
                return [
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                ];
            }),
        ];
    }
}
