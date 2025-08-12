<?php

namespace App\Models\KeptKaya;

use App\Models\Admin\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpSellTransaction extends Model
{
    use HasFactory;

    protected $table = 'kp_sell_transactions';

    protected $fillable = [
        'kp_u_trans_no',
        'shop_name',
        'transaction_date',
        'total_weight',
        'total_amount',
        'status',
        'recorder_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'total_weight' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'recorder_id', 'user_id');
    }
    
    public function details(): HasMany
    {
        return $this->hasMany(KpSellDetail::class, 'kp_sell_trans_id');
    }
}
