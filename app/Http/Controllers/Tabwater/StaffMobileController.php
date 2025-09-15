<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FunctionsController;
use App\Models\Admin\Organization;
use App\Models\Tabwater\Invoice;
use App\Models\Tabwater\InvoicePeriod;
use App\Models\Tabwater\UndertakerSubzone;
use App\Models\Tabwater\UserMerterInfo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR; // Add this line
use Symfony\Component\Process\Process; // ยังคงเก็บไว้เผื่อใช้ส่วนอื่น
use Symfony\Component\Process\Exception\ProcessFailedException;

class StaffMobileController extends Controller
{
    public function index()
    {
        $staff = User::where('id', Auth::id())
            ->with([
                'undertaker_subzone',
                'undertaker_subzone.subzone' => function ($q) {
                    return $q->select('id', 'subzone_name', 'zone_id');
                },
                'undertaker_subzone.subzone.zone' => function ($q) {
                    return $q->select('id', 'zone_name');
                }
                
            ])
            ->get(['id', 'username', 'prefix', 'firstname', 'lastname', 'subzone_id', 'zone_id'])->first();
        $inv_period_id = 7;
        foreach ($staff->undertaker_subzone as $undertaker_subzone) {
            $members = $undertaker_subzone->subzoneMemberCount(1);
            $init = $undertaker_subzone->subzoneInvoiceStatusCount('init', 1, $inv_period_id);
            
            $paid = $undertaker_subzone->subzoneInvoiceStatusCount('paid', 1, $inv_period_id);
            
            $invoice = $undertaker_subzone->subzoneInvoiceStatusCount('invoice', 1, $inv_period_id);
            
            $undertaker_subzone->members = $members;
            $undertaker_subzone->invoice_status = $invoice;
            $undertaker_subzone->init_status = $init;
            $undertaker_subzone->paid_status = $paid;
        }
        return view('tabwater.staff.mobile.index', compact('staff'));
    }

    public function meter_reading($meter_id)
    {
        $currentInvPeriod = InvoicePeriod::where('status', 'active')->get('id')->first();

        $meter = UserMerterInfo::where('meter_id', $meter_id)
            ->get(['meter_id', 'last_meter_recording']);
        return view('tabwater.staff.mobile.meter_reading', compact('meter'));
    }

    public function members($subzone_id, $status)
    {
        $members = UserMerterInfo::where('undertake_subzone_id', $subzone_id)
            ->with([
                'user' => function ($q) {
                    return $q->select('id', 'prefix', 'firstname', 'lastname', 'address', 'subzone_id', 'zone_id');
                },
            ])
            ->whereHas(
                'invoice' , function ($q) use($status){
                    return $q->select('inv_id')->where('status', $status);
                },
            )
            ->where('status', 'active')
            ->get([
                'meter_id',
                'user_id',
                'meternumber',
                'meter_address',
                'undertake_zone_id',
                'undertake_subzone_id'
            ]);
        return view('tabwater.staff.mobile.members', compact('members'));
    }

