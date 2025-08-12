<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteBankTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'staff_id',
        'total_member_payout_amount',
        'estimated_factory_revenue',
        'transaction_date',
        'receipt_code',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_member_payout_amount' => 'decimal:2',
        'estimated_factory_revenue' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    // ความสัมพันธ์
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id'); // เจ้าหน้าที่ก็คือ User คนหนึ่ง
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
