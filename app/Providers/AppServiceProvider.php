<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Organization; // หรือ Model ที่คุณใช้เรียก getOrgName
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (env(key: 'APP_ENV') === 'local' && request()->server(key: 'HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme(scheme: 'https');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // สั่งว่า ถ้ามีการเรียกใช้ View 'layouts.admin1' (หรือทุกหน้าที่มี layout นี้)
        // ให้ทำงานใน fuction นี้
        View::composer('layouts.admin1', function ($view) {
            
            $orgInfos = [];

            // เช็คก่อนว่า Login หรือยัง (กัน Error กรณีหน้า Login ที่ยังไม่มี User)
            if (Auth::check()) {
                $user = Auth::user();
                // เรียกใช้ Function เดิมที่คุณมีอยู่แล้ว
                $orgInfos = Organization::getOrgName($user->org_id_fk);
            }

            // ส่งตัวแปร $orgInfos ไปที่ View
            $view->with('orgInfos', $orgInfos);
        });
    }
}
