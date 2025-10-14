<?php

namespace App\Models\Tabwater;

use App\Models\Admin\Subzone;
use App\Models\Admin\Zone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwUsersInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        "id","meter_address",'submeter_name',
        "user_id", "meternumber", "metertype_id", "undertake_zone_id",  "undertake_subzone_id", "acceptance_date",
        "status",  "payment_id", "discounttype", "recorder_id", 'cutmeter', 'factory_no', 'inv_no_index', 'last_meter_recording'
    ];
    protected $table = "tw_users_infos";

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function meter_type()
    {
        return $this->belongsTo(TwMeterType::class, 'metertype_id', 'id');
    }

    public function undertake_zone()
    {
        return $this->belongsTo(Zone::class, 'undertake_zone_id');
    }

    public function undertake_subzone()
    {
        return $this->belongsTo(Subzone::class, 'undertake_subzone_id','id');
    }

    public function cutmeter()
    {
        return $this->hasMany(TwCutmeter::class, 'meter_id_fk', 'id');
    }

    public function invoice()
    {
        return $this->hasMany(TwInvoice::class, 'meter_id_fk', 'id');
    }

     public function invoice_temp()
    {
        return $this->hasMany(TwInvoiceTemp::class, 'meter_id_fk', 'id');
    }

    public function invoice_currrent_inv_period()
    {
        return $this->hasMany(TwInvoice::class, 'meter_id_fk', 'id');
    }

    public function invoice_history()
    {
        return $this->hasMany(TwInvoiceHistoty::class, 'meter_id_fk', 'id');
    }

    public function invoice_not_paid()
    {
        return $this->hasMany(TwInvoice::class, 'meter_id_fk', 'id');
    }

    public function invoice_last_inctive_inv_period()
    {
        return $this->hasMany(TwInvoice::class, 'meter_id_fk', 'id');
    }
    public function invoice_by_user_id()
    {
        return $this->hasMany(TwInvoice::class, 'meter_id_fk', 'id');
    }

}
