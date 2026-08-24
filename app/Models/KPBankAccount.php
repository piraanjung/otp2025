<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KPBankAccount extends Model
{
protected $table = 'kp_bank_accounts';

protected $fillable = [
    'user_pref_id', 'org_id_fk', 'account_no', 'balance', 'points', 'status'
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
