<?php

namespace App\Models\Tabwater;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwCutmeter extends Model
{
    use HasFactory;
    protected $table = 'tw_cutmeter';
    protected $fillable = [ 'meter_id_fk', 'id', 'progress', 'status', 'owe_count', 'warning_print'];


    public function usermeterinfo(){
        return $this->belongsTo(TwUsersInfo::class, 'meter_id_fk', 'meter_id');
    }
}
