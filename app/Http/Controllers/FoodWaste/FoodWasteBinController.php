<?php

namespace App\Http\Controllers\FoodWaste;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FunctionsController;
use App\Models\FoodWaste\FoodWasteBin;
use App\Models\FoodWaste\FoodwasteBinStocks;
use App\Models\FoodWaste\FoodwastIotbox;
use App\Models\Keptkaya\KpUserGroup;
use App\Models\Keptkaya\KpUsergroupPayratePerMonth;
use App\Models\KeptKaya\WasteBinPayratePerMonth;
use App\Models\User;
use App\Models\KeptKaya\WasteBinSubscription; // Import WasteBinSubscription model
use App\Models\KeptKaya\WasteBin;
use App\Services\UserWasteStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FoodWasteBinController extends Controller
{
    protected $wasteStatusService;

    public function __construct(UserWasteStatusService $wasteStatusService)
    {
        $this->wasteStatusService = $wasteStatusService;
    }

    /**
     * Display a listing of the waste bins for a specific user.
     *
     * @param  \App\Models\User  $w_user
     * @return \Illuminate\Http\Response
     */
    public function index(User $w_user)
    {
        $wasteBins = $w_user->foodwasteBins()->paginate(10);
        return view('foodwaste.w.waste_bins.index', compact('w_user', 'wasteBins'));
    }

    /**
     * Show the form for creating a new waste bin.
     *
     * @param  \App\Models\User  $w_user
     * @return \Illuminate\Http\Response
     */
    public function create(User $w_user)
    {
        $user_groups = KpUserGroup::all();
        $func = new FunctionsController();
        $bins_pending = FoodwasteBinStocks::where('status', 'pending')->get();
        $iotboxes = FoodwastIotbox::where('status', 'pending')->get();
        return view('foodwaste.w.waste_bins.create', compact('w_user', 'user_groups', 'bins_pending', 'iotboxes'));
    }

    public function store(Request $request, User $w_user)
    {

        $request->validate([
            'bin_code' => 'required',
            'bin_type' => 'required|string|max:255',
            'user_group' => 'required',
            'iotboxes_id' => 'required',
            'location_description' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => ['required', Rule::in(['active', 'inactive', 'damaged', 'removed'])],
        ]);

        $w_user->foodwasteBins()->create([
            'bin_code_fk' => $request->bin_code,
            'user_id' => $w_user->id,
            'bin_type' => $request->bin_type,
            'iotbox_id_fk' => $request->iotboxes_id != 0 ? $request->iotboxes_id : 0,
            'location_description' => $request->location_description,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $request->status,
        ]);

        FoodwasteBinStocks::where('id', $request->bin_code)->update([
            'status' => 'active',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($request->iotboxes_id != 0) {
            $iotbox = FoodwastIotbox::find($request->iotboxes_id);
            $iotbox->status = 'active';
            $iotbox->updated_at = date('Y-m-d H:i:s');
            $iotbox->save();
        }

        return redirect()->route('foodwaste.waste_bins.index', $w_user->id)
            ->with('success', 'เพิ่มถังขยะเรียบร้อยแล้ว!');
    }


    public function show(WasteBin $wasteBin)
    {
        return view('foodwaste.waste_bins.show', compact('wasteBin'));
    }

    public function edit(FoodWasteBin $wasteBin)
    {
        return view('foodwaste.waste_bins.edit', compact('wasteBin'));
    }


    public function update(Request $request, FoodWasteBin $wasteBin)
    {
        $request->validate([
            'bin_code' => ['nullable', 'string', 'max:255', Rule::unique('waste_bins')->ignore($wasteBin->id)],
            'bin_type' => 'required|string|max:255',
            'location_description' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'status' => ['required', Rule::in(['active', 'inactive', 'damaged', 'removed'])],
            'is_active_for_annual_collection' => 'boolean',
        ]);

        // DB::transaction(function () use ($request, $wasteBin) {
        $oldIsActiveForAnnualCollection = $wasteBin->is_active_for_annual_collection;
        $newIsActiveForAnnualCollection = $request->has('is_active_for_annual_collection');

        $data = $request->all();
        $data['is_active_for_annual_collection'] = $newIsActiveForAnnualCollection;

        $wasteBin->update($data); // Update waste bin data

        // If status changed to active for annual collection, create/ensure subscription
        if (!$oldIsActiveForAnnualCollection && $newIsActiveForAnnualCollection) {
            $fiscalYear = WasteBinSubscription::calculateFiscalYear();
            $annualFee = 1200.00; // Default annual fee
            $monthlyFee = $annualFee / 12;

            WasteBinSubscription::firstOrCreate(
                [
                    'waste_bin_id' => $wasteBin->id,
                    'fiscal_year' => $fiscalYear,
                ],
                [
                    'annual_fee' => $annualFee,
                    'monthly_fee' => $monthlyFee,
                    'total_paid_amount' => 0,
                    'status' => 'pending',
                ]
            );
        }
        // If status changed from active to inactive, you might want to update the subscription status to cancelled/inactive
        // Or handle this logic in a separate process. For now, we only create on activation.

        // Call service to update overall user waste status (waste_preference)
        $this->wasteStatusService->updateWasteBinAndUserStatus($foodwasteBin, $data);
        // });

        return redirect()->route('foodwaste.waste_bins.index', $foodwasteBin->user->id)
            ->with('success', 'อัปเดตถังขยะเรียบร้อยแล้ว!');
    }


    public function destroy(FoodWasteBin $wasteBin)
    {
        $w_user = $wasteBin->user; // Get user before deleting bin

        DB::transaction(function () use ($wasteBin, $w_user) {
            $wasteBin->delete();
            // Call service to update overall user waste status (waste_preference)
            $this->wasteStatusService->updateOverallUserWasteStatus($w_user);
        });

        return redirect()->route('foodwaste.waste_bins.index', $w_user->id)
            ->with('success', 'ลบถังขยะเรียบร้อยแล้ว!');
    }

    public function viewmap()
    {
        return view('foodwaste.dashboard_map');
    }

    public function map()
    {
        $bins = FoodWasteBin::with([
            'user' => function ($q) {
                return $q->select('id', 'firstname', 'lastname', 'address', 'zone_id', 'subzone_id');
            },
            'user.user_zone' => function ($q) {
                return $q->select('id', 'zone_name');
            },
            'user.user_subzone' => function ($q) {
                return $q->select('id', 'subzone_name');
            },
        ])->get(['id', 'user_id',  'bin_code', 'latitude', 'longitude', 'status', 'bin_type']);
        return response()->json($bins);
    }
}
