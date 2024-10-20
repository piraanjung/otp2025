<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMerterInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        "meter_id","meter_address",
        "user_id", "meternumber", "metertype_id", "undertake_zone_id",  "undertake_subzone_id", "acceptance_date",
        "status",  "payment_id", "discounttype", "recorder_id", 'cutmeter'
    ];
    protected $table = "user_meter_infos";

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function meter_type()
    {
        return $this->belongsTo(MeterType::class, 'metertype_id', 'id');
    }

    public function undertake_zone()
    {
        return $this->belongsTo(Zone::class, 'undertake_zone_id');
    }

    public function undertake_subzone()
    {
        return $this->belongsTo(Subzone::class, 'undertake_subzone_id');
    }

    public function cutmeter()
    {
        return $this->hasMany(Cutmeter::class, 'meter_id_fk', 'meter_id');
    }

    public function invoice()
    {
        return $this->hasMany(Invoice::class, 'meter_id_fk', 'meter_id');
    }

    public function invoice_history()
    {
        return $this->hasMany(InvoiceHistoty::class, 'meter_id_fk', 'meter_id');
    }

    public function invoice_not_paid()
    {
        return $this->hasMany(Invoice::class, 'meter_id_fk', 'meter_id');
    }

    public function invoice_last_inctive_inv_period()
    {
        return $this->hasMany(Invoice::class, 'meter_id_fk', 'meter_id');
    }
    public function invoice_by_user_id()
    {
        return $this->hasMany(Invoice::class, 'meter_id_fk', 'meter_id');
    }

}
