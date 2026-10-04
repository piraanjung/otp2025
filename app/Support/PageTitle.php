<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * หาชื่อเมนูของหน้าที่เปิดอยู่ จาก config/page_titles.php (ใช้เป็น <title> ของแท็บ browser)
 */
class PageTitle
{
    public static function for(?Request $request = null): ?string
    {
        $request ??= request();

        foreach (config('page_titles', []) as $pattern => $title) {
            if ($request->routeIs($pattern)) {
                return $title;
            }
        }

        return null;
    }
}