    public function store(Request $request){

        $userMeterInfo = UserMerterInfo::where('meter_id', $request->get('meter_id'))->update([
            'last_meter_recording' => $request->get('currentmeter'),
        ]);
        
        
        if($userMeterInfo){
            $invInfos = Invoice::where([
                'meter_id_fk' => $request->get('meter_id'),
                'inv_period_id_fk' => 7
                ])->get()->first();
            
            $water_used =  $request->get('currentmeter') - $invInfos->lastmeter;
            $paid = $water_used * 6;
             Invoice::where([
                'meter_id_fk' => $request->get('meter_id'),
                'inv_period_id_fk' => 7
                ])->update([
                'currentmeter' => $request->get('currentmeter'),
                'water_used' => $water_used,
                'paid' => $paid,
                'totalpaid' => $paid + $invInfos->vat + $invInfos->reserve_meter,
                'status' => 'invoice',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }


        
        $umf = UserMerterInfo::where('meter_id', $request->get('meter_id'))->get()->first();

                    $inv_owes = Invoice::where('meter_id_fk', $request->get('meter_id'))
                        ->with('invoice_period')
                        ->where('status', 'owe')->get();
            $owe_infos = [];
            foreach($inv_owes as $owe){
                $a = explode('-',$owe->invoice_period->inv_p_name);
                $thaiMonthStr = FunctionsController::fullThaiMonth($a[0]);
                array_push($owe_infos,[
                    'inv_id' => $owe->inv_id,
                    'inv_period' => $thaiMonthStr." ".$a[1],
                    'totalpaid' => $owe->totalpaid
                ]);
            } 

         $currentPeriod = InvoicePeriod::where('status', 'active')->get()->first();
            $a = explode('-',$currentPeriod->inv_p_name);
            $thaiMonthStr = FunctionsController::fullThaiMonth($a[0]);
            if(!isset($umf->invoice[0]->created_at)){
                return $umf;
            }
            $inv_created_at = explode(' ',$umf->invoice[0]->created_at);
            $date =  Carbon::parse($inv_created_at[0]);
            $expired_date = $date->addDays(15)->format('Y-m-d');

            $thai_created_date = (new FunctionsController())->engDateToThaiDateFormat($inv_created_at[0]);
            $thai_expired_date = (new FunctionsController())->engDateToThaiDateFormat($expired_date);
        $org = Organization::getOrgInfos(2);
        $data = [
                'org_name' => $org['org_type_name'].$org['org_name'],
                'org_address' =>$org['org_address']." ". $org['org_zone']." ต.".$org['org_tambons']
                    ." อ.".$org['org_districts']." จ.".$org['org_provinces']." ".$org['org_zipcode'],
                'org_logo' => $org['org_logo_img'],
                'org_dept_name' => $org['org_dept_name'],
                'org_dept_phone' => $org['org_dept_phone'],
                'meter_id' => $request->get('meter_id'),
                'inv_id' =>$umf->invoice[0]->inv_id,
                'meternumber' => $umf->meternumber,
                'factory_no' => $umf->factory_no,
                'submeter_name' => $umf->submeter_name,
                'user_id' => $umf->user_id,
                'name' =>  $umf->submeter_name =="" ? $umf->user->prefix.$umf->user->firstname." ".$umf->user->lastname : $umf->user->prefix.$umf->user->firstname." ".$umf->user->lastname." ( ".$umf->submeter_name." )",
                'user_address' => $umf->user->address." ".$umf->user->user_zone->zone_name
                        . " ".$umf->user->user_tambon->tambon_name . " ".$umf->user->user_district->district_name
                        . " ".$umf->user->user_province->province_name,
                'lastmeter' => $umf->invoice[0]->lastmeter,
                'currentmeter' => $umf->invoice[0]->currentmeter,
                'water_used' =>  $umf->invoice[0]->water_used,
                'paid' =>  $umf->invoice[0]->paid,
                'vat' =>  $umf->invoice[0]->vat,
                'reserve_meter' =>  $umf->invoice[0]->reserve_meter,
                'totalpaid' =>  $umf->invoice[0]->totalpaid,
                'period' => $thaiMonthStr." ".$a[1],
                'created_at' => $thai_created_date,
                'expired_date' => $thai_expired_date,
                'owe_infos' => $owe_infos,
                'netpaid' => number_format($umf->invoice[0]->totalpaid + collect($owe_infos)->sum('totalpaid'),2)
        ];
        
        return view('tabwater.staff.mobile.print_bill', compact('data'));
    }

    public function membersJson($subzone_id)
    {
        $members = UserMerterInfo::where('undertake_subzone_id', $subzone_id)
            ->with([
                'user' => function ($q) {
                    return $q->select('id', 'prefix', 'firstname', 'lastname', 'address', 'subzone_id', 'zone_id');
                },
                 'invoice' => function ($q) {
                    return $q->select('inv_id', 'meter_id_fk', 'status');
                },
            ])
            ->whereHas(
                'invoice' , function ($q) {
                    return $q->select('inv_id')->where('status', 'init');
                },
            )
            ->where('status', 'active')
            ->get([
                'meter_id',
                'user_id',
                'meternumber',
                'meter_address',
                'undertake_zone_id',
                'undertake_subzone_id'
            ]);
        return response()->json($members);
    }

    public function process_meter_image(Request $request)
    {

        // Validate the uploaded file
        $request->validate([
            'meter_image' => 'required|image|mimes:jpeg,png,jpg|max:5000', // Max 5MB
        ]);

        try {
            // Get the file from the request
            $file = $request->file('meter_image');

            // Save the file to storage (e.g., 'public/uploads')
            // This is crucial for saving the image as proof
            $path = $file->store('public/meter_readings');
            $fullPath = Storage::path($path);

            // Here, we use the tesseract_ocr library to run Tesseract
            $extractedNumber = $this->runTesseractOCR($fullPath);

            // You can delete the file after processing if you don't need it
            // Storage::delete($path);

            // Return the extracted number as a JSON response
            return response()->json([
                'success' => true,
                'reading' => $extractedNumber,
                'message' => 'Image processed successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการประมวลผลรูปภาพ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Runs Tesseract OCR on the server using the tesseract_ocr library.
     * @param string $imagePath
     * @return string
     */
    private function runTesseractOCR($imagePath)
    {
        try {
            $extractedText = (new TesseractOCR($imagePath))
                ->lang('eng') // ระบุภาษาที่ต้องการอ่าน
                ->psm(7)    // Page segmentation mode for a single text line
                ->run();

            // Filter out non-numeric characters from the result
            $cleanedNumber = preg_replace('/[^0-9.]/', '', $extractedText);

            return $cleanedNumber;
        } catch (\Exception $e) {
            // In case of an error, return a clear error message or an empty string
            throw new \Exception('Tesseract OCR failed: ' . $e->getMessage());
        }
    }
}
