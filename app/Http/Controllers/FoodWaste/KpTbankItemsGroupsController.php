<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Keptkaya\KpTbankItemsGroups;
use Illuminate\Http\Request;

class KpTbankItemsGroupsController extends Controller
{
    public function index()
    {
        $kp_tbank_item_groups = KpTbankItemsGroups::where('status', 'active')->get();
        return view('keptkaya.tbank.items_group.index', compact('kp_tbank_item_groups'));
    }

    public function create()
    {
        return view('keptkaya.tbank.items_group.create');
    }

    public function store(Request $request)
    {
        foreach ($request->get('kp_items_groupname') as $item_groupname) {
            KpTbankItemsGroups::create([
                'kp_items_groupname' => $item_groupname,
                'status' => 'active',
                'deleted' => '0',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->route('keptkaya.tbank.items_group.index');
    }
}
