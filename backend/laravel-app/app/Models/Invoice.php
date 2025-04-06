<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'stripe_id',
        'stripe_customer_id',
        'user_id',
        'amount_due',
        'amount_paid',
        'amount_remaining',
        'currency',
        'status',
        'invoice_pdf',
        'hosted_invoice_url',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}