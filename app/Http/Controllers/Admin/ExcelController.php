<?php

namespace App\Http\Controllers\Admin;

use App\Imports\UsersImport;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function index(){
        $users = User::all();
        return view("admin.excel.index", compact("users"));
    }
    public function create(){
        return view("admin.excel.create");
    }
    public function store(Request $request){
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Get the uploaded file
        $file = $request->file('file');

        // Process the Excel file
        Excel::import(new UsersImport, $file);

        // return redirect()->back()->with('success', 'Excel file imported successfully!');
        return 'saved';
    }
}
