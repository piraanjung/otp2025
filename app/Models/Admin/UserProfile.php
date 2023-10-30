<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'id_card',
        'phone',
    ];

    protected $table = 'user_profile';

    public function zone(){
        return $this->belongsTo('App\Models\Zone', 'zone_id', 'id');
    }
}
