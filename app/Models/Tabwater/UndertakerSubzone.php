<?php

namespace App\Models\Tabwater;

use App\Models\Admin\Subzone;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UndertakerSubzone extends Model
{
    use HasFactory;
    protected $table = 'undertaker_subzones';

    public function twman_info(){
        return $this->belongsTo(User::class, 'twman_id', 'id');
    }

    public function subzone(){
        return $this->belongsTo(Subzone::class,'subzone_id','id');
    }

}
