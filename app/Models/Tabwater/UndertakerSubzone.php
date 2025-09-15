<?php

namespace App\Models\Tabwater;

use App\Models\Admin\Subzone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UndertakerSubzone extends Model
{
    use HasFactory;
    protected $table = 'undertaker_subzone';

    protected $fillable =[
        'id',
        'twman_id',
        'subzone_id'
    ];

    public function twman_info(){
        return $this->belongsTo(User::class, 'twman_id', 'id');
    }

    public function subzone(){
        return $this->belongsTo(Subzone::class,'subzone_id','id');
    }

     public function subzoneInvoiceStatusCount($status, $subzone_id, $inv_period){
        return UserMerterInfo::where('undertake_subzone_id', $subzone_id)
        ->whereHas('invoice', function($q) use ($status, $inv_period) {
            $q->where('status', $status)
              ->where('inv_period_id_fk', $inv_period);
        })
        ->count();
    }

    public function subzoneMemberCount($subzone_id){
        return UserMerterInfo::where('undertake_subzone_id', $subzone_id)->where('status', 'active')->count();
    }

}
