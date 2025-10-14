<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
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

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ismobile = preg_match(
            "/(android|avantgo|blackberry|bolt|boost|cello|hiptop|irengin|mobi|mini|mo(bil|si)|ntellect|palm|pda|phone|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|wap|windows ce|xda|xiino)/i",
            $userAgent
        );
        $request->session()->regenerate();

        if ($ismobile) {
            if(isset($request->kp_mobile_login)){
                return redirect()->intended(route('kp_mobile.create', absolute: false));
            }
            return redirect()->intended(route('staff_accessmenu', absolute: false));
        }

        return redirect()->intended(route('accessmenu', absolute: false));

        // return redirect()->intended(RouteServiceProvider::HOME);
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
