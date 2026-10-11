<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KpBankAccount extends Model
{
    protected $table = 'kp_bank_accounts';

    protected $fillable = [
        'user_id',
        'org_id_fk',
        'entity_type',
        'account_no',
        'balance',
        'points',
        'status'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'points'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public static function updateBalanceAndPoint($user_id, $amount, $points)
    {
        // Cast Type เพื่อป้องกัน SQL Injection ชัวร์ๆ
        $amount = (float) $amount;
        $points = (int) $points;

        return self::where('user_id', $user_id)->update([
            'balance'    => DB::raw("balance + $amount"),
            'points'     => DB::raw("points + $points"),
            'updated_at' => now(),
        ]);
    }
}
