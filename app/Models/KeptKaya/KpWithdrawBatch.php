<?php

namespace App\Models\KeptKaya;

use App\Models\Admin\Organization;
use App\Models\User;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpWithdrawBatch extends Model
{
    use HasFactory;
    use BelongsToOrganization;

    protected $table = 'kp_withdraw_batches';

    protected $fillable = [
        'org_id_fk',
        'batch_no',
        'cutoff_date',
        'payout_date',
        'total_requests',
        'total_amount',
        'status',
        'current_step_order',
        'created_by',
    ];

    protected $casts = [
        'cutoff_date' => 'date',
        'payout_date' => 'date',
        'total_requests' => 'integer',
        'total_amount' => 'decimal:2',
    ];

    public function requests()
    {
        return $this->hasMany(KpMoneyRequest::class, 'batch_id_fk', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function org()
    {
        return $this->belongsTo(Organization::class, 'org_id_fk', 'id');
    }
}