<?php

namespace App\Models;

use App\Models\Admin\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
class InvTransaction extends Model
{
    use HasFactory; use BelongsToOrganization;

     protected $table = 'inv_transactions';

    protected $fillable = [
        'org_id_fk',
        'requester_id_fk',
        'ref_no',
        'inv_item_id_fk',
        'quantity',
        'purpose',
        'status',
        'current_step',
        'transaction_date',
        'approve_workflow_id_fk'
        
    ];

    // ✅ เชื่อมกลับไปหา Organization เดิม
    public function organization() {
        return $this->belongsTo(Organization::class, 'org_id_fk', 'id');
    }

    public function item() {
        return $this->belongsTo(InvItem::class, 'inv_item_id_fk', 'id');
    }

    public function requester() {
        return $this->belongsTo(User::class, 'requester_id_fk', 'id');
    }

    public function approver_user()
    {
        // หมายเหตุ: หากในตาราง inv_transactions ของคุณใช้ชื่อฟิลด์เก็บไอดีผู้อนุมัติว่า 'approved_by' 
        // ให้ใช้ 'approved_by' แต่ถ้าใช้ชื่ออื่น (เช่น 'approver_id') ให้เปลี่ยนตามฐานข้อมูลจริงครับ
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function detail() {
        return $this->belongsTo(InvItemDetail::class, 'inv_item_id_fk', 'inv_item_id_fk');
    }

    public function approve_workflow(){
        return $this->belongsTo(ApprovalWorkflow::class, 'approve_workflow_id_fk');
    }
}
