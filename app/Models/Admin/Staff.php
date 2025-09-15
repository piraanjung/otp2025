<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;
    protected $table = 'staffs';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'status',
        'deleted',
        'created_at',
        'updated_at',
    ];

    function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
