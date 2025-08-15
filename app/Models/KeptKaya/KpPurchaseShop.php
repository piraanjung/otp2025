<?php

    namespace App\Models\KeptKaya;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class KpPurchaseShop extends Model
    {
        use HasFactory;

        protected $table = 'kp_purchase_shops';

        protected $fillable = [
            'shop_name',
            'contact_person',
            'phone',
            'address',
            'status',
            'comment',
        ];
    }
    