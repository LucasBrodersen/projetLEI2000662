<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',           // Add 'type' here
        'address',        // Add 'address' if applicable
        'city',           // Add 'city' if applicable
        'state',          // Add 'state' if applicable
        'postal_code',    // Add 'postal_code' if applicable
        'enrolment_date',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Adicionando o relacionamento active_subscription
    public function activeSubscription()
    {
        return $this->hasOne(\Laravel\Cashier\Subscription::class, 'user_id')
                    ->where('stripe_status', 'active');
    }
}
