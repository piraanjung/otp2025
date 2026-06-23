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
        'ef_id_fk',
        'deleted_at'
    ];

    // แนะนำให้ใส่ Casts เพื่อให้เวลาดึงข้อมูลมาใช้ Laravel เปลี่ยน Type ให้เลย
    protected $casts = [
        'favorite' => 'integer',
        // 'deleted' => 'integer',
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
        return $query->whereNotNull('unit_kiosk_idfk')->where('status', 'active');
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

    // 🌟 เพิ่ม Relation ดึงราคาและแต้ม "ล่าสุด/ปัจจุบัน"
    public function currentPriceAndPoint()
    {
        return $this->hasOne(KpTbankItemsPriceAndPoint::class, 'kp_items_idfk', 'id')
                    ->where('status', 'active') // ดึงเฉพาะอันที่ Active
                    ->whereDate('effective_date', '<=', now()) // ต้องถึงวันที่มีผลแล้ว
                    // ->where(function($q) { ...เช็ค end_date เพิ่มเติมได้... })
                    ->orderBy('effective_date', 'desc'); // เอาเรทล่าสุด
    }

    public function items_price_and_point_infos()
    {
        // กรณีที่ 1: ถ้า 1 รายการขยะมีหลายเรทราคา/หลายหน่วย (เช่น ขวดเล็ก ขวดใหญ่) ใช้ hasMany
        // (เปลี่ยนชื่อคลาสโมเดลปลายทางให้ตรงกับที่มีอยู่ในโปรเจกต์ของคุณนะครับ เช่น KpTbankItemsPriceAndPoint หรือ KpTbankItemPrice)
        return $this->hasMany(KpTbankItemsPriceAndPoint::class, 'kp_items_idfk', 'id');
        
        /* กรณีที่ 2: แต่ถ้าในระบบของคุณ 1 รายการขยะ ผูกกับราคาได้เพียงแค่เรทเดียวตายตัว 
        ให้เปลี่ยนไปใช้ hasOne แทนแบบนี้ครับ:
        return $this->hasOne(KpTbankItemsPriceAndPoint::class, 'item_id_fk', 'id');
        */
    }
}
