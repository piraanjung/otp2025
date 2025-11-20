<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KPAccounts extends Model
{
    use HasFactory;

    protected $table = 'kp_accounts';
    public $primaryKey = 'u_wpref_id_fk';
    protected $fillable = [
        'u_wpref_id_fk','balance','points','status'
    ];

    public function userWastePreference(){
        return $this->belongsTo( KpUserWastePreference::class, 'u_wpref_id_fk', 'id' );
    }

    public function registerAccount($u_w_pref_id){
        $res = 0;
        $kPAccounts = new KPAccounts();
        $check = $kPAccounts->where('u_wpref_id_fk', $u_w_pref_id)->count();
        if($check == 0){
            $kPAccounts->create([
                'u_wpref_id_fk' => $u_w_pref_id,
                'balance'       => 0,
                'points'        => 0,
                'status'        => 'active',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $res = 1;
        }

        return $res;
    }

    public function updateBalanceAndPoint($u_w_pref_id, $balance, $points){
    return    $remain = KPAccounts::where('u_wpref_id_fk', $u_w_pref_id)->get()->first();
        KPAccounts::where('u_wpref_id_fk', $u_w_pref_id)->update([
            'balance' => $remain->balance + $balance,
            'points' => $remain->points + $points,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
