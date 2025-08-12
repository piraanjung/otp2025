<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteBinPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'wbs_id',
        'pay_mon',
        'pay_yr',
        'amount_paid',
        'payment_date',
        'notes',
        'staff_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    /**
     * Get the subscription that owns the payment.
     */
    public function subscription()
    {
        return $this->belongsTo(WasteBinSubscription::class);
    }

    /**
     * Get the staff who recorded the payment.
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
