<?php

namespace App\Models\Tabwater;

use App\Models\Admin\ZonBlocks;
use App\Models\Admin\Zones;
use App\Models\Tabwater\TwMeterReading;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwMeters extends Model
{
    use HasFactory;

    protected $table = 'tw_meters'; // กำหนดชื่อตารางให้ถูกต้อง

    protected $fillable = [
        'id',
        'user_id',
        'factory_no',
        'initial_reading', //เลขจดมิเตอร์เริ่มต้นเมื่อติดตั้ง/เปิดใช้งานมิเตอร์ครั้งแรก
        'middle_name',
        'meter_address',
        'undertake_zone_id',
        'undertake_zone_block_id',
        'acceptace_date',
        'status',
        'comment',
        'metertype_id',
        'current_active_reading',
        'owe_count',
        'cutmeter',
        'payment_id',
        'discounttype_id',
        'recorder_id',
    ];

    protected $casts = [
        'acceptace_date' => 'date',
        'cutmeter' => 'boolean',
        'current_active_reading' => 'float:2', // <-- เพิ่ม cast

    ];

    // --- Relationships ---

    // ผู้ใช้งาน/ลูกค้าหลัก
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ผู้บันทึก/พนักงาน
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorder_id');
    }


    // โซนที่รับผิดชอบ
    public function undertakeZone()
    {
        return $this->belongsTo(Zones::class, 'undertake_zone_id');
    }

    // โซนย่อยที่รับผิดชอบ
    public function undertakeZoneBlock()
    {
        return $this->belongsTo(ZonBlocks::class, 'undertake_zone_block_id'); // Assuming SubZone is the correct model
    }

    // ประเภทมิเตอร์
    public function meterType()
    {
        return $this->belongsTo(TwMeterType::class, 'metertype_id');
    }

    // ประเภทการชำระเงิน
    public function paymentType()
    {
        return $this->belongsTo(TwPaymentType::class, 'payment_id');
    }

    // ประเภทส่วนลด
    public function discountType(): BelongsTo // Uncomment ถ้ามี Model TwDiscountType
    {
        return $this->belongsTo(TwDiscountType::class, 'discounttype_id');
    }
    public function invoices()
    {
        return $this->hasMany(TwInvoice::class, 'meter_id_fk');
    }

    public function meterReadings()
    {
        return $this->hasMany(TwMeterReading::class, 'meter_id_fk');
    }
}
