<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvTransactionApprovals extends Model
{
    protected $table = 'inv_transaction_approvals';
    protected $fillable = [
        'ref_no',
        'module_name',
        'approval_workflow_id',
        'step_order',
        'approver_id',
        'status',
        'comment',
        'action_at'
    ];

    

    public function approver() {
        return $this->belongsTo(User::class, 'approver_id');
    }
        public function approval_workflow() {
        return $this->belongsTo(ApprovalWorkflow::class, 'approval_workflow_id');
    }

    /**
     * 💡 เช็กว่า ref_no และ workflow นี้นั้นอนุมัติครบทุกสเต็ปหรือยัง
     */
    public static function isWorkflowCompleted($refNo, $workflowId)
    {
        $pendingCount = self::where('ref_no', $refNo)
            ->where('approval_workflow_id', $workflowId)
            ->where('status', 'PENDING')
            ->count();

        $rejectedCount = self::where('ref_no', $refNo)
            ->where('approval_workflow_id', $workflowId)
            ->where('status', 'REJECTED')
            ->count();

        // ถ้าไม่มี PENDING เหลืออยู่ และไม่มีการถูก REJECT ถือว่าอนุมัติครบถ้วน
        return ($pendingCount === 0 && $rejectedCount === 0);
    }
}
