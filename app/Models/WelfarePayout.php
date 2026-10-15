<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelfarePayout extends Model
{
    use HasFactory;

    protected $table = 'welfare_payouts';

    protected $fillable = [
        'org_id_fk',
        'user_id',
        'deceased_name',
        'beneficiary_name',
        'amount',
        'requester_id',
        'current_step_id',
        'status',
        'paid_at',
        'note',
        'death_certificate_img',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function currentStep()
    {
        return $this->belongsTo(ApprovalWorkflowStep::class, 'current_step_id');
    }

    public function logs()
    {
        return $this->hasMany(WelfarePayoutLog::class, 'payout_id')->orderBy('created_at', 'desc');
    }

    public function contributions()
    {
        return $this->hasMany(WelfareContribution::class, 'payout_id');
    }
}