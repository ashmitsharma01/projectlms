@extends('layouts.app')

@section('content')
    <div class="loginMain">
        <div class="loginSec">
            <div class="pb-3 text-center">
                <a href="{{route('/')}}"><img src="{{ asset(config('constants.SITE_LOGO')) }}" alt="" width="200"/></a>
            </div>
            <div class="loginFormBox">
                <h3>Login OTP</h3>
                <p class=" mb-4">Hey, Enter your details to Login </p>
                <form method="post" action="{{ route('login.otp.fill') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Email/Mobile Number</label>
                        <input class="form-control w-100 @error('id') is-invalid @enderror" type="text" name="id"
                            value="{{ old('id') }}" required autocomplete="id" autofocus placeholder="">
                        @if (session('error'))
                            <span>
                                <label class="error">{{ session('error') }}</label>
                            </span>
                        @endif

                    </div>

                    <span class="lineotp"> <b>
                            @if (Route::has('admin.login'))
                                <a href="{{ route('login') }}">
                                    {{ __('or Login with Password') }}
                                </a>
                            @endif
                        </b></span>



                    <div class="text-center my-2 mt-4">
                        <button type="submit" class="btn btn-primary-gradient fs-7 rounded-2 w-75">Submit</button>
                    </div>


                    <strong class="signupTxt pb-0">Don't have an account?
                        @if (Route::has('admin.register'))
                            <a href="{{ route('register') }}">
                                {{ __('Register') }}
                            </a>
                        @endif
                    </strong>



                </form>
            </div>
        </div>
        <div class="mainBanner p-0">
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
@endsection
