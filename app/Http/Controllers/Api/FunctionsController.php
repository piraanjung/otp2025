<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Invoice;
use App\User;
use App\UserMeterInfos;
use App\UserProfile;
use Illuminate\Http\Request;

class FunctionsController extends Controller
{
    public function thaiDateToEngDateFormat($date)
    {
        $dateExp = explode("/", $date);
        $cEyear = $dateExp[2] - 543;
        return $cEyear . "-" . $dateExp[1] . "-" . $dateExp[0];

    }

    public function engDateToThaiDateFormat($date)
    {
        $dateExp = explode("-", $date);
        $cEyear = $dateExp[0] + 543;
        return $dateExp[2] . "/" . $dateExp[1] . "/" . $cEyear;

    }

    public static function shortThaiMonth($m)
    {
        $month = ['ม.ค.' => '01', 'ก.พ.' => '02', 'มี.ค.' => '03', 'เม.ย.' => '04',
            'พ.ค.' => '05', 'มิ.ย.' => '06', 'ก.ค.' => '07', 'ส.ค.' => '08',
            'ก.ย.' => '09', 'ต.ค.' => '10', 'พ.ย.' => '11', 'ธ.ค.' => '12',
        ];
        return array_search($m, $month);
    }

    public static function fullThaiMonth($m)
    {
        $month = ['มกราคม' => '01', 'กุมภาพันธ์' => '02', 'มีนาคม' => '03',
            'เมษายน' => '04', 'พฤษภาคม' => '05', 'มิถุนายน' => '06',
            'กรกฎาคม' => '07', 'สิงหาคม' => '08', 'กันยายน' => '09',
            'ตุลาคม' => '10', 'พฤศจิกายน' => '11', 'ธันวาคม' => '12',
        ];
        return array_search($m, $month);
    }

    public static function createInvoiceNumberString($id)
    {
        $invString = '';
        if ($id < 10) {
            $invString = '0000' . $id;
        } else if ($id >= 10 && $id < 100) {
            $invString = '000' . $id;
        } else if ($id >= 100 && $id < 1000) {
            $invString = '00' . $id;
        } elseif ($id >= 1000 && $id < 9999) {
            $invString = '0' . $id;
        } else {
            $invString = $id;
        }
        return $invString;

    }

<<<<<<< HEAD

=======
    public function updateInactiveStatus($user_id, $meter_id)
    {
        $updateUsermeterInfos = UserMeterInfos::where('id', $meter_id)
            ->update(['status' => 'inactive', 'comment' => 'ค้างขำระ 5 งวด']);

        $updateUserProfile = UserProfile::where('user_id', $user_id)
            ->update(['status' => 0]);

        $updateUser = User::where('id', $user_id)->update([
            'status' => 'inactive',
        ]);

    }
>>>>>>> origin/main

    public function statusThai($status)
    {
        $str = '';
        if ($status == 'owe') {
            $str = 'ค้างชำระ';
        } elseif ($status == 'invoice') {
            $str = 'ออกใบแจ้งหนี้';
        } elseif ($status == 'paid') {
            $str = 'ชำระแล้ว';
        }
        return $str;
    }

<<<<<<< HEAD

=======
    public static function invoice_last_record()
    {
        return Invoice::get()->last();
    }
>>>>>>> origin/main

    public function forget_session(REQUEST $request)
    {
        return $request;
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> origin/main
