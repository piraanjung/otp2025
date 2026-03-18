<?php
namespace App\Models\FoodWaste;

use App\Models\FoodWaste\FoodWasteIssueReport;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;

class FoodWasteIssueType extends Model
{
    use BelongsToOrganization;
    protected $table = 'foodwaste_issue_types'; // ระบุชื่อตารางให้ชัดเจน
    protected $fillable = ['name', 'is_active','org_id_fk'];

    // 1 หมวดหมู่ มีการแจ้งปัญหาหลายรายการ
    public function issueReports()
    {
        return $this->hasMany(FoodWasteIssueReport::class, 'foodwaste_issue_type_id');
    }
}
