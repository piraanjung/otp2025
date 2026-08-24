<?php

namespace App\Models;

use App\Models\Admin\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpWelfareConfigs extends Model
{
    use HasFactory;

    protected $table = 'kp_welfare_configs';

    // เพิ่มฟิลด์เหล่านี้เพื่อให้รองรับการบันทึกแบบ Mass Assignment (create/update)
    protected $fillable = [
        'org_id_fk',
        'min_months_active',
        'min_total_weight',
        'default_payout_amount',
        'criteria_description'
    ];

    // ความสัมพันธ์กับ Organization (ถ้ามี)
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id_fk');
    }
}
