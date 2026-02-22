<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $table = 'provinces';

    protected $fillable =[
        'province_name',
        'go_id'
    ];
}
