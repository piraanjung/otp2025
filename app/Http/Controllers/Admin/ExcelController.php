<?php

namespace App\Http\Controllers\Admin;

use App\Imports\UsersImport;
use App\Models\Invoice;
use App\Models\InvoiceOld;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FunctionsController;
use App\Models\SequenceNumber;
use App\Models\UserMerterInfo;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as ReaderXlsx;


class ExcelController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view("admin.excel.index", compact("users"));
    }
    public function create()
    {
        return view("admin.excel.create");
    }
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Get the uploaded file
        return   $file = $request->file('file');

        // Process the Excel file
        Excel::import(new UsersImport, $file);

        // return redirect()->back()->with('success', 'Excel file imported successfully!');
        return 'saved';
    }

    public function store_invoice(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        //Allowed mime types
        $excelMimes = array('text/xls', 'text/xlsx', 'application/excel', 'application/vnd.msexcel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Validate whether selected file is a Excel file
        if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $excelMimes)) {
            // If the file is uploaded
            if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                $reader = new ReaderXlsx();
                $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
                // $worksheet =  $spreadsheet->getActiveSheet();
                $arr = [];
                $err = [];
                $aaa = [];
                for ($i = 0; $i < 1; $i++) {
                    $worksheet =   $spreadsheet->getSheet($i);
                    $worksheet_arr =  $worksheet->toArray();
                    // Remove header row
                    for ($j = 0; $j < 4; $j++) {
                        unset($worksheet_arr[$j]);
                    }

                    // Get the uploaded file

                    $invModel = new Invoice();
                    $fn = new FunctionsController();
                    foreach ($worksheet_arr as $worksheet) {
                        try {
                            $usermeterInfo = UserMerterInfo::where('factory_no', $worksheet[6])->get(['meter_id', 'user_id'])[0];
                            UserMerterInfo::where('factory_no', $worksheet[6])->update([
                                'meter_address' => $worksheet[4]
                            ]);
                        } catch (Exception $e) {
                            array_push($err, $worksheet);
                            // return $worksheet;
                        }
                        //insert into invoice table

                    }
                }
            }
        } else {
            return $_FILES['file']['name'];
        }
        if (collect($err)->count() > 0) {
            return $err;
        }
        // return $aaa;
        //    return collect($arr)->sum('totalpaid');


        // Invoice::insert($arr);

        return redirect()->back()->with(['color' => 'success', 'message' => 'Excel file imported successfully!']);
    }

    public function import_invoice_byzone(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'subzone' => 'required'
        ]);

        //Allowed mime types
        $excelMimes = array('text/xls', 'text/xlsx', 'application/excel', 'application/vnd.msexcel', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Validate whether selected file is a Excel file
        if (!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $excelMimes)) {
            if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                $reader = new ReaderXlsx();
                $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
                // $worksheet =  $spreadsheet->getActiveSheet();
                $err = [];
                for ($i = 0; $i < 1; $i++) {
                    $worksheet =   $spreadsheet->getSheet($i);
                    $worksheet_arr =  $worksheet->toArray();
                    // Remove header row
                    for ($j = 0; $j <= 2; $j++) {
                        unset($worksheet_arr[$j]);
                    }

                    // Get the uploaded file

                    $meternumberCount = SequenceNumber::where('id', 1)->get('tabmeter')[0];
                    $i = $meternumberCount->tabmeter;
                    foreach ($worksheet_arr as $worksheet) {
                        try {

                            // list($code , $meter_id) = explode("KP10",$worksheet[15]);
                            $meterCode = substr("0000", strlen($i)) . $i;
                            
                            $usermeter = UserMerterInfo::create([
                                'submeter_name' => $worksheet[7],
                                "meter_address" => $worksheet[14],
                                "user_id" => $worksheet[2],
                                "meternumber" => "KP10" . $meterCode,
                                "metertype_id" => 1,
                                "undertake_zone_id" => $request->get('subzone'),
                                "undertake_subzone_id" => $request->get('subzone'),
                                "acceptance_date" => date('Y-m-d'),
                                "status" =>  'active',
                                "payment_id" => 1,
                                "discounttype" => 1,
                                "recorder_id" => 1848,
                                'cutmeter' => 0,
                                'inv_no_index' => 0,
                                'factory_no' => $worksheet[0]
                            ]);


                           //list($code,$meter_id_fk_old) = explode('KP10',$worksheet[0]);
                            $invoice_old = InvoiceOld::where('meter_id_fk', $worksheet[4])
                                ->whereIn('status', ['owe','invioce'])
                                ->where('inv_period_id_fk', '<>',7)->get();
                            $inv_no_index_temp = 0;
                            if (collect($invoice_old)->isNotEmpty()) {
                                foreach ($invoice_old as $inv) {
                                    $inv_no_index = substr($inv->inv_no, 0, 4);

                                    Invoice::create([
                                        'inv_period_id_fk' => $inv->inv_period_id_fk,
                                        'meter_id_fk' => $usermeter->id,
                                        'user_id' => $inv->user_id,
                                        'lastmeter' => $inv->lastmeter,
                                        'currentmeter' => $inv->currentmeter,
                                        'water_used' => $inv->water_used,
                                        'inv_type' =>  $inv->inv_type,
                                        'inv_no' =>     $inv_no_index . $meterCode,
                                        'paid' => $inv->paid,
                                        'vat' => 0,
                                        'reserve_meter' => 10,
                                        'totalpaid' => $inv->totalpaid,
                                        'acc_trans_id_fk' => 0,
                                        'status' => 'owe',
                                        'comment' => $inv->meter_id_fk,
                                        'recorder_id' => $inv->recorder_id,
                                        'created_at' =>$inv->created_at,
                                        'updated_at' => $inv->updated_at
                                    ]);
                                    $inv_no_index_temp =substr($inv_no_index, -1);
                                   
                                   
                                }
                            }



                            $inv_no_index_next = $inv_no_index_temp + 1;
                            Invoice::create([
                                'inv_period_id_fk' => 7,
                                'meter_id_fk' => $usermeter->id,
                                'user_id' => $usermeter->user_id,
                                'lastmeter' => $worksheet[15],
                                'currentmeter' => $worksheet[16],
                                'water_used' => $worksheet[17],
                                'inv_type' =>  $worksheet[17] == 0 ? 'r' : 'u',
                                'inv_no' => '010'.$inv_no_index_next . $meterCode,
                                'paid' => $worksheet[18],
                                'vat' => 0,
                                'reserve_meter' => 10,
                                'totalpaid' => $worksheet[19],
                                'acc_trans_id_fk' => 0,
                                'status' => 'invoice',
                                'recorder_id' => 1849,
                                'created_at' => date_create('2025-04-01 00:00:00'),
                                'updated_at' => date_create('2025-04-21 00:00:00')
                            ]);

                             UserMerterInfo::where('meter_id', $usermeter->meter_id)->update([
                                        'inv_no_index' =>  $inv_no_index_next
                                    ]);
                            $i++;
                           

                        } catch (Exception $e) {
                            array_push($err, $worksheet);
                            // return $worksheet;
                        }
                        //insert into invoice table
                        SequenceNumber::where('id', 1)->update(['tabmeter' => $i]);

                    }

                }
            }
            return $err;
        } else {
            return 'a';
        }
    }


    public function import_invoice_old(Request $request)
    {
        try {
            $invoice_old = InvoiceOld::where('meter_id_fk', $request->get('old_meter_id'))
                ->whereIn('status', ['owe'])->get();
            if (collect($invoice_old)->isNotEmpty()) {
                foreach ($invoice_old as $inv) {
                    $inv_no_index = substr($inv->inv_no, 0, 4);

                    Invoice::create([
                        'inv_period_id_fk' => $inv->inv_period_id_fk,
                        'meter_id_fk' => $request->get('new_meter_id'),
                        'user_id' => $inv->user_id,
                        'lastmeter' => $inv->lastmeter,
                        'currentmeter' => $inv->currentmeter,
                        'water_used' => $inv->water_used,
                        'inv_type' =>  $inv->inv_type,
                        'inv_no' =>     $inv_no_index . $request->get('$meterCode'),
                        'paid' => $inv->paid,
                        'vat' => 0,
                        'reserve_meter' => 10,
                        'totalpaid' => $inv->totalpaid,
                        'acc_trans_id_fk' => 0,
                        'status' => $inv->status,
                        'recorder_id' => 1849,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    UserMerterInfo::where('meter_id', $request->get('new_meter_id'))->update([
                        'inv_no_index' => substr($inv_no_index, -1)
                    ]);
                }
            }
        } catch (Exception $e) {
            array_push($err, $request->get('old_meter_id'));
            // return $worksheet;
        }
    }
}
