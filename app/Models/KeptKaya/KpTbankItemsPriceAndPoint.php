<?php

namespace App\Models\KeptKaya;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class KpTbankItemsPriceAndPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'kp_items_idfk',
        'price_from_dealer',
        'price_for_member',
        'effective_date',
        'end_date',
        'point',
        'type',
        'kp_units_idfk',
        'status',
        'deleted',
        'recorder_id'
    ];
    protected $table = 'kp_tbank_items_pricepoint';

    public function kp_units_info(){
        return $this->belongsTo(KpTbankUnits::class, 'kp_units_idfk', 'id');
    }

    public function item(){
            return $this->belongsTo(KpTbankItems::class, 'kp_items_idfk', 'id');
        }

    
        public function recorder(){
            return $this->belongsTo(User::class, 'recorder_id', 'id');
        }
        
        // Logic to ensure only one active price per item at any given time
        public static function boot()
        {
            parent::boot();

            static::saving(function ($priceConfig) {
                if ($priceConfig->is_active) {
                    KpTbankItemsPriceAndPoint::where('kp_items_idfk', $priceConfig->kp_items_idfk)
                                             ->where('is_active', true)
                                             ->where('id', '!=', $priceConfig->id)
                                             ->update([
                                                 'is_active' => false,
                                                 'end_date' => Carbon::parse($priceConfig->effective_date)->subDay()
                                             ]);
                }
            });
        }
    


}
