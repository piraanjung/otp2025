<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Keptkaya\KpTbankItemsGroups;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KpTbankItemsGroupsController extends Controller
{
    public function index()
    {
        $kp_tbank_item_groups = KpTbankItemsGroups::where('org_id_fk', Auth::user()->org_id_fk)
            ->where('status', 'active')->get();
        return view('keptkayas.tbank.items_group.index', compact('kp_tbank_item_groups'));
    }

    public function create()
    {
        return view('keptkayas.tbank.items_group.create');
    }

    public function store(Request $request)
    {
        foreach ($request->get('kp_items_groupname') as $item_groupname) {
            KpTbankItemsGroups::create([
                'org_id_fk'             => Auth::user()->org_id_fk,
                'kp_items_groupname'    => $item_groupname["'name'"],
                'kp_items_group_code'   => $item_groupname["'code'"],
                'status'                => 'active',
                'deleted'               => '0',
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->route('keptkayas.tbank.items_group.index');
    }
}
