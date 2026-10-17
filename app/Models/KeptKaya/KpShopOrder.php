<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpShopOrder extends Model
{
    use HasFactory;

    protected $table = 'kp_shop_orders';

    protected $fillable = [
        'order_no',
        'user_wpref_id',
        'total_points',
        'total_cash',
        'order_status',
        'recorder_id',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kp_user_id');
    }
    
    /**
     * Get the staff who recorded the order.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorder_id');
    }
    
    /**
     * Get the details for the order.
     */
    public function details(): HasMany
    {
        return $this->hasMany(KpShopOrderDetail::class, 'kp_shop_order_id');
    }
}
