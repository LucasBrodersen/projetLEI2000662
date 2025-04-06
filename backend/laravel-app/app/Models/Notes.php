<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'note',
        'creation_date',
    ];


    public function orders() {
        return $this->belongsTo(Customer::class);
    }
}
