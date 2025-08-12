<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cutmeter extends Model
{
    use HasFactory;
    protected $table = 'cutmeter';
    protected $fillable = [ 'meter_id_fk', 'id', 'progress', 'status', 'owe_count'];


    public function usermeterinfo(){
        return $this->belongsTo(UserMerterInfo::class, 'meter_id_fk', 'meter_id');
    }
}
