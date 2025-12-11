@extends('layouts.app')

@section('content')
    <div class="loginMain">
        <div class="loginMain">

            <div class="loginSec registerPage">
                <div class="pb-3 text-center">
                    <a href="{{ route('/') }}"><img src="{{ asset(config('constants.SITE_LOGO')) }}" alt=""
                            width="200" /></a>
                </div>
                <div class="loginFormBox">
                    <h3>Sign Up</h3>
                    <p class="mb-4">Hey there! Enter your details to create your account.</p>
                    @if (session('error'))
                        <span>
                            <label class="error">{{ session('error') }}</label>
                        </span>
                    @endif
                    <form method="POST" action="{{ route('store.qr.register') }}" id="register-password-form">
                        @csrf
                        <div class="row px-md-1">
                            <div class="col-md-6 px-md-2">
                                <div class="form-group mb-4">
                                    <label class="form-label required">Name</label>
                                    <input class="form-control w-100 @error('name') is-invalid @enderror" type="text"
                                        placeholder="" name="name" value="{{ $data->name ?? old('name') }}"
                                        autocomplete="name" autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 px-md-2">
                                <div class="form-group mb-4">
                                    <label class="form-label">Email</label>
                                    <div class="position-relative">
                                        <input class="form-control w-100 pe-5 @error('email') is-invalid @enderror"
                                            id="email" type="email" placeholder="" name="email"
                                            value="{{ $data->email ?? old('email') }}" autocomplete="email">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        {{-- <a href="" class="rightTxt">Verify</a> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 px-md-2">
                                <div class="form-group mb-4">
                                    <label class="form-label required">Mobile Number</label>
                                    <div class="position-relative">
                                        <input class="form-control w-100 pe-5 @error('mobile') is-invalid @enderror"
                                            type="text" placeholder="" id="mobile" name="mobile"
                                            value="{{ $data->mobile_no ?? old('mobile') }}" autocomplete="mobile" autofocus>
                                        @error('mobile')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 px-md-2">
                                <div class="form-group mb-4">
                                    <label for="schoolName" class="mb-2 required">School Name</label>
                                    <div id="schools-data" data-schools='@json($schools)'
                                        style="display: none;"></div>

                                    <input type="text" name="schoolNameSearch" id="schoolNameSearch" required
                                        class="form-control fs-8 @error('schoolName') is-invalid @enderror"
                                        placeholder="Search for a school" autocomplete="off">

                                    <select name="schoolName" id="schoolName" style="display: none;">
                                        <option value="" selected>Select School</option>
                                        @foreach ($schools as $school)
                                            <option value="{{ $school->id }}"
                                                data-classes="{{ json_encode($school->classes ?? []) }}">
                                                {{ $school->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <div id="schoolSearchResults" class="list-group mt-2"
                                        style="display: none; position: absolute; z-index:30;"></div>

                                    @error('schoolName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- <div id="classSelectWrapper" class="col-md-6 px-md-2 form-group mb-3" style="display: none;">
                                <label for="className" class="mb-2 required">Class</label>
                                <select name="className" id="className" required
                                    class="form-control form-select fs-8 @error('className') is-invalid @enderror">
                                    <option value="" selected>Select Class</option>
                                </select>
                                @error('className')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="seriesSelectWrapper" class="col-md-6 px-md-2 form-group mb-3" style="display: none;">
                                <label for="seriesName" class="mb-2 required">Series</label>
                                <select name="seriesName" id="seriesName" required
                                    class="form-control form-select fs-8 @error('seriesName') is-invalid @enderror">
                                    <option value="" selected>Select Series</option>
                                </select>
                                @error('seriesName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div> --}}


                            <div id="classSelectWrapper" class="col-md-6 px-md-2 form-group mb-3" style="display: none;">
                                <label for="className" class="mb-2 required">Class</label>
                                <select name="className" required
                                    class="form-control form-select fs-8 @error('className') is-invalid @enderror">
                                    <option value="" selected>Select Class</option>
                                    @foreach ($classes as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('className')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="seriesSelectWrapper" class="col-md-6 px-md-2 form-group mb-3" style="display: none;">
                                <label for="seriesName" class="mb-2 required">Series</label>
                                <select name="seriesName" required
                                    class="form-control form-select fs-8 @error('seriesName') is-invalid @enderror">
                                    <option value="" selected>Select Series</option>
                                    @foreach ($series as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('seriesName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div id="subjectSelectWrapper" class="col-md-6 px-md-2 form-group mb-3" style="display: none;">
                                <label for="subject" class="mb-2 required">Subject</label>
                                <select id="subjectSelect" name="subject" required
                                    class="form-control form-select fs-8 @error('subject') is-invalid @enderror">
                                    <option value="" selected>Select Subject</option>
                                    @foreach ($subjects as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 px-md-2">
                                <div class="form-group mb-4">
                                    <label class="form-label required">Password</label>
                                    <div class="position-relative">
                                        <input class="form-control w-100 pe-5 @error('password') is-invalid @enderror"
                                            id="password" type="password" placeholder="" name="password"
                                            autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <span class="eyeInput eye_icon" data-id="password">
                                            <i class="bi bi-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-6 px-md-2">
                                <div class="form-group mb-4">
                                    <label class="form-label required">Confirm Password</label>
                                    <div class="position-relative">
                                        <input class="form-control w-100 pe-5"type="password" id="password_confirmation"
                                            placeholder="" name="password_confirmation" autocomplete="new-password">
                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <span class="eyeInput eye_icon" data-id="password_confirmation">
                                            <i class="bi bi-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                            <label for="captcha" class="col-md-4 control-label required">Captcha</label>
                            <div class="col-md-12">
                                <div class="captcha">
                                    <div class="d-flex align-items-center gap-2 mb-4">
                                        <span>{!! captcha_img() !!}</span>
                                        <button type="button" class="bg-transparent border-0 btn-refresh">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                        <div style="flex-grow: 1;"> <!-- This ensures it takes available width -->
                                            <input id="captcha" type="text" class="form-control"
                                                placeholder="Enter Captcha" name="captcha">
                                            @if ($errors->has('captcha'))
                                                <div class="text-danger mt-1">
                                                    <small>{{ $errors->first('captcha') }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="loginbtm mt-2">
                            <div class="cstmCheckbox">
                                <input type="checkbox" id="termsCheck" checked name="terms_accepted">
                                <label for="termsCheck">By Clicking you are indicating that you have read and agreed to the
                                    <a href="{{ route('terms.condition') }}">terms of use</a> & <a
                                        href="{{ route('privacy.policy') }}">Privacy
                                        policy</a></label>
                                @error('terms_accepted')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="text-center my-2 mt-4">
                            <button type="submit" class="btn btn-primary-gradient fs-7 rounded-2 w-75">Sign Up</button>
                        </div>

                        {{-- <strong class="signupTxt pb-0">Already have an account?
                            <a href="{{ route('login') }}">Login</a>
                        </strong> --}}

                    </form>
                </div>
            </div>
            <div class="mainBanner p-0">
                <span class="bgIcons1"><img src="{{ asset('frontend/images/bgIcon1.svg') }}" width="30"></span>
                <span class="bgIcons2"><img src="{{ asset('frontend/images/bgIcon2.png ') }}" width="50"></span>
                <span class="bgIcons3"><img src="{{ asset('frontend/images/bgIcon3.png ') }}" width="50"></span>
                <span class="bgIcons4"><img src="{{ asset('frontend/images/bgIcon4.png ') }}" width="50"></span>
                <span class="bgIcons5"><img src="{{ asset('frontend/images/bgIcon5.png ') }}" width="60"></span>
                <span class="bgIcons6"><img src="{{ asset('frontend/images/bgIcon6.png ') }}" width="40"></span>
                <span class="bgIcons7"><img src="{{ asset('frontend/images/bgIcon7.png ') }}" width="40"></span>
                <span class="bgIcons8"><img src="{{ asset('frontend/images/bgIcon8.png ') }}" width="55"></span>
                <span class="bgIcons9"><img src="{{ asset('frontend/images/bgIcon9.png ') }}" width="60"></span>
                <span class="bgIcons10"><img src="{{ asset('frontend/images/bgIcon10.png ') }}" width="55"></span>
                <span class="bgIcons11"><img src="{{ asset('frontend/images/bgIcon11.png ') }}" width="50"></span>
                <span class="bgIcons12"><img src="{{ asset('frontend/images/bgIcon12.png ') }}" width="50"></span>
                <span class="bgIcons13"><img src="{{ asset('frontend/images/bgIcon13.png ') }}" width="60"></span>
            </div>
        </div>



        <div class="modal fade" id="ragister" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Account Create</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <lottie-player src="{{ asset('frontend/images/advertising.json') }}" background="transparent"
                            speed="1" style="width: 180px; height: 180px;margin: auto;" loop
                            autoplay></lottie-player>
                        <h6 class="text-center">Congratulations!!</h6>
                        <p class="text-center">Your account has been successfully created.</p>
                        <div class="text-center my-2 mt-4">
                            <a href="{{ route('login') }}"
                                class="btn btn-primary-gradient fs-7 rounded-2 w-50">Continue</a>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <script>
            $(".btn-refresh").click(function() {
                $.ajax({
                    type: 'GET',
                    url: '/refresh-captcha',
                    success: function(data) {
                        $(".captcha span").html(data.captcha);
                    }
                });
            });
        </script>
    @endsection
