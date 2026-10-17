<?php

namespace App\Models\FoodWaste;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CompostBatches extends Model
{
    protected $table = 'foodwaste_compost_batches';
    protected $fillable = [
    'batch_code','user_id', 'start_date', 'closed_date','status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'closed_date' => 'date',
    ];

     public function wasteLogs()
    {
        // 1 ล็อต (Batch) มีรายการเทขยะได้หลายครั้ง (HasMany)
        return $this->hasMany(FoodWasteLog::class, 'batch_id');
    }

    public function user(){
        return $this->hasOne(User::class,'id');
    }
}
