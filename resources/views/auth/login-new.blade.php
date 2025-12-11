@extends('layouts.app')

@section('content')
    <div class="loginMain">
        <div class="loginSec">
            <div class="pb-3 text-center">
                <a href="#"><img src="{{ asset(config('constants.SITE_LOGO')) }}" alt="" width="200" /></a>
            </div>

            <div class="loginFormBox">
                <div class="text-center">
                    <h3 class="afterNone mb-0">Login</h3>
                    <p class="mb-4 mt-0">Hey, Enter your details to Login</p>
                </div>

                {{-- Show login error --}}
                @if (session('error'))
                    <label class="error">{{ session('error') }}</label>
                @endif

                <form method="post" action="{{ route('login.submit') }}">
                    @csrf
                    <input type="hidden" id="guestUserId" name="guest_user_id" value="">

                    <div class="mb-4">
                        <label class="form-label">Email / Mobile Number / Username</label>
                        <input type="text" name="username"
                            class="form-control w-100 @error('username') is-invalid @enderror"
                            value="{{ Cookie::get('remember_username') ?? old('username') }}" autocomplete="username"
                            autofocus>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-1">
                        <label class="form-label">Password</label>
                        <div class="position-relative">
                            <input type="password" id="password" name="password"
                                class="form-control w-100 pe-5 @error('password') is-invalid @enderror"
                                value="{{ Cookie::get('remember_password') ? decrypt(Cookie::get('remember_password')) : '' }}"
                                autocomplete="current-password">

                            <span class="eyeInput eye_icon" data-id="password">
                                <i class="bi bi-eye-slash"></i>
                            </span>

                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="loginbtm mt-2">
                        <div class="cstmCheckbox">
                            <input type="checkbox" id="rememberCheck" name="remember"
                                {{ old('remember', Cookie::get('remember_username') ? 'checked' : '') }}>
                            <label for="rememberCheck">Remember Me</label>
                        </div>

                        @if (Route::has('forgot_password'))
                            <a href="{{ route('forgot_password') }}">Forgot Password?</a>
                        @endif
                    </div>

                    <div class="text-center my-2 mt-4">
                        <button type="submit" class="btn btn-primary-gradient fs-7 rounded-2 w-75">Login</button>
                    </div>

                    <strong class="signupTxt pb-0">Don't have an account?
                        <a href="#">Register</a>
                    </strong>
                </form>
            </div>

            <div class="mainBanner p-0">
                {{-- Background animations remain unchanged --}}
                <span class="bgIcons1"><img src="{{ asset('frontend/images/bgIcon1.svg') }}" width="30"></span>
                <span class="bgIcons2"><img src="{{ asset('frontend/images/bgIcon2.png') }}" width="50"></span>
                <span class="bgIcons3"><img src="{{ asset('frontend/images/bgIcon3.png') }}" width="50"></span>
                <span class="bgIcons4"><img src="{{ asset('frontend/images/bgIcon4.png') }}" width="50"></span>
                <span class="bgIcons5"><img src="{{ asset('frontend/images/bgIcon5.png') }}" width="60"></span>
                <span class="bgIcons6"><img src="{{ asset('frontend/images/bgIcon6.png') }}" width="40"></span>
                <span class="bgIcons7"><img src="{{ asset('frontend/images/bgIcon7.png') }}" width="40"></span>
                <span class="bgIcons8"><img src="{{ asset('frontend/images/bgIcon8.png') }}" width="55"></span>
                <span class="bgIcons9"><img src="{{ asset('frontend/images/bgIcon9.png') }}" width="60"></span>
                <span class="bgIcons10"><img src="{{ asset('frontend/images/bgIcon10.png') }}" width="55"></span>
                <span class="bgIcons11"><img src="{{ asset('frontend/images/bgIcon11.png') }}" width="50"></span>
                <span class="bgIcons12"><img src="{{ asset('frontend/images/bgIcon12.png') }}" width="50"></span>
                <span class="bgIcons13"><img src="{{ asset('frontend/images/bgIcon13.png') }}" width="60"></span>
            </div>
        </div>
    </div>
@endsection
