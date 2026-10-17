<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelfareContribution extends Model
{
    use HasFactory;

    protected $table = 'welfare_contributions';

    protected $fillable = [
        'org_id_fk',
        'user_id',
        'payout_id',
        'amount',
        'payment_method',
        'status',
        'paid_at',
        'slip_img',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payout()
    {
        return $this->belongsTo(WelfarePayout::class, 'payout_id');
    }
}