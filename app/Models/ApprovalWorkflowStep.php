<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalWorkflowStep extends Model
{
    use HasFactory;

    protected $table = 'approval_workflow_steps';

    protected $fillable = [
        'workflow_id',
        'step_order', // 👈 เพิ่มตรงนี้เข้าไปครับ
        'role_name',
        'specific_user_id',
    ];

    public function workflow()
    {
        return $this->belongsTo(ApprovalWorkflow::class, 'workflow_id');
    }

    public function specificUser()
    {
        return $this->belongsTo(User::class, 'specific_user_id');
    }
}