<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'customerId' => $this->customer_id,
            'price' => $this->price,
            'status' => $this->status,
            'creationDate' => $this->creation_date,
            'paidDate' => $this->paid_date

        ];
    }
}
