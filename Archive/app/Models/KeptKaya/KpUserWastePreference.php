<?php

namespace App\Models\KeptKaya;

use App\Models\AnnualTrash\AnnualTrash;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpUserWastePreference extends Model
{
    use HasFactory;

    protected $table = 'kp_user_waste_preferences';

    protected $fillable = [
        'org_id_fk',
        'user_id',
        'is_annual_collection',
        'is_waste_bank',
        "address",
       "zone_id",
       "subzone_id",
       "tambon_code",
       "district_code",
       "province_code",
    ];

    // ✅ เพิ่มตรงนี้: บังคับให้เป็น boolean เพื่อความแม่นยำ
    // protected $casts = [
    //     'is_annual_collection' => 'boolean',
    //     'is_waste_bank' => 'boolean',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function AnnualTrashs()
    {
        return $this->hasMany(AnnualTrash::class, 'user_id', 'user_id');
    }

    public function purchaseTransactions()
    {
        // Assuming kp_user_id_fk is the foreign key in the kp_purchase_transactions table
        return $this->hasMany(KpPurchaseTransaction::class, 'kp_user_w_pref_id_fk', 'id');
    }

    public function kp_account()
    {
        return $this->hasOne(KPAccounts::class, 'u_wpref_id_fk');
    }
}
