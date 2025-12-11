@extends('layouts.app')

@section('content')
<div class="loginMain">
    <div class="loginSec">
        <div class="pb-3 text-center">
            <a href="{{route('/')}}"><img src="{{ asset(config('constants.SITE_LOGO')) }}" alt="" width="200"/></a>
        </div>
        <div class="loginFormBox">
            <h3>Forgot Password</h3>
            <p class=" mb-4">Enter your Email/Mobile Number and we'll send you a OTP to get back into your account.
            </p>
            <form method="post" action="{{ route('password_otp') }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label">Email/Mobile Number</label>
                    <input class="form-control w-100 @error('id') is-invalid @enderror" type="text" placeholder="" name="id" value="{{ old('id') }}" required autocomplete="id" autofocus>
                    @if (session('error'))
                        <span class="error-msg" role="alert">
                            <strong class="error">{{ session('error') }}</strong>
                        </span>
                    @endif

                </div>
                <div class="text-center my-2 mt-4">
                    <button type="submit" class="btn btn-primary-gradient fs-7 rounded-2 w-75">Send OTP</button>
                </div>

                <strong class="signupTxt pb-0">Already have an account? 
                    <a href="{{route('login')}}">Login</a>
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
