<?php

namespace App\Http\Controllers\Tabwater;


use App\Http\Controllers\Controller;
use App\Models\Tabwater\InvoicePeriod;
use App\Models\Tabwater\UserMerterInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
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
        return view('tabwater.staff.mobile.index', compact('staff'));
    }

    public function meter_reading($meter_id){
        $currentInvPeriod = InvoicePeriod::where('status', 'active')->get('id')->first();

         $meter = UserMerterInfo::where('meter_id', $meter_id)
        ->get(['meter_id', 'last_meter_recording']);
        return view('tabwater.staff.mobile.meter_reading', compact('meter'));

    }

    public function members($subzone_id){
        $members = UserMerterInfo::where('undertake_subzone_id',$subzone_id)
        ->with([
            'user' => function($q){
                return $q->select('id', 'prefix', 'firstname', 'lastname', 'address', 'subzone_id', 'zone_id');
            },
        ])
        ->where('status', 'active')
        ->get([
            'meter_id', 'user_id', 'meternumber', 'meter_address', 'undertake_zone_id', 'undertake_subzone_id'
        ]);
        return view('tabwater.staff.mobile.members', compact('members'));

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

            // Here, we use Symfony's Process component to run Tesseract OCR
            // This is the correct way to do it in Laravel.
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
     * Runs Tesseract OCR on the server using Symfony Process component.
     * * @param string $imagePath
     * @return string
     */
    private function runTesseractOCR($imagePath)
    {
        // Path to the Tesseract executable (adjust if necessary)
        $tesseractPath = '/usr/bin/tesseract'; 
        
        // Command to run Tesseract
        // -l eng: specifies English language pack
        // --psm 7: Page segmentation mode for a single text line
        $command = [$tesseractPath, $imagePath, 'stdout', '-l', 'eng', '--psm', '7', '--oem', '3'];
        
        $process = new Process($command);
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        
        // Return the clean output
        return trim($process->getOutput());
    }
}
