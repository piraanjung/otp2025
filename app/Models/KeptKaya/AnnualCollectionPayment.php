<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualCollectionPayment extends Model
{
    use HasFactory;

    protected $table = 'annual_collection_payments';

    protected $fillable = [
        'user_id',
        'number_of_bins',
        'amount_due',
        'amount_paid',
        'due_date',
        'payment_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount_due' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}