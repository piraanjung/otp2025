<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WelfarePayoutLog extends Model
{
    use HasFactory;

    protected $table = 'welfare_payout_logs';

    protected $fillable = [
        'payout_id',
        'step_id',
        'action_by_user_id',
        'status',
        'attachment_path',
        'note',
    ];

    public function payout()
    {
        return $this->belongsTo(WelfarePayout::class, 'payout_id');
    }

    public function step()
    {
        return $this->belongsTo(ApprovalWorkflowStep::class, 'step_id');
    }

    public function actionBy()
    {
        return $this->belongsTo(User::class, 'action_by_user_id');
    }
}