<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IssueType;
use Illuminate\Http\Request;

class IssueTyeController extends Controller
{
    public function lists($systemType){
        $issue_types_list = IssueType::where('system_type', $systemType)->get();
        return response()->json($issue_types_list);
    }
}
