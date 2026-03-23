<?php

namespace App\Models\FoodWaste;

use App\Models\FoodWaste\FoodWasteUserPreference;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class FoodWasteAccount extends Model {

    
   protected $fillable = [
        'user_id',
        'points_balance',            // แต้มสะสมจากขยะเปียก
        'money_balance',             // เงินสะสม (ถ้ามี)
        'total_weight_kg',           // น้ำหนักรวมที่ส่งมา (กิโลกรัม)
        'last_contributed_at'        // วันที่ส่งขยะล่าสุด (เพิ่มไว้จะดีมาก)
    ];

    protected $casts = [
        'points_balance' => 'integer',
        'money_balance' => 'decimal:2',
        'total_weight_kg' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
