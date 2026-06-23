<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpShopProduct extends Model
{
    use HasFactory;

    protected $table = 'kp_shop_products';

    protected $fillable = [
        'category_id',
        'product_name',
        'product_code',
        'kp_shop_category_id',
        'description',
        'image_path',
        'point_price',
        'cash_price',
        'stock',
        'status',
    ];
    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(KpShopCategory::class, 'category_id');
    }

    /**
     * Get the order details for the product.
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(KpShopOrderDetail::class, 'kp_shop_product_id');
    }
}
