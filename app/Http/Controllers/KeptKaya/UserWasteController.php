<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Keptkaya\KpUserGroup;
use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\User;
use App\Services\UserWasteStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserWasteController extends Controller
{
    protected $wasteStatusService;

    public function __construct(UserWasteStatusService $wasteStatusService)
    {
        $this->wasteStatusService = $wasteStatusService;
    }

  
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // Get the 'per_page' value from the request, default to 10
        $perPage = $request->input('per_page', 10);

        // Define allowed per page options
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage) && $perPage !== 'all') {
            $perPage = 10; // Fallback to default if invalid value is provided
        }

        // Get search parameters from the request
        $searchName = $request->input('search_name');
        $searchEmail = $request->input('search_email');
        $searchStatus = $request->input('search_status');
        $searchIsAnnualCollection = $request->input('search_is_annual_collection'); // 'true', 'false', 'any'
        $searchIsWasteBank = $request->input('search_is_waste_bank'); // 'true', 'false', 'any'


        $query = User::with(['wastePreference', 'wasteBins'])
                ->where('org_id_fk', Auth::user()->org_id_fk)
               ->whereHas('wastePreference')
                ->role('User');

        // Apply search filters
        $query->when($searchName, function ($q, $name) {
            $q->where(function ($subQ) use ($name) {
                $subQ->where('firstname', 'like', '%' . $name . '%')
                    ->orWhere('lastname', 'like', '%' . $name . '%')
                    ->orWhere('username', 'like', '%' . $name . '%');
            });
        });

        $query->when($searchEmail, function ($q, $email) {
            $q->where('email', 'like', '%' . $email . '%');
        });

        $query->when($searchStatus && $searchStatus !== 'any', function ($q, $status) {
            $q->where('status', $status);
        });

        // Filter by is_annual_collection status
        $query->when($searchIsAnnualCollection && $searchIsAnnualCollection !== 'any', function ($q) use ($searchIsAnnualCollection) {
            if ($searchIsAnnualCollection === 'true') {
                $q->whereHas('wastePreference', function ($wp) {
                    $wp->where('is_annual_collection', true);
                });
            } elseif ($searchIsAnnualCollection === 'false') {
                // Users who have preference and is_annual_collection is false
                // OR users who do NOT have a wastePreference record (meaning it's implicitly false)
                $q->where(function ($subQ) {
                    $subQ->whereHas('wastePreference', function ($wp) {
                        $wp->where('is_annual_collection', false);
                    })->orWhereDoesntHave('wastePreference');
                });
            }
        });

        // Filter by is_waste_bank status
        $query->when($searchIsWasteBank && $searchIsWasteBank !== 'any', function ($q) use ($searchIsWasteBank) {
            if ($searchIsWasteBank === 'true') {
                $q->whereHas('wastePreference', function ($wp) {
                    $wp->where('is_waste_bank', true);
                });
            } elseif ($searchIsWasteBank === 'false') {
                // Users who have preference and is_waste_bank is false
                // OR users who do NOT have a wastePreference record (implicitly false)
                $q->where(function ($subQ) {
                    $subQ->whereHas('wastePreference', function ($wp) {
                        $wp->where('is_waste_bank', false);
                    })->orWhereDoesntHave('wastePreference');
                });
            }
        });

        $query = $query->whereHas('wastePreference');
        // Check if it's an AJAX request for live search
        if ($request->ajax()) {
            // For AJAX, just get the filtered data (no pagination for simplicity in AJAX update)
            $users = $query->get();
            return view('keptkayas.w.users._table_body', compact('users'))->render();
        } else {
            // For full page load, apply pagination
            if ($perPage === 'all') {
                $users = $query->get();
            } else {
                $users = $query->paginate($perPage)->appends($request->query()); // Append search queries to pagination links
            }


            // Pass all search parameters back to the view to pre-fill search fields
            return view('keptkayas.w.users.index', compact('users', 'perPage', 'searchName', 'searchEmail', 'searchStatus', 'searchIsAnnualCollection', 'searchIsWasteBank'));
        }
    }



    public function waste_bin_users(Request $request)
    {
        // return $request;
        // Get the 'per_page' value from the request, default to 10
        $perPage = $request->input('per_page', 10);

        // Define allowed per page options
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage) && $perPage !== 'all') {
            $perPage = 10; // Fallback to default if invalid value is provided
        }

        // Get search parameters from the request
        $searchName = $request->input('search_name');
        $searchEmail = $request->input('search_email');
        $searchStatus = $request->input('search_status');
        $searchIsAnnualCollection = $request->input('search_is_annual_collection'); // 'true', 'false', 'any'
        $searchIsWasteBank = $request->input('search_is_waste_bank'); // 'true', 'false', 'any'


        $query = User::with(['wastePreference', 'wasteBins']);

        // Apply search filters
        $query->when($searchName, function ($q, $name) {
            $q->where(function ($subQ) use ($name) {
                $subQ->where('firstname', 'like', '%' . $name . '%')
                    ->orWhere('lastname', 'like', '%' . $name . '%')
                    ->orWhere('username', 'like', '%' . $name . '%');
            });
        });

        $query->when($searchEmail, function ($q, $email) {
            $q->where('email', 'like', '%' . $email . '%');
        });

        $query->when($searchStatus && $searchStatus !== 'any', function ($q, $status) {
            $q->where('status', $status);
        });

        // Filter by is_annual_collection status
        $query->when($searchIsAnnualCollection && $searchIsAnnualCollection !== 'any', function ($q) use ($searchIsAnnualCollection) {
            if ($searchIsAnnualCollection === 'true') {
                $q->whereHas('wastePreference', function ($wp) {
                    $wp->where('is_annual_collection', true);
                });
            } elseif ($searchIsAnnualCollection === 'false') {
                // Users who have preference and is_annual_collection is false
                // OR users who do NOT have a wastePreference record (meaning it's implicitly false)
                $q->where(function ($subQ) {
                    $subQ->whereHas('wastePreference', function ($wp) {
                        $wp->where('is_annual_collection', false);
                    })->orWhereDoesntHave('wastePreference');
                });
            }
        });

        // Filter by is_waste_bank status
        $query->when($searchIsWasteBank && $searchIsWasteBank !== 'any', function ($q) use ($searchIsWasteBank) {
            if ($searchIsWasteBank === 'true') {
                $q->whereHas('wastePreference', function ($wp) {
                    $wp->where('is_waste_bank', true);
                });
            } elseif ($searchIsWasteBank === 'false') {
                // Users who have preference and is_waste_bank is false
                // OR users who do NOT have a wastePreference record (implicitly false)
                $q->where(function ($subQ) {
                    $subQ->whereHas('wastePreference', function ($wp) {
                        $wp->where('is_waste_bank', false);
                    })->orWhereDoesntHave('wastePreference');
                });
            }
        });

        // Check if it's an AJAX request for live search
        if ($request->ajax()) {
            // For AJAX, just get the filtered data (no pagination for simplicity in AJAX update)
            $users = $query->get();
            return view('keptkayas.w.users._table_body', compact('users'))->render();
        } else {
            // For full page load, apply pagination
            if ($perPage === 'all') {
                $users = $query->get();
            } else {
                $users = $query->paginate($perPage)->appends($request->query()); // Append search queries to pagination links
            }


            // Pass all search parameters back to the view to pre-fill search fields
            return view('keptkayas.w.users.waste_bin_users', compact('users', 'perPage', 'searchName', 'searchEmail', 'searchStatus', 'searchIsAnnualCollection', 'searchIsWasteBank'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user_groups = KpUserGroup::all();
        $nonMemberUsers = User::role('User')
            ->where('org_id_fk', Auth::user()->org_id_fk)
            // ตรวจสอบว่าไม่เป็นสมาชิกธนาคารขยะ (สมมติตาราง/ฟิลด์)
            ->whereDoesntHave('wastePreference')
            ->get();
        return view('keptkayas.w.users.create', compact('nonMemberUsers', 'user_groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->mode == 'batch_select') {
            $validatedData = $request->validate([
                'selected_user_ids.*' => 'required|',
            ]);
        } else {
            $validatedData = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
        }


        if ($request->mode == 'batch_select') {
            foreach ($validatedData['selected_user_ids'] as $selected_user_id) {
                KpUserWastePreference::create([
                    'user_id'               => $selected_user_id,
                    'is_annual_collection'  => false,
                    'is_waste_bank'         => false,
                    'created_at'            => Now(),
                    'updated_at'            => Now(),
                ]);
            }
        } else {
            DB::transaction(function () use ($validatedData) {

                $user = User::create([
                    'firstname'     => $validatedData['firstname'],
                    'lastname'      => $validatedData['lastname'],
                    'email'         => $validatedData['email'],
                    'password'      => Hash::make($validatedData['password']),
                    'org_id_fk'     => Auth::user()->org_id_fk,
                    'tambon_code'   => Auth::user()->tambon_code,
                    'district_code' => Auth::user()->district_code,
                    'province_code' => Auth::user()->province_code,
                ]);

                $user->assignRole('User');

                // สร้าง UserWastePreference เริ่มต้นสำหรับผู้ใช้ใหม่
                $user->wastePreference()->create([
                    'is_annual_collection' => false,
                    'is_waste_bank' => false,
                ]);
            });
        }




        return redirect()->route('keptkayas.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    // public function show(Request $request)
    // {
    //     return $request;
    //     // $w_users->load('wasteBins', 'wastePreference'); // โหลดความสัมพันธ์
    //     // return view('users.show', compact('user'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {

        return view('keptkayas.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            // 'password' และ 'password_confirmation' ไม่จำเป็นต้อง validate ถ้าไม่มีการเปลี่ยนแปลง
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($validatedData, $user) {
            $user->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => isset($validatedData['password']) ? bcrypt($validatedData['password']) : $user->password,
            ]);
        });

        return redirect()->route('keptkayas.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            // $user->delete(); // จะลบ wastePreference และ wasteBins ด้วย cascade ถ้าตั้งค่าไว้ใน migration
        });

        return redirect()->route('keptkayas.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Update waste service preferences for a user.
     */
    public function updateWasteServicePreferences(Request $request)
    {
        $wasteData = $request->input('waste', []);

    DB::transaction(function () use ($wasteData) {
        foreach ($wasteData as $userId => $preferences) {
            
            // 1. เตรียมข้อมูล boolean (แปลงค่า 1/0 เป็น true/false)
            $isAnnual = isset($preferences['is_annual_collection']) && $preferences['is_annual_collection'] == '1';
            $isWasteBank = isset($preferences['is_waste_bank']) && $preferences['is_waste_bank'] == '1';

            // 2. ดึง User มาตรวจสอบความถูกต้อง (Optional: ป้องกัน Data Integrity)
            $user = User::with('wasteBins')->find($userId);
            if (!$user) continue;

            // 🔴 Security Check: ถ้ามีถังขยะอยู่ ห้ามปิด Annual Collection
            // (เป็นการ Re-validate ฝั่ง Server เผื่อคนแอบแก้ HTML)
            if ($user->wasteBins->count() > 0) {
                $isAnnual = true; // บังคับเปิดเสมอถ้ามีถังขยะ
            }

            // 3. บันทึกหรืออัปเดตข้อมูลลง Model KpUserWastePreference
            // ใช้ updateOrCreate เพื่อ: ถ้ามีแล้ว->อัปเดต, ถ้าไม่มี->สร้างใหม่
            KpUserWastePreference::updateOrCreate(
                ['user_id' => $userId], // เงื่อนไขการค้นหา
                [
                    'is_annual_collection' => $isAnnual,
                    'is_waste_bank' => $isWasteBank,
                ]
            );
        }
    });

    return redirect()->back()->with('success', 'บันทึกข้อมูลบริการเรียบร้อยแล้ว');
}

    /**
     * แสดงหน้าสำหรับเลือกผู้ใช้ที่ยังไม่ได้เป็นสมาชิกบริการขยะ
     */
    public function showEligibleForWasteServices()
    {
        // ดึงผู้ใช้ที่ยังไม่มี record ใน user_waste_preferences
        // หรือมี record แล้วแต่ทั้ง is_annual_collection และ is_waste_bank เป็น false
        $eligibleUsers = User::leftJoin('user_waste_preferences', 'users.id', '=', 'user_waste_preferences.user_id')
            ->select(
                'users.*',
                'user_waste_preferences.is_annual_collection',
                'user_waste_preferences.is_waste_bank'
            )
            ->whereNull('user_waste_preferences.user_id') // ผู้ใช้ที่ยังไม่มี preference record
            ->orWhere(function ($query) { // หรือมีแล้วแต่ทั้งสองเป็น false
                $query->where('user_waste_preferences.is_annual_collection', false)
                    ->where('user_waste_preferences.is_waste_bank', false);
            })
            ->get();

        return view('users.enroll_waste_services', compact('eligibleUsers'));
    }

    public function aa(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*.is_annual_collection' => 'boolean',
            'users.*.is_waste_bank' => 'boolean',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->input('users') as $userId => $userData) {
                $user = User::find($userId);
                if ($user) {
                    $preference = $user->wastePreference()->firstOrCreate(['user_id' => $user->id]);

                    $oldIsAnnualCollection = $preference->is_annual_collection;
                    $oldIsWasteBank = $preference->is_waste_bank;

                    $newIsAnnualCollection = $userData['is_annual_collection'];
                    $newIsWasteBank = $userData['is_waste_bank'];

                    // Apply the same logic for forcing waste bank if annual collection is turned off
                    if (!$newIsAnnualCollection && $oldIsAnnualCollection && !$oldIsWasteBank) {
                        $newIsWasteBank = true;
                        Log::info("User {$user->id}: Annual Collection changed from TRUE to FALSE via batch update. Forcing Waste Bank to TRUE.");
                    }

                    $preference->update([
                        'is_annual_collection' => $newIsAnnualCollection,
                        'is_waste_bank' => $newIsWasteBank,
                    ]);

                    // You might want to call the UserWasteStatusService here if it has more complex logic
                    // $this->wasteStatusService->updateOverallUserWasteStatus($user);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'อัปเดตประเภทบริการของผู้ใช้งานเรียบร้อยแล้ว!']);
    }

    /**
     * ประมวลผลการบันทึกผู้ใช้ที่เลือกเป็นสมาชิกบริการขยะ
     */
    public function enrollInWasteServices(Request $request)
    {
        $request->validate([
            'selected_users' => 'required|array',
            'selected_users.*' => 'exists:users,id', // ตรวจสอบว่า ID ผู้ใช้มีอยู่จริง
            'service_type' => 'required|in:annual_collection,waste_bank', // ประเภทบริการที่เลือก
        ]);

        $selectedUserIds = $request->input('selected_users');
        $serviceType = $request->input('service_type');

        DB::transaction(function () use ($selectedUserIds, $serviceType) {
            foreach ($selectedUserIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $preference = $user->wastePreference()->firstOrCreate(['user_id' => $user->id]);

                    if ($serviceType === 'annual_collection') {
                        $preference->is_annual_collection = true;
                    } elseif ($serviceType === 'waste_bank') {
                        $preference->is_waste_bank = true;
                    }
                    $preference->save();

                    // เรียก Service เพื่อรัน Logic การอัปเดตสถานะโดยรวม (เผื่อมี Logic บังคับอื่นๆ)
                    $this->wasteStatusService->updateOverallUserWasteStatus($user);
                }
            }
        });

        return redirect()->route('users.enroll.show')->with('success', 'ผู้ใช้ที่เลือกถูกเพิ่มเข้าสู่บริการเรียบร้อยแล้ว!');
    }

    public function search($query)
    {

        if (!$query) {
            return response()->json([]);
        }

        $users = KpUserWastePreference::where('id', $query)
            ->with('user')
            ->where('is_waste_bank', true)
            ->get();
            // where(function ($q) use ($query) {
            //         $q->where('firstname', 'like', '%' . $query . '%')
            //           ->orWhere('lastname', 'like', '%' . $query . '%')
            //           ->orWhere('email', 'like', '%' . $query . '%')
            //           ->orWhere('phone', 'like', '%' . $query . '%')
            //           ->orWhere('username', 'like', '%' . $query . '%');
            //     })
            // คุณอาจจะเพิ่มเงื่อนไขเพื่อกรองเฉพาะสมาชิกธนาคารขยะที่ active ได้
        ;

        return response()->json($users);
    }
}
