<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPAccounts extends Model
{
    use HasFactory;

    protected $table = 'kp_accounts';
    protected $fillable = [
        'u_wpref_id_fk','balance','points','status'
    ];

    public function userWastePreference(){
        return $this->belongsTo( UserWastePreference::class, 'u_wpref_id_fk', 'id' );
    }

    public function registerAccount($u_w_pref_id){
        $res = 0;
        $check = KPAccounts::where('u_wpref_id_fk', $u_w_pref_id)->count();
        if($check == 0){
            KPAccounts::create([
                'u_wpref_id_fk' => $u_w_pref_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $res = 1;
        }

        return $res;
    }

    public function updateBalanceAndPoint($u_w_pref_id, $balance, $points){

        $remain = KPAccounts::where('u_wpref_id_fk', $u_w_pref_id)->get()->first();
        KPAccounts::where('u_wpref_id_fk', $u_w_pref_id)->update([
            'balance' => $remain->balance + $balance,
            'points' => $remain->points + $points,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
