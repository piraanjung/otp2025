<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpShopOrderDetail extends Model
{
    use HasFactory;

    protected $table = 'kp_shop_order_details';

    protected $fillable = [
        'kp_shop_order_id',
        'kp_shop_product_id',
        'order_type',
        'quantity',
        'points_per_unit',
        'cash_per_unit',
        'total_points',
        'status',
        'total_cash',
    ];

    /**
     * Get the order that owns the order detail.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(KpShopOrder::class, 'order_id');
    }

    /**
     * Get the product that the order detail belongs to.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(KpShopProduct::class, 'kp_shop_product_id');
    }
}
