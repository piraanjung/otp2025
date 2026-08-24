<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
    
        return view('auth.login');
    }

   
    public function store(LoginRequest $request)
    {
        $login_staff = $request->input('login_staff');
        $request->authenticate();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ismobile = preg_match(
            "/(android|avantgo|blackberry|bolt|boost|cello|hiptop|irengin|mobi|mini|mo(bil|si)|ntellect|palm|pda|phone|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|wap|windows ce|xda|xiino)/i",
            $userAgent
        );
        $request->session()->regenerate();
        $user = User::find(Auth::id());
        if ($ismobile || $login_staff == 1) {
            if(isset($request->kp_mobile_login)){
                //ตู้รับซื้อขวด
                redirect()->intended(route('kp_mobile.create', absolute: false));
            }else if($user->hasRole('Staff') || $user->hasRole('Admin') || $login_staff == 1){
                return redirect()->intended(route('staff_accessmenu', absolute: false));
            }else if($user->hasRole('Super Admin')){
                return redirect()->intended(route('admin.org_selector', absolute: false));
            }
        }

        if($user->hasRole('Super Admin')){
            return redirect()->intended(route('admin.org_selector', absolute: false));
        }

        return redirect()->intended(route('accessmenu', absolute: false));

    }


    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

}
