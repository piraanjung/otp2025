<?php

namespace App\Helpers;

class CarbonTranslator
{
    /**
     * แปลงคาร์บอน (kgCO2e) เป็นจำนวนต้นไม้ที่ต้องปลูก
     * อ้างอิง: ต้นไม้ยืนต้น 1 ต้น ดูดซับคาร์บอนได้เฉลี่ย 9-15 kgCO2e/ปี
     * เราจะใช้ค่ากลางคือ 10 kg เพื่อให้จำง่าย
     */
    public static function toTrees($kgCO2)
    {
        if ($kgCO2 <= 0) return 0;
        // ปัดเศษลงเพื่อให้ตัวเลขดูน่าเชื่อถือ (Conservative estimate)
        return floor($kgCO2 / 10);
    }

    /**
     * แปลงคาร์บอน (kgCO2e) เป็นระยะทางที่รถยนต์วิ่ง (กิโลเมตร)
     * อ้างอิง: รถยนต์ทั่วไปปล่อยก๊าซเฉลี่ย 120 กรัม (0.12 kg) ต่อ 1 กม.
     */
    public static function toCarDistance($kgCO2)
    {
        if ($kgCO2 <= 0) return 0;
        // สูตร: คาร์บอนที่ลดได้ / การปล่อยของรถต่อ กม.
        return number_format($kgCO2 / 0.12, 2);
    }
}
