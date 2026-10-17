<?php

namespace App\Models\FoodWaste;

use App\Models\Admin\Organization;
use App\Models\KeptKaya\KPAccounts;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodWasteUserPreference extends Model
{
    use HasFactory;

    protected $table = 'foodwaste_user_preferences';

    protected $fillable = [
        'id',
        'user_id',
        'org_id_fk',
        'status'
    ];

    protected $casts = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

        public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id_fk');
    }

    public function foodwaste_bins()
    {
        return $this->hasMany(FoodAnnualTrash::class, 'u_pref_id_fk');
    }



    public function foodwaste_account() // เปลี่ยนชื่อให้สื่อสารชัดเจน
    {
        // เปลี่ยนจาก KPAccounts เป็น FoodWasteAccount
        // และใช้ 'fw_pref_id_fk' ตามที่นิยามไว้ใน Model Account
        return $this->hasOne(FoodWasteAccount::class, 'fw_pref_id_fk');
    }
}
