@extends('layouts.app')

@section('content')
    <div class="loginMain">
        <div class="loginSec">
            @if ($errors->any())
                {!! implode('', $errors->all('<div>:message</div>')) !!}
            @endif

            <div class="pb-3 text-center">
                <a href="{{ route('/') }}"><img src="{{ asset(config('constants.SITE_LOGO')) }}" alt=""
                        width="200" /></a>
            </div>
            <div class="loginFormBox">
                <div class="d-flex justify-content-between">
                    <h3>Verify Email</h3>
                    <a href="{{ route('login', $data) }}" class="backBtn"><img
                            src="{{ asset('frontend/images/backbtn.svg') }}" alt="" width="14"> Back</a>
                </div>
                <p class=" mb-4">Verify your Email to login your account</p>

                <form method="post" action="{{ route('login.emailverify.check') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Email</label>
                        <input class="form-control w-100 @error('username') is-invalid @enderror" type="text" readonly
                            placeholder="" name="id" id="id" value="{{ $data }}" required
                            autocomplete="id" autofocus>
                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <a href="{{ route('login') }}" class="textUnderline mb-3">Change Email</a>

                    <div class="otpMain">
                        <strong>Enter OTP</strong>
                        <div class="otpFind">
                            @php
                                $otpValue = session('otp_value', ''); // Get OTP value from the session
                                $otpDigits = str_split($otpValue); // Split the OTP into individual digits
                            @endphp
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" class="form-control otp-input" maxlength="1" name="otp[]"
                                    value="{{ isset($otpDigits[$i]) ? $otpDigits[$i] : '' }}" required
                                    oninput="handleInput(this, {{ $i }})" autocomplete="off" />
                            @endfor
                        </div>
                        @if (isset($error))
                            <span class="error-msg" role="alert">
                                <strong class="error">{{ $error }}</strong>
                            </span>
                        @endif
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

                    <div class="text-center my-2 mt-4">
                        <button type="submit" class="btn btn-primary-gradient fs-7 rounded-2 w-75">Submit</button>
                    </div>

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

    <div class="modal fade" id="VerifyMdl" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Verify Mobile</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <lottie-player src="{{ asset('frontend/images/done.json') }}" background="transparent"
                        speed="1" style="width: 180px; height: 180px;margin: auto;" loop autoplay></lottie-player>
                    <h6 class="text-center fw-semibold">verified!</h6>
                    <p class="text-center">Your Email successfully verified</p>
                    <div class="text-center my-2 mt-4">
                        <a href="{{ route('/') }}" class="btn btn-primary-gradient fs-7 rounded-2 w-75">Continue
                        </a>
                    </div>
                </div>
            </div>
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
