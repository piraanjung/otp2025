<?php

namespace App\Models\Tabwater;

use App\Models\Admin\BudgetYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization; // 1. เรียกใช้ Trait
class TwInvoicePeriod extends Model
{
    use HasFactory; 
    use BelongsToOrganization;

    protected $fillable = [ 'id', 'org_id_fk', "inv_p_name","budgetyear_id","startdate","enddate","status"];
    protected $table = "invoice_period";

    public function budgetyear()
    {
        return $this->belongsTo(BudgetYear::class, 'budgetyear_id', 'id');
    }

    // เปลี่ยนเป็น Static function จะเรียกใช้ง่ายกว่า หรือถ้าเรียกผ่าน instance ก็ต้องแก้ query
    public static function get_curr_inv_pd()
    {
        // เมื่อใช้ Trait BelongsToOrganization แล้ว
        // คำสั่งนี้จะ where('org_id_fk', ...) และเลือก Connection ให้เองอัตโนมัติ
        return self::where('status', 'active')
            ->latest('id') // ควรเอาอันล่าสุด เผื่อมี active ค้างหลายอัน (กัน error)
            ->first();
    }
}
