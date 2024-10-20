<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BudgetYear;
use App\Models\InvoicePeriod;
use Illuminate\Http\Request;

class InvoicePeriodController extends Controller
{
    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Bangkok');

        $inv_period = new InvoicePeriod();
        $inv_period->budgetyear_id = $request->get('budgetyear_id');
        $inv_period->inv_period_name = $request->get('inv_period_name');
        $inv_period->startdate = $request->get('fromdate');
        $inv_period->enddate = $request->get('enddate');
        $inv_period->status = $request->get('status');
        $inv_period->created_at = date('Y-m-d H:i:s');
        $inv_period->updated_at = date('Y-m-d H:i:s');
        $inv_period->save();
        return response()->json($request);
    }

    public function edit($id)
    {
        $budgetyear = InvoicePeriod::where('id', $id)->with('budgetyear')->first();
        return response()->json($budgetyear);
    }

    public function update(Request $request)
    {
        date_default_timezone_set('Asia/Bangkok');

        $inv_period = InvoicePeriod::where('id', $request->get('id'))->first();
        $inv_period->budgetyear_id = $request->get('budgetyear_id');
        $inv_period->inv_period_name = $request->get('inv_period_name');
        $inv_period->startdate = $request->get('fromdate');
        $inv_period->enddate = $request->get('enddate');
        $inv_period->status = $request->get('status');
        $inv_period->updated_at = date('Y-m-d H:i:s');
        $inv_period->update();
        return response()->json(['res' => 'ok', 'status' => 200]);
    }

    public function prensent_budgetyear()
    {
        $presentbudgetyear = BudgetYear::where('status', 'active')->first();
        return response()->json($presentbudgetyear);
    }

    public function check_invoice_period_by_budgetyear($budgetyear)
    {
        $invoices = InvoicePeriod::where('budgetyear_id', $budgetyear)
            ->where('status', '<>', 'deleted')
            ->count();
        return $invoices;
    }

    public function inv_period_lists($budgetyear_id)
    {
        $current_budgetyear = BudgetYear::where('id', $budgetyear_id)
            ->with(['invoicePeriod' => function ($query) {
                $query->select('id', 'inv_p_name', 'budgetyear_id')
                    ->whereIn('status', ['active', 'inactive']);
            }])
            ->get(['id']);


        $invPeriod_buggetYear_array = collect($current_budgetyear[0]->invoicePeriod)->toArray();

        return \response()->json($invPeriod_buggetYear_array);
    }
}
