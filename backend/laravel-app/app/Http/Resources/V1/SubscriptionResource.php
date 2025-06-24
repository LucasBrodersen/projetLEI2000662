<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'stripe_status' => $this->stripe_status,
            'plan_name' => $this->name, // Adicione outros campos conforme necessário
            'start_date' => $this->created_at, // Por exemplo, data de início da assinatura
            'ends_at' => $this->ends_at, // Por exemplo, data de início da assinatura
            'stripe_id' => $this->stripe_id, // Por exemplo, data de início da assinatura
        ];
    }
}

