<?php

namespace App\Http\Controllers\FoodWaste;

use App\Http\Controllers\Controller;
use App\Models\Admin\BudgetYear;
use App\Models\FoodWaste\FoodWasteUserPreference;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request, ){
        if (collect(BudgetYear::where('status', 'active')->first())->isEmpty()) {
            session(['hiddenMenu' => true]);
        }
        $foodwasteUsers = FoodWasteUserPreference::with('foodwaste_bins', 'foodwaste_bins.iotbox_datas')
            ->whereHas('foodwaste_bins')
            ->get();
    $viewModel = [];

         foreach ($foodwasteUsers as $userData) {
            $userId = $userData['user_id'];
            
            $binCharts = []; // เก็บข้อมูลกราฟของแต่ละถังขยะ
            
            // วนลูปผ่านถังขยะทั้งหมดของ User
            foreach ($userData['foodwaste_bins'] as $bin) {
                // ข้อมูลสำหรับกราฟของถังขยะแต่ละใบ
                $binData = [
                    'bin_id' => $bin['id'],
                    'bin_type' => $bin['bin_type'],
                    'labels' => [],
                    'temperatures' => [],
                    'humidities' => [],
                ];

                // วนลูปผ่านข้อมูล IoT ทั้งหมดของถังขยะ
                foreach ($bin['iotbox_datas'] as $data) {
                    // Label จะเป็นแค่เวลา (H:i:s) เพราะเราแยกตามถังขยะแล้ว
                    $timestamp = \Carbon\Carbon::parse($data['created_at'])->format('H:i:s');
                    
                    $binData['labels'][] = $timestamp;
                    $binData['temperatures'][] = (float)$data['temperature'];
                    $binData['humidities'][] = (float)$data['humidity'];
                }
                
                // เก็บข้อมูลถังขยะนี้เข้าใน User Charts
                $binCharts[] = $binData;
            }

            // จัดเก็บข้อมูลของ User นี้
            $viewModel[] = [
                'user_id' => $userId,
                'card_data' => [
                    'ID' => $userData['id'],
                    'Foodwaste Bank' => $userData['is_foodwaste_bank'] ? 'Yes' : 'No',
                    'Created At' => \Carbon\Carbon::parse($userData['created_at'])->format('Y-m-d H:i'),
                    'Total Bins' => count($userData['foodwaste_bins']),
                ],
                'bin_charts' => $binCharts, // เปลี่ยนมาใช้โครงสร้างใหม่
            ];
        }

        $referenceValues = [
            'temp_min' => 20, 'temp_max' => 35,
            'hum_min' => 40, 'hum_max' => 80,
            'methane_min' => 0, 'methane_max' => 5, // สมมติค่า PPM
            'weight_min' => 0, 'weight_max' => 50,  // สมมติค่า Kg
        ];
        
        // โครงสร้างสำหรับกราฟรวม (4 ตัว)
        $globalTemperatureData = ['labels' => [], 'datasets' => [], 'references' => $referenceValues];
        $globalHumidityData = ['labels' => [], 'datasets' => [], 'references' => $referenceValues];
        $globalMethaneData = ['labels' => [], 'datasets' => [], 'references' => $referenceValues];
        $globalWeightData = ['labels' => [], 'datasets' => [], 'references' => $referenceValues];

        $binTempPoints = [];
        $binHumPoints = [];
        $binMethanePoints = [];
        $binWeightPoints = [];
        $allTimestamps = []; 

        foreach ($foodwasteUsers as $userData) {
            $userId = $userData['user_id'];
            
            foreach ($userData['foodwaste_bins'] as $bin) {
                
                $binKey = "U{$userId}-B{$bin['id']} ({$bin['bin_type']})";
                
                $binTempPoints[$binKey] = ['label' => $binKey, 'dataPoints' => []];
                $binHumPoints[$binKey] = ['label' => $binKey, 'dataPoints' => []];
                $binMethanePoints[$binKey] = ['label' => $binKey, 'dataPoints' => []];
                $binWeightPoints[$binKey] = ['label' => $binKey, 'dataPoints' => []];

                foreach ($bin['iotbox_datas'] as $data) {
                    $timestamp = Carbon::parse($data['created_at'])->format('H:i:s');
                    
                    $allTimestamps[$timestamp] = $timestamp;
                    
                    // เก็บค่าแต่ละพารามิเตอร์
                    $binTempPoints[$binKey]['dataPoints'][$timestamp] = (float)$data['temperature'];
                    $binHumPoints[$binKey]['dataPoints'][$timestamp] = (float)$data['humidity'];
                    $binMethanePoints[$binKey]['dataPoints'][$timestamp] = (float)$data['methane_gas'];
                    $binWeightPoints[$binKey]['dataPoints'][$timestamp] = (float)$data['weight'];
                }
            }
        }

        // Finalize structure: จัดเรียง Labels และสร้าง Datasets ให้ครบทุกพารามิเตอร์
        ksort($allTimestamps);
        $labels = array_values($allTimestamps);

        // ฟังก์ชันช่วยสร้าง Datasets
        $createDatasets = function (&$globalData, $binPoints, $labels) {
            $globalData['labels'] = $labels;
            foreach ($binPoints as $binKey => $binData) {
                $dataArray = [];
                foreach ($labels as $timestamp) {
                    $dataArray[] = $binData['dataPoints'][$timestamp] ?? null; 
                }
                $globalData['datasets'][] = [
                    'label' => $binData['label'],
                    'data' => $dataArray,
                ];
            }
        };

        $createDatasets($globalTemperatureData, $binTempPoints, $labels);
        $createDatasets($globalHumidityData, $binHumPoints, $labels);
        $createDatasets($globalMethaneData, $binMethanePoints, $labels);
        $createDatasets($globalWeightData, $binWeightPoints, $labels);
        
        // ส่งข้อมูลกราฟทั้งสี่ชุดไปยัง View
        return view('foodwaste.dashboard', compact('viewModel','globalTemperatureData', 'globalHumidityData', 'globalMethaneData', 'globalWeightData'));
    }
    
}
