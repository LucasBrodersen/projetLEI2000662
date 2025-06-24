<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'product_id',
        'payment_intent_id',
        'payment_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
