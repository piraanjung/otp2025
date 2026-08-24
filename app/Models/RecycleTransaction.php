<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecycleTransaction extends Model
{
    protected $table = 'recycle_transactions';

    protected $fillable = [
        'user_id', 'type', 'amount', 'weight_kg', 'note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
