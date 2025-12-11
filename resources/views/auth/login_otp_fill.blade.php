@extends('layouts.app')

@livewireStyles

@section('content')
    <div class="loginMain">
        <div class="loginSec">
            <div class="pb-3 text-center">
                <a href="{{ route('/') }}"><img src="{{ asset(config('constants.SITE_LOGO')) }}" alt=""
                        width="200" /></a>
            </div>
            <div class="loginFormBox">
                <h3>Login OTP</h3>
                <p class=" mb-4">Hey, Enter Your details to Login</p>
                @if (isset($msg))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ $msg }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <form method="post" action="{{ route('login.otp.check') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Email/Mobile Number</label>
                        <input class="form-control w-100" type="text" name="id" id="id"
                            value="{{ old('id', isset($userId) ? $userId : request()->userId) }}" readonly required
                            autocomplete="id" autofocus>
                    </div>
                    <a href="{{ route('login.otp') }}" class="textUnderline mb-3">Change Email or Mobile Number</a>
                    <div class="otpMain">
                        <strong>Enter OTP</strong>
                        <div class="otpFind">
                            @php
                                $otpValue = session('otp_value', '') ?? $otp; // Get OTP value from the session
                                $otpDigits = str_split($otpValue); // Split the OTP into individual digits
                            @endphp
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" class="form-control otp-input" maxlength="1" name="otp[]"
                                    value="{{ old('otp') }}" required oninput="handleInput(this, {{ $i }})"
                                    autocomplete="off" />
                            @endfor
                        </div>
                        @if (session('error'))
                            <span>
                                <label class="error">{{ session('error') }}</label>
                            </span>
                        @endif
                    </div>



                    <div>
                        <livewire:otp-timer />
                    </div>
                    @livewireScripts


                    <strong class="signupTxt pb-0">Didn't get a OTP?
                        <a id="resend_otp" class="resend_coursor"><u>Click to Resend</u></a>
                    </strong>

                    <span class="lineotp"> <b>
                            @if (Route::has('admin.login'))
                                <a href="{{ route('login') }}"> Or Login with Password</a>
                            @endif
                        </b></span>



                    <div class="text-center my-2 mt-4">
                        <button type="submit" class="btn btn-primary-gradient fs-7 rounded-2 w-75">Login</button>
                    </div>

                    <strong class="signupTxt pb-0">Don't have an account?
                        <a href="{{ route('register') }}">Register</a>
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#resend_otp').on('click', function() {
                const mobileEmail = $('#id').val();
                $.ajax({
                    url: '{{ route('login.resend.otp') }}',
                    type: 'POST',
                    data: {
                        mobile_email: mobileEmail,
                        _token: '{{ csrf_token() }}'
                    },

                });
            });
        });
    </script>




    <script>
        function moveToNext(current) {
            const next = current.nextElementSibling;
            if (current.value.length === current.maxLength && next) {
                next.focus();
            }
        }
        document.querySelectorAll('.otp-input').forEach((input) => {
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value === '') {
                    const previous = input.previousElementSibling;
                    if (previous) {
                        previous.focus();
                    }
                }
            });
        });
    </script>

    <script>
        function handleInput(input, index) {
            const value = input.value;

            // Move to next input if a digit is entered
            if (value.length === 1 && index < 5) {
                const nextInput = input.parentNode.children[index + 1];
                if (nextInput) nextInput.focus();
            }

            // Move to previous input if the input is deleted
            if (value.length === 0) {
                if (index > 0) {
                    const prevInput = input.parentNode.children[index - 1];
                    if (prevInput) {
                        prevInput.focus();
                        prevInput.select(); // Select content to easily replace it
                    }
                }
            }
        }
    </script>
@endsection
