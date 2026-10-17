<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class meterRecordHistoryExport implements FromView
{
        private  $arr;
        private $usermeterinfos;
        private $inv_period_list;
        private $zones;
        private $budgetyears;
        private $budgetyear_selected_array;
        private $zone_id_array;
        public function __construct($arr)
        {
            $this->arr =  $arr;
            $this->usermeterinfos = $arr['usermeterinfos'];
            $this->inv_period_list = $arr['inv_period_list'];
            $this->zones = $arr['zones'];
            $this->budgetyears = $arr['budgetyears'];
            $this->budgetyear_selected_array = $arr['budgetyear_selected_array'];
            $this->zone_id_array = $arr['zone_id_array'];
        }
        public function view(): View
        {
            ini_set('memory_limit', '512M');
            return view("reports.export_meter_record_history",[
                'usermeterinfos' => $this->usermeterinfos,
                'inv_period_list' => $this->inv_period_list,
                'zones' => $this->zones,
                'budgetyears' => $this->budgetyears,
                'budgetyear_selected_array' => $this->budgetyear_selected_array,
                'zone_id_array' => $this->zone_id_array,
            ]);

        }
    }
