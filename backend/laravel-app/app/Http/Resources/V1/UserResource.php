<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postalCode' => $this->postal_code,
            'enrolmentDate' => $this->enrolment_date,
            'status' => $this->status,
            'activeSubscription' => new SubscriptionResource($this->whenLoaded('activeSubscription')), // Aqui é onde carregamos a assinatura
            //'orders' => OrderResource::collection($this->whenLoaded('orders')) // show orders when required
        ];
    }
}
