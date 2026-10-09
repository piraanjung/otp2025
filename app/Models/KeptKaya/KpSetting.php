<?php

namespace App\Models\KeptKaya;

use Illuminate\Database\Eloquent\Model;

class KpSetting extends Model
{
    protected $table = 'kp_settings';

    protected $fillable = [
        'org_id_fk',
        'key_name',
        'key_value',
        'description',
    ];

    /**
     * Helper Function สำหรับดึงค่า Setting
     */
    public static function getValue($orgId, $key, $default = null)
    {
        $setting = self::where('org_id_fk', $orgId)->where('key_name', $key)->first();
        return $setting && !is_null($setting->key_value) ? $setting->key_value : $default;
    }
}