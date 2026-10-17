<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpShopCategory extends Model
{
    use HasFactory;

    protected $table = 'kp_shop_categories';

    protected $fillable = [
        'category_name',
        'status',
    ];

    /**
     * Get the products for the category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(KpShopProduct::class, 'category_id');
    }
}
