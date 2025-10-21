<?php

namespace App\Models\KeptKaya;

use App\Models\KeptKaya\WasteBin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteBinSubscription extends Model
{
    use HasFactory;

    protected $table = 'kp_waste_bin_subscriptions';

    protected $fillable = [
        'waste_bin_id',
        'fiscal_year',
        'payrate_permonth_id_fk',
        'annual_fee',
        'month_fee',
        'total_paid_amt',
        'status',
    ];



    /**
     * Get the waste bin associated with the subscription.
     */
    public function wasteBin()
    {
        return $this->belongsTo(WasteBin::class);
    }

    public function payrate_permonth(){
        return $this->belongsTo(WasteBinPayratePerMonth::class, 'payrate_permonth_id_fk', 'id');
    }

    /**
     * Get the payments for the subscription.
     */
    public function payments()
    {
        return $this->hasMany(WasteBinPayment::class, 'wbs_id','id');
    }

    /**
     * Calculate the fiscal year based on a given date.
     * Fiscal year starts in October.
     *
     * @param \Carbon\Carbon|string|null $date
     * @return int
     */
    public static function calculateFiscalYear($date = null)
    {
        $date = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
        $currentYear = $date->year;
        $currentMonth = $date->month;

        // If current month is Oct, Nov, Dec, fiscal year is current year + 1
        if ($currentMonth >= 10) {
            return $currentYear + 1;
        }
        // Otherwise, fiscal year is current year
        return $currentYear;
    }

    /**
     * Get the payment status for a specific month.
     *
     * @param int $month (1-12)
     * @param int $year
     * @return bool
     */
    public function isMonthPaid(int $month, int $year): bool
    {
        return $this->payments()
                    ->where('pay_mon', $month)
                    ->where('pay_yr', $year)
                    ->exists();
    }

    /**
     * Get the total amount paid for a specific month.
     *
     * @param int $month (1-12)
     * @param int $year
     * @return float
     */
    public function getAmountPaidForMonth(int $month, int $year): float
    {
        return $this->payments()
                    ->where('pay_mon', $month)
                    ->where('pay_yr', $year)
                    ->sum('amount_paid');
    }
}
