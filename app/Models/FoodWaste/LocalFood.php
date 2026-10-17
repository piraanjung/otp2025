<?php

namespace App\Models\FoodWaste;

use Illuminate\Database\Eloquent\Model;

class LocalFood extends Model
{
    protected $table = 'local_foods';
    protected $fillable = [
        'category',
        'menu_name',// ชื่อเมนู
        'calories',
    ];
}
