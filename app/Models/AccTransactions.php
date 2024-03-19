<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccTransactions extends Model
{
    use HasFactory;

    protected $fillable = ['id' , 'user_id_fk','vatsum', 'paidsum', 'totalpaidsum', 'cashier'];
}
