<?php

namespace App\Models\FoodWaste;

use App\Models\Admin\Staff;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodWasteIssueReport extends Model
{
    use HasFactory;
    protected $table = 'foodwaste_issue_reports';
    protected $fillable = [
        'user_id',
        'batch_id',
        'issue_type_id',
        'description',
        'status',
        'assigned_staff_id', // 🌟 เพิ่มตรงนี้
        'admin_note',
        'staff_comment'
    ];

    // ความสัมพันธ์เชื่อมกลับไปหา User
    public function user() {
        return $this->belongsTo(User::class);
    }
    // เชื่อมไปหาหมวดหมู่ปัญหา (FoodwasteIssueType)
    public function issueType()
    {
        return $this->belongsTo(FoodwasteIssueType::class, 'issue_type_id');
    }

    // ความสัมพันธ์เชื่อมไปหา Batch
    public function batch() {
        return $this->belongsTo(CompostBatches::class, 'batch_id');
    }

    public function assignedStaff() {
        return $this->belongsTo(Staff::class, 'assigned_staff_id');
    }
}
