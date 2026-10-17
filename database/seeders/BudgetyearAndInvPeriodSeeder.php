<?php

namespace Database\Seeders;

use App\Models\Admin\BudgetYear;
use App\Models\Tabwater\TwInvoicePeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BudgetyearAndInvPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BudgetYear::create([
             'budgetyear_name' => '2568',
        'startdate' => '2024-10-01',
        'enddate' => '2024-10-01',
        'status' => 'active'
        ]);

        $invPs = [
            [
                'inv_p_name'=>'10-2567',
                'budgetyear_id'=> 1,
                'start_date' =>'2024-10-01',
                'end_date'=>'2024-10-31',
                'status'=>'inactive',
                'deleted' =>'0'
            ],
            [
                'inv_p_name'=>'11-2567',
                'budgetyear_id'=> 1,
                'start_date' =>'2024-11-01',
                'end_date'=>'2024-11-30',
                'status'=>'inactive',
                'deleted' =>'0'
            ],
            [
                'inv_p_name'=>'12-2567',
                'budgetyear_id'=> 1,
                'start_date' =>'2024-12-01',
                'end_date'=>'2024-12-31',
                'status'=>'inactive',
                'deleted' =>'0'
            ],
            [
                'inv_p_name'=>'01-2568',
                'budgetyear_id'=> 1,
                'start_date' =>'2025-01-01',
                'end_date'=>'2025-01-31',
                'status'=>'inactive',
                'deleted' =>'0'
            ],
            [
                'inv_p_name'=>'02-2568',
                'budgetyear_id'=> 1,
                'start_date' =>'2025-02-01',
                'end_date'=>'2025-02-28',
                'status'=>'inactive',
                'deleted' =>'0'
            ],
            [
                'inv_p_name'=>'03-2568',
                'budgetyear_id'=> 1,
                'start_date' =>'2025-03-01',
                'end_date'=>'2025-03-31',
                'status'=>'inactive',
                'deleted' =>'0'
            ],
            [
                'inv_p_name'=>'04-2568',
                'budgetyear_id'=> 1,
                'start_date' =>'2025-04-01',
                'end_date'=>'2025-04-30',
                'status'=>'active',
                'deleted' =>'0'
            ]
        ];

        foreach($invPs as $inv){
            TwInvoicePeriod::create([
                "inv_p_name" =>$inv['inv_p_name'],
                "budgetyear_id" =>$inv['budgetyear_id'],
                "startdate" => $inv['start_date'],
                "enddate" => $inv['end_date'],
                'deleted' => $inv['deleted'],
                "status" => $inv['status']
            ]);
        }
        
    }
}
