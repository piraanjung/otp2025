<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Organization; // 👈 import Model เดิมของคุณ

class InvItem extends Model
{
    use HasFactory;

    protected $table = 'inv_items';

    protected $fillable = [
        'org_id_fk', 'inv_category_id_fk', 'name', 'code', 
        'min_stock', 'unit', 'is_chemical', 'return_required', 
        'image_path', 'cas_number', 'expire_date'
    ];

    // ✅ เชื่อมกลับไปหา Organization เดิม
    public function organization() {
        return $this->belongsTo(Organization::class, 'org_id_fk', 'id');
    }

    // เชื่อมหมวดหมู่
    public function category() {
        return $this->belongsTo(InvCategory::class, 'inv_category_id_fk', 'id');
    }
    public function details()
    {
        return $this->hasMany(InvItemDetail::class, 'inv_item_id_fk', 'id');
    }
    public function hazards()
    {
        return $this->belongsToMany(InvHazardLevel::class, 'inv_item_hazard', 'inv_item_id', 'inv_hazard_level_id');
    }

    // 2. ฟังก์ชันพิเศษ: นับจำนวนขวดที่ยังมีของอยู่ (Active)
    public function getActiveBottlesCountAttribute()
    {
        return $this->details()->where('status', 'ACTIVE')->count();
    }

    // 3. ฟังก์ชันพิเศษ: รวมปริมาณคงเหลือทั้งหมด (Total Volume)
    public function getTotalStockAttribute()
    {
        return $this->details()->where('status', 'ACTIVE')->sum('current_qty');
    }

    public function transactions()
    {
        return $this->hasMany(InvTransaction::class, 'inv_item_id_fk');
    }

    public function getPendingQtyAttribute()
    {
        return $this->transactions()
                    ->where('status', 'PENDING')
                    ->sum('quantity');
    }
}