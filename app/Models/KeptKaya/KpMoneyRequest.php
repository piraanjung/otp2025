<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class KpMoneyRequest extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'verification_code', 'status',
        'is_proxy', 'proxy_name', 'payout_date', 'admin_id', 'completed_at'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
