<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; // เพิ่มเพื่อใช้ DB Transaction

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KPAccounts extends Model
{
    use HasFactory;

    protected $table = 'kp_accounts';
    
    // PK เป็น FK (1-to-1)
    protected $primaryKey = 'u_wpref_id_fk';
    
    // ปิด incrementing เพราะ PK กำหนดเอง
    public $incrementing = false; 

    protected $fillable = [
        'u_wpref_id_fk', 'balance', 'points', 'status'
    ];

    protected $casts = [
        'balance' => 'decimal:2', // ตอนดึงออกมาจะเป็น string/float ตาม PHP version
        'points'  => 'integer'
    ];

    public function userWastePreference(){
        return $this->belongsTo(KpUserWastePreference::class, 'u_wpref_id_fk', 'id');
    }

    /**
     * สร้างบัญชีใหม่
     */
    public static function registerAccount($u_w_pref_id)
    {
        $account = self::firstOrCreate(
            ['u_wpref_id_fk' => $u_w_pref_id],
            [
                'balance'    => 0,
                'points'     => 0,
                'status'     => 'active',
            ]
        );
        
        return $account->wasRecentlyCreated;
    }

    /**
     * อัปเดตยอดเงินและแต้ม (Atomic Update)
     * รองรับทั้งการเพิ่ม (+) และการลด (-)
     */
    public static function updateBalanceAndPoint($u_w_pref_id, $amount, $points)
    {
        // Cast Type เพื่อป้องกัน SQL Injection ชัวร์ๆ
        $amount = (float) $amount;
        $points = (int) $points;

        return self::where('u_wpref_id_fk', $u_w_pref_id)->update([
            'balance'    => DB::raw("balance + $amount"),
            'points'     => DB::raw("points + $points"),
            'updated_at' => now(),
        ]);
    }
}