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
            <img src="{{asset('logo/ko_envsogo.png')}}" class="w-40 h-40 fill-current text-gray-500">

                {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
            </a>
            {{-- <h1>Envsogo</h1> --}}
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
                    value="admin1"
                    required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required 
                    value="999999999"
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
