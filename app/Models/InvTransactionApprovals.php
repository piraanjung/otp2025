<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvTransactionApprovals extends Model
{
    protected $table = 'inv_transaction_approvals';
    protected $fillable = [
        'ref_no',
        'step_order',
        'approver_id',
        'status',
        'comment',
        'action_at'
    ];
}
