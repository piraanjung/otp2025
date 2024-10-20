<?php

namespace App\Exports;

use App\Models\UserMerterInfo;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\Api\OwepaperController;
class ReportOweUserExport implements FromView
{
    private  $arr;
    private $owes;
    private $budgetyears;
    private $budgetyears_selected;
    private $selected_inv_periods;
    private $reservemeter_sum;
    private $crudetotal_sum;
    private $owe_zones;
    private $zones;
    private $subzones;
    private $owe_inv_periods;
    private $showDetails;
    public function __construct($arr, $showDetails)
    {
        $this->arr =  $arr;
        $this->owes = $this->arr['owes'];
        $this->budgetyears = $this->arr['budgetyears'];
        $this->budgetyears_selected = $this->arr['budgetyears_selected'];
        $this->budgetyears_selected = $this->arr['budgetyears_selected'];
        $this->reservemeter_sum = $this->arr['reservemeter_sum'];
        $this->crudetotal_sum = $this->arr['crudetotal_sum'];
        $this->owe_zones = $this->arr['owe_zones'];
        $this->zones = $this->arr['zones'];
        $this->subzones = $this->arr['subzones'];
        $this->owe_inv_periods = $this->arr['owe_inv_periods'];
        $this->showDetails = $showDetails;
    }
    public function view(): View
    {

        return view("reports.export_owe",[
            'owes' => $this->owes,
            'budgetyears' => $this->budgetyears,
            'budgetyears_selected' => $this->budgetyears_selected,
            'selected_inv_periods' => $this->selected_inv_periods,
            'reservemeter_sum' => $this->reservemeter_sum,
            'crudetotal_sum' => $this->crudetotal_sum,
            'owe_zones' => $this->owe_zones,
            'zones' => $this->zones,
            'subzones' => $this->subzones,
            'owe_inv_periods' =>$this->owe_inv_periods,
            'show_details' => $this->showDetails
        ]);

    }
}
