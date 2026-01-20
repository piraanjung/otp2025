<?php

namespace App\Models\KeptKaya;

use App\Models\Admin\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
class KpPurchaseTransaction extends Model
{
    use HasFactory; use BelongsToOrganization;

    protected $table = 'kp_purchase_transactions';

    protected $fillable = [
        'org_id_fk',
        'kp_u_trans_no',        // เลขที่เอกสาร (Unique String)
        'kp_user_w_pref_id_fk', // ลูกค้า
        'machine_id_fk',        // (Optional) เครื่องชั่ง
        'transaction_date',     // วันเวลา
        'total_weight',
        'total_amount',
        'total_points',
        'status',
        'recorder_id',          // จนท.
        'cash_back'
    ];

    protected $casts = [
        'transaction_date' => 'datetime', // ✅ แนะนำให้ใช้ datetime
        'total_weight' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_points' => 'integer',
        'cash_back' => 'decimal:2',
    ];

    // --- Relationships ---

    public function userWastePreference() // ปรับชื่อ function ให้ camelCase สวยงาม
    {
        return $this->belongsTo(KpUserWastePreference::class, 'kp_user_w_pref_id_fk', 'id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorder_id', 'id');
    }
    
    public function details()
    {
        // ตรวจสอบชื่อ FK ใน DB ให้ตรงกับ parameter ที่ 2
        return $this->hasMany(KpPurchaseTransactionDetail::class, 'kp_purchase_trans_id', 'id');
    }

    public function org(){
        return $this->belongsTo(Organization::class, 'org_id_fk');
    }
}