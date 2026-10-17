<?php

namespace App\Models;

use App\Models\Admin\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalWorkflow extends Model
{
    use HasFactory;

    protected $table = 'approval_workflows';

    // เพิ่ม fillable ป้องกัน Mass Assignment Exception
    protected $fillable = [
        'name',
        'org_id_fk',
        'description',
        'is_active',
    ];

    // ➕ เพิ่มความสัมพันธ์เชื่อมไปยังขั้นตอน (Steps)
    public function steps()
    {
        return $this->hasMany(ApprovalWorkflowStep::class, 'workflow_id');
    }

    public function organization(){
        return $this->belongsTo(Organization::class, 'org_id_fk');
    }
}
