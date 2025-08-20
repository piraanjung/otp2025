<x-guest-layout>
    <style>
        .badge-primary {
            color: #fff;
            background-color: #007bff;
        }

        .badge {
            display: inline-block;
            padding: .25em .4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
        }
    </style>
    <x-auth-card>
        <div class="flex items-center justify-end mb-4">
            <a href="{{ url('/') }}" class="badge badge-primary">Home</a>
        </div>

        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
            <h2>ระบบประปา</h2>
        </x-slot>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="username" :value="__('User name')" />

                <x-input id="username" class="block mt-1 w-full" type="text" name="username" 
                    value="twman1"
                    required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required 
                    value="tw1234"
                    autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                {{-- <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label> --}}
            </div>

            <div class="flex items-center justify-end mt-4">
                {{-- @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif --}}

                <x-button class="ml-3">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
<script src="https://static.line-scdn.net/liff/edge/versions/2.21.1/sdk.js"></script>
<script>
        window.onload = function() {
            const statusElement = document.getElementById('status');

            liff.init({
                liffId: '1656703539-Rzmb63NE' // <<== แทนที่ด้วย LIFF ID ของคุณ
            })
            .then(() => {
                statusElement.textContent = "LIFF ID ถูกต้องและเชื่อมต่อสำเร็จ";
                alert("LIFF ID ถูกต้องและเชื่อมต่อสำเร็จ");
                
                if (liff.isInClient()) {
                    statusElement.textContent = "Redirecting to external browser...";
                    
                    setTimeout(() => {
                        const currentUrl = window.location.href;
                        
                        liff.openWindow({
                            url: currentUrl,
                            external: true
                        });
                    }, 100);
                } else {
                    statusElement.textContent = "Running in an external browser.";
                }
            })
            .catch((err) => {
                statusElement.textContent = "เกิดข้อผิดพลาดในการเชื่อมต่อ LIFF";
                alert('err')
                alert("เกิดข้อผิดพลาดในการเชื่อมต่อ LIFF SDK:", err.code, err.message);
                
                if (err.code === "2001") {
                    alert("LIFF ID ที่ระบุไม่ถูกต้อง กรุณาตรวจสอบ LIFF ID");
                } else {
                    alert("เกิดข้อผิดพลาดในการเชื่อมต่อ LIFF: " + err.message);
                }
            });
        };
    </script>