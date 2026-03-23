<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecycleBankAccount extends Model
{
    protected $table = 'recycle_bank_accounts';

    protected $fillable = [
        'user_id', 'account_no', 'balance', 'points', 'status'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'points'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
