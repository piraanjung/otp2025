<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteType extends Model
{
    use HasFactory;

    protected $table = 'kp_waste_types';
    protected $fillable = [
        'name',
        'waste_group_id', // เพิ่มฟิลด์นี้ถ้าคุณสร้างตาราง waste_groups
        'default_unit',
        'factory_buy_price_per_kg',
        'member_buy_price_per_kg',
        'factory_buy_price_per_piece',
        'member_buy_price_per_piece',
        'description',
        'is_recyclable',
    ];

    protected $casts = [
        'factory_buy_price_per_kg' => 'decimal:2',
        'member_buy_price_per_kg' => 'decimal:2',
        'factory_buy_price_per_piece' => 'decimal:2',
        'member_buy_price_per_piece' => 'decimal:2',
        'is_recyclable' => 'boolean',
    ];

    // ความสัมพันธ์
    public function wasteGroup()
    {
        return $this->belongsTo(WasteGroup::class); // เพิ่มความสัมพันธ์ถ้าคุณสร้างตาราง waste_groups
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
