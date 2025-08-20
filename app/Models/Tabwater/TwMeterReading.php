<?php

namespace App\Models\Tabwater;


use App\Models\Tabwater\TwMeters;
use App\Models\Tabwater\TwPeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TwMeterReading extends Model
{
    use HasFactory;

    protected $table = 'tw_meter_readings'; // กำหนดชื่อตารางให้ตรงกัน

    protected $fillable = [
        'id',
        'meter_id_fk',
        'inv_period_id_fk',
        'reading_date',
        'reading_value',
        'previous_reading_value',
        'comment',
        'recorder_id',
    ];

    protected $casts = [
        'reading_date' => 'date',
        'reading_value' => 'float:2',
        'previous_reading_value' => 'float:2',
    ];

    // กำหนดความสัมพันธ์ (Relationships)
    public function meter()
    {
        return $this->belongsTo(TwMeters::class, 'meter_id_fk', 'id');
    }

    public function period()
    {
        return $this->belongsTo(TwPeriod::class, 'inv_period_id_fk');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorder_id'); // สมมติว่ามี Model User
    }
    public function invoice(): HasOne // เพิ่มความสัมพันธ์ไปยัง TwInvoice
    {
        return $this->hasOne(TwInvoice::class, 'meter_reading_id');
    }
}
