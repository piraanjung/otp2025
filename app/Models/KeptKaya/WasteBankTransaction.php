<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteBankTransaction extends Model
{
    use HasFactory;

    protected $table = 'kp_waste_bank_transactions';

    protected $fillable = [
        'user_id',
        'weight_kg',
        'waste_type',
        'price_per_kg',
        'total_amount',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'weight_kg' => 'decimal:2',
        'price_per_kg' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}