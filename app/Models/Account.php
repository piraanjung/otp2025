<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [ 'deposit', 'payee'];
    protected $table = "accounts";

    public function invoice(){
        return $this->hasMany(Invoice::class, 'id');
    }
    public function user_payee(){
        return $this->belongsTo(User::class,'payee', 'id');
    }
}
