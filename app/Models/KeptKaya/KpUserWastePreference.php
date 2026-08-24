<?php

namespace App\Models\KeptKaya;

use App\Models\Admin\Organization;
use App\Models\Admin\Subzone;
use App\Models\Admin\Zone;
use App\Models\AnnualTrash\AnnualTrash;
use App\Models\KPBankAccount;
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

     public function kpBankAccount()
    {
        return $this->hasOne(KPBankAccount::class, 'user_pref_id', 'id');
    }    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id_fk', 'id');
    }

     public function user_pref_zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    public function user_pref_subzone()
    {
        return $this->belongsTo(Subzone::class, 'subzone_id', 'id');
    }


}
