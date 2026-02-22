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
        'user_id_fk',
        'requester_name',
        'approver_name',
        'inv_item_id_fk',
        'inv_item_detail_id_fk',
        'quantity',
        'purpose',
        'status',
        'current_step',
        'approved_by_user_id_fk',
        'transaction_date',
        
    ];

    // ✅ เชื่อมกลับไปหา Organization เดิม
    public function organization() {
        return $this->belongsTo(Organization::class, 'org_id_fk', 'id');
    }

    public function item() {
        return $this->belongsTo(InvItem::class, 'inv_item_id_fk', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id_fk', 'id');
    }

    public function detail() {
        return $this->belongsTo(InvItemDetail::class, 'inv_item_detail_id_fk', 'id');
    }
}
