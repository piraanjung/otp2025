<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpBudgetYear extends Model
{
    use HasFactory;
    protected $fillable = [
        "budgetyearname",
        'startdate',
        'enddate',
        'deleted',
        'status'
    ];
    protected $table = "kp_budgetyears";

    public function invoicePeriod()
    {
        return $this->hasMany('App\Models\InvoicePeriod', 'budgetyear_id', 'id');
    }

    public function getCalendarYearAttribute()
    {
        // ตรวจสอบว่า budgetyearname เป็นตัวเลข และเกิน 2500 (สมมติว่าเป็น พ.ศ.)
        if (is_numeric($this->budgetyearname) && (int)$this->budgetyearname > 2500) {
            return (int)$this->budgetyearname - 543;
        }
        // ถ้าเป็น ค.ศ. หรือรูปแบบอื่น ก็คืนค่าเดิม หรือปรับตาม Logic จริง
        return (int)$this->budgetyearname;
    }

    // public function user_payment_per_year()
    // {
    //     return $this->hasMany(UserPaymentPeryear::class, 'budgetyear_id_fk', 'id');
    // }

    public static function get_current_budgetyear()
    {
        return KpBudgetYear::where('status', 'active')->first();
    }
}
