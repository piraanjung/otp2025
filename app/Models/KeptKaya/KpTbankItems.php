<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. นำเข้า Trait
class KpTbankItems extends Model
{
    use HasFactory;
    use BelongsToOrganization; // ตัวนี้จะช่วยกรอง org_id_fk อัตโนมัติในทุก query
    use SoftDeletes; // 2. เรียกใช้งาน Trait

    protected $dates = ['deleted_at']; // 3. ระบุคอลัมน์วันที่
    protected $table = 'kp_tbank_items';

    protected $fillable = [
        'kp_itemscode',
        'kp_itemsname',
        'unit_bank_idfk',           // เพิ่มใหม่
        'unit_kiosk_idfk',          // เพิ่มใหม่
        'kp_items_group_idfk',
        'favorite',            // เพิ่มใหม่
        'status',
        'image',
        'deleted',
        'org_id_fk',
        'ef_id_fk'
    ];

    // แนะนำให้ใส่ Casts เพื่อให้เวลาดึงข้อมูลมาใช้ Laravel เปลี่ยน Type ให้เลย
    protected $casts = [
        'favorite' => 'integer',
        'deleted' => 'integer',
    ];

    // --- Relationships ---

    public function group() // เปลี่ยนชื่อให้เรียกง่ายขึ้นจาก kp_items_groups
    {
        return $this->belongsTo(KpTbankItemsGroups::class, 'kp_items_group_idfk', 'id');
    }

    public function prices()
    {
        return $this->hasMany(KpTbankItemsPriceAndPoint::class, 'kp_items_idfk', 'id');
    }

    public function activePrices()
    {
        return $this->hasMany(KpTbankItemsPriceAndPoint::class, 'kp_items_idfk', 'id')
            ->where('status', 'active');
    }

    public function emissionFactor()
    {
        // ใช้ ef_id_fk ตามที่คุณมีใน Table
        return $this->belongsTo(\App\Models\EmissionFactor::class, 'ef_id_fk');
    }

    // --- Scopes (ตัวช่วย Query) ---

    // เรียกใช้ KpTbankItems::forKiosk()->get() เพื่อดึงรายการที่แสดงหน้าตู้ได้ทันที
    public function scopeForKiosk($query)
    {
        return $query->whereNotNull('unit_kiosk')->where('status', 'active');
    }

    public function unitBank()
    {
        return $this->belongsTo(KpTbankUnits::class, 'unit_bank_idfk', 'id');
    }

    // ดึงข้อมูลหน่วยนับของคีออส
    public function unitKiosk()
    {
        return $this->belongsTo(KpTbankUnits::class, 'unit_kiosk_idfk', 'id');
    }
}
