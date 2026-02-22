<?php

namespace App\Models;

use App\Models\Admin\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvCategory extends Model
{
    use HasFactory;

    protected $table = 'inv_categories';

    protected $fillable = [
        'org_id_fk', 'name'
    ];

    // ✅ เชื่อมกลับไปหา Organization เดิม
    public function organization() {
        return $this->belongsTo(Organization::class, 'org_id_fk', 'id');
    }

    public function items() { return $this->hasMany(InvItem::class, 'inv_category_id_fk'); }
}
