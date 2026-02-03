<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class KpTbankItemsPriceAndPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'kp_items_idfk',
        'org_id_fk',
        'price_from_dealer',
        'price_for_member',
        'effective_date',
        'end_date',
        'point',
        'type',
        'kp_units_idfk',
        'status',
        'deleted',
        'recorder_id'
    ];
    protected $table = 'kp_tbank_items_pricepoint';


    public function kp_units_info(){
        return $this->belongsTo(KpTbankUnits::class, 'kp_units_idfk', 'id');
    }

    public function item(){
            return $this->belongsTo(KpTbankItems::class, 'kp_items_idfk', 'id');
        }


        public function recorder(){
            return $this->belongsTo(User::class, 'recorder_id', 'id');
        }

        // Logic to ensure only one active price per item at any given time
        protected static function boot()
{
    parent::boot();

    static::saving(function ($priceConfig) {
        // ตรวจสอบว่าถ้าสถานะเป็น active ให้ปิดราคาเก่า "ที่อยู่ในหน่วยเดียวกัน" เท่านั้น
        if ($priceConfig->status == 'active') { // สมมติใช้ field status แทน is_active ตาม fillable
            self::where('kp_items_idfk', $priceConfig->kp_items_idfk)
                ->where('kp_units_idfk', $priceConfig->kp_units_idfk) // 👈 เพิ่มเงื่อนไขหน่วยนับ
                ->where('status', 'active')
                ->where('id', '!=', $priceConfig->id)
                ->update([
                    'status' => 'inactive',
                    'end_date' => Carbon::now()
                ]);
        }
    });
}



}

