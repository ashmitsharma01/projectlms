@extends('layouts.app')

@section('content')
    <div class="loginMain">
        <div class="loginSec">
            <div class="pb-3 text-center">
                <a href="{{route('/')}}"><img src="{{ asset(config('constants.SITE_LOGO')) }}" alt="" width="200"/></a>
            </div>
            <div class="loginFormBox">
                <h3>Reset Password</h3>
                <p class=" mb-4">Your new password must be different from previous used password. </p>
                <form method="POST" action="{{ route('reset.password.submit') }}" id="reset-password-form">
                    @csrf

                    <input type="hidden" name="username" value="{{ $id }}">
                    <div class="mb-1">
                        <label class="form-label">Password</label>
                        <div class="position-relative">
                            <input class="form-control w-100 pe-5 @error('password') is-invalid @enderror" type="password"
                                placeholder="" name="password" id="password" required autocomplete="new-password">
                            @if (session('error'))
                                <span>
                                    <label class="error">{{ session('error') }}</label>
                                </span>
                            @endif
                            <span class="eyeInput eye_icon" data-id="password">
                                <i class="bi bi-eye-slash"></i>
                            </span>
                        </div>
                    </div>

                    <div class="mb-1">
                        <label class="form-label"> Confirm Password</label>
                        <div class="position-relative">
                            <input class="form-control w-100 pe-5" type="password" name="password_confirmation"
                                id="password_confirmation" required autocomplete="new-password" placeholder="">
                            @if (session('error'))
                                <span>
                                    <label class="error">{{ session('error') }}</label>
                                </span>
                            @endif
                            <span class="eyeInput eye_icon" data-id="password_confirmation">
                                <i class="bi bi-eye-slash"></i>
                            </span>
                        </div>
                    </div>


                    <div class="text-center my-2 mt-4">
                        <button type="submit" class="btn btn-primary-gradient fs-7 rounded-2 w-75"
                           >Submit</button>
                    </div>

                    <strong class="signupTxt pb-0">Don't have an account?
                        <a href="{{ route('register') }}">Register</span>
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


    <div class="modal fade" id="passReset" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5">Password Reset</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <lottie-player src="{{ asset('frontend/images/Id Password.json') }}" background="transparent"
                        speed="1" style="width: 130px; height: 130px;margin: auto;" loop autoplay></lottie-player>
                    <h6 class="text-center">All Done!</h6>
                    <p class="text-center">Your password has been reset.</p>
                    <div class="text-center my-2 mt-4">
                        <a href="{{ route('login') }}" class="btn btn-primary-gradient fs-7 rounded-2 w-50">Continue
                            Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if (session('status'))
        <script>
            $(document).ready(function() {
                $('#passReset').modal('show');
            });
        </script>
    @endif
@endsection
