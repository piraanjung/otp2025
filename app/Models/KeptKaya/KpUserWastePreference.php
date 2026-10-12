<?php

namespace App\Models\KeptKaya;

use App\Models\Admin\Organization;
use App\Models\Admin\Subzone;
use App\Models\Admin\Zone;
use App\Models\AnnualTrash\AnnualTrash;
use App\Models\KpBankAccount;
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
        'is_recycle_bank',
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
        // kp_purchase_transactions อ้างอิงผู้ขายด้วย user_id (users.id)
        return $this->hasMany(KpPurchaseTransaction::class, 'user_id', 'user_id');
    }

     public function kpBankAccount()
    {
        // kp_bank_accounts อ้างอิงด้วย user_id + entity_type (บัญชีธนาคารขยะรีไซเคิล)
        return $this->hasOne(KpBankAccount::class, 'user_id', 'user_id')->where('entity_type', 'recycle_bank');
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
