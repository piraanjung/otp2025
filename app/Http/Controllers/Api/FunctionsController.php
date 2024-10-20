<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use App\Models\UserMerterInfo;
use App\Models\UserProfile;
use App\Models\Setting;
use Illuminate\Http\Request;

class FunctionsController extends Controller
{
  public static function thaiDateToEngDateFormat($date)
  {

    $dateExp = explode("/", $date);
    $cEyear = intval($dateExp[2]) - 543;
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
    $month = [
      'ม.ค.' => '01',
      'ก.พ.' => '02',
      'มี.ค.' => '03',
      'เม.ย.' => '04',
      'พ.ค.' => '05',
      'มิ.ย.' => '06',
      'ก.ค.' => '07',
      'ส.ค.' => '08',
      'ก.ย.' => '09',
      'ต.ค.' => '10',
      'พ.ย.' => '11',
      'ธ.ค.' => '12',
    ];
    return array_search($m, $month);
  }

  public static function fullThaiMonth($m)
  {
    $month = [
      'มกราคม' => '01',
      'กุมภาพันธ์' => '02',
      'มีนาคม' => '03',
      'เมษายน' => '04',
      'พฤษภาคม' => '05',
      'มิถุนายน' => '06',
      'กรกฎาคม' => '07',
      'สิงหาคม' => '08',
      'กันยายน' => '09',
      'ตุลาคม' => '10',
      'พฤศจิกายน' => '11',
      'ธันวาคม' => '12',
    ];
    return array_search($m, $month);
  }

  public static function createInvoiceNumberString($id)
  {
    $meternumber_code = Setting::where('name', 'meternumber_code')->get('values')->first();

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
    return $meternumber_code->values . "" . $invString;
  }

  public static function createMeterNumberString($id)
  {
    $meternumber_code = Setting::where('name', 'meternumber_code')->get('values')->first();

    $invString = '';
    if ($id < 10) {
      $invString = '000' . $id;
    } else if ($id >= 10 && $id < 100) {
      $invString = '00' . $id;
    } else if ($id >= 100 && $id < 1000) {
      $invString = '0' . $id;
    } elseif ($id >= 1000 && $id < 9999) {
      $invString = $id;
    } else {
      $invString = $id;
    }
    return $meternumber_code->values . "10" . $invString;
  }

  public static function createNumberString($id, $type)
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
    return $type . "" . $invString;
  }

  public function updateInactiveStatus($user_id, $meter_id)
  {
    $updateUsermeterInfos = UserMerterInfo::where('id', $meter_id)
      ->update(['status' => 'inactive', 'comment' => 'ค้างขำระ 5 งวด']);

    $updateUserProfile = UserProfile::where('user_id', $user_id)
      ->update(['status' => 0]);

    $updateUser = User::where('id', $user_id)->update([
      'status' => 'inactive',
    ]);
  }

  // public static function reset_auto_increment_when_deleted($subzone){

  // }

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

  public static function invoice_last_record()
  {
    return Invoice::get()->last();
  }

  public function forget_session(REQUEST $request)
  {
    return $request;
  }

  public static function convertAmountToLetter($number)
  {
    if (empty($number)) return "";
    $number = strval($number);
    $txtnum1 = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า', 'สิบ');
    $txtnum2 = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
    $number = str_replace(",", "", $number);
    $number = str_replace(" ", "", $number);
    $number = str_replace("บาท", "", $number);
    $number = explode(".", $number);
    if (sizeof($number) > 2) {
      return '';
      exit;
    }
    $strlen = strlen($number[0]);
    $convert = '';
    for ($i = 0; $i < $strlen; $i++) {
      $n = substr($number[0], $i, 1);
      if ($n != 0) {
        if ($i == ($strlen - 1) && $n == 1) {
          $convert .= 'เอ็ด';
        } elseif ($i == ($strlen - 2) && $n == 2) {
          $convert .= 'ยี่';
        } elseif ($i == ($strlen - 2) && $n == 1) {
          $convert .= '';
        } else {
          $convert .= $txtnum1[$n];
        }
        $convert .= $txtnum2[$strlen - $i - 1];
      }
    }
    $convert .= 'บาท';
    if (sizeof($number) == 1) {
      $convert .= 'ถ้วน';
    } else {
      if ($number[1] == '0' || $number[1] == '00' || $number[1] == '') {
        $convert .= 'ถ้วน';
      } else {
        $number[1] = substr($number[1], 0, 2);
        $strlen = strlen($number[1]);
        for ($i = 0; $i < $strlen; $i++) {
          $n = substr($number[1], $i, 1);
          if ($n != 0) {
            if ($i > 0 && $n == 1) {
              $convert .= 'เอ็ด';
            } elseif ($i == 0 && $n == 2) {
              $convert .= 'ยี่';
            } elseif ($i == 0 && $n == 1) {
              $convert .= '';
            } else {
              $convert .= $txtnum1[$n];
            }
            $convert .= $i == 0 ? $txtnum2[1] : '';
          }
        }
        $convert .= 'สตางค์';
      }
    }
    return $convert;
  }
}
