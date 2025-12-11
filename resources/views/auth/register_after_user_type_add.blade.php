@extends('layouts.app')

@section('content')
    <div class="loginMain">
        <div class="loginMain">
            <style>
                /* Add border and square look */
                .custom-check-olympiad {
                    height: 15px !important;
                    width: 15px !important;
                    appearance: none;
                    /* Reset default browser styles */
                    -webkit-appearance: none;
                    -moz-appearance: none;
                    background-color: #fff;
                    /* White background */
                    cursor: pointer;
                    display: inline-block;
                    position: relative;
                }

                /* Add checkmark when checked */
                .custom-check-olympiad:checked {
                    background-color: #044783;
                    /* Bootstrap blue */
                    border-color: #044783;
                }

                .custom-check-olympiad:checked::after {
                    content: "✔";
                    color: #fff;
                    font-size: 12px;
                    position: absolute;
                    top: -1px;
                    left: 2px;
                }
            </style>
            <div class="loginSec registerPage">
                <div class="pb-3 text-center">
                    <a href="{{ route('/') }}"><img src="{{ asset(config('constants.SITE_LOGO')) }}" alt=""
                            width="200" /></a>
                </div>
                <div class="loginFormBox">
                    <h3>Registration</h3>
                    <p class=" mb-4">Hey, Enter your details to get your account register</p>
                    @if (session('error'))
                        <span>
                            <label class="error">{{ session('error') }}</label>
                        </span>
                    @endif
                    <form method="POST" action="{{ route('register.store') }}" id="register-password-form">
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
                                    <label for="userType" class="mb-2 required">User Type</label>
                                    <select name="userType" id="userType"
                                        class="form-control fs-8 @error('userType') is-invalid @enderror" required>
                                        <option value="">Select User Type</option>
                                        @foreach (config('constants.USER_TYPES') as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ old('userType') == $key ? 'selected' : '' }}>{{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('userType')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 px-md-2 olympiad-check" style="display:none;">
                                <div class="form-group mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input custom-check-olympiad"
                                            id="isOlympiadUser" name="isOlympiadUser" value="1">
                                        <label class="form-check-label" for="isOlympiadUser">Are you registering for MOM (Olympiad)?</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 px-md-2 access-code-field" style="display:none;">
                                <div class="form-group mb-4">
                                    <label for="accessCode" class="form-label required">Access Code</label>
                                    <input type="text" name="access_code" id="accessCode"
                                        class="form-control fs-8 @error('accessCode') is-invalid @enderror"
                                        placeholder="Enter Access Code">
                                    @error('access_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 px-md-2 user-come-for" style="display: none;">
                                <div class="form-group mb-4 ">
                                    <label for="userComeFor" class="mb-2 required">You Are Here For</label>
                                    <select name="userComeFor" id="userComeFor"
                                        class="form-control fs-8 @error('userComeFor') is-invalid @enderror" required>
                                        <option value="">--Select--</option>
                                        @foreach (config('constants.USER_COME_FOR') as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('userComeFor')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 px-md-2 school-field" style="display: none;">
                                <div class="form-group mb-4">
                                    <label for="schoolName" class="mb-2">School Name</label>
                                    <div id="schools-data" data-schools='@json($schools)'
                                        style="display: none;"></div>

                                    <input type="text" name="schoolNameSearch" id="schoolNameSearch"
                                        class="form-control fs-8 @error('schoolName') is-invalid @enderror"
                                        placeholder="Search for a school" autocomplete="off">

                                    <select name="schoolName" id="schoolName" style="display: none;">
                                        <option value="" selected>Select School</option>
                                        @foreach ($schools as $school)
                                            <option value="{{ $school->id }}"
                                                data-classes="{{ json_encode($school->classes ?? []) }}">
                                                {{ $school->name }}</option>
                                        @endforeach
                                    </select>

                                    <div id="schoolSearchResults" class="list-group mt-2"
                                        style="display: none; position: absolute; z-index:30">
                                    </div>

                                    @error('schoolName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div id="classSelectWrapper2" class="col-md-6 px-md-2 form-group mb-3"
                                style="display: none;">
                                <label for="className" class="mb-2 required">Class</label>
                                <select name="className" id="className2" required
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

                            <div class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                                <label for="captcha" class="col-md-4 mb-2 control-label required">Captcha</label>
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

                        </div>
                        <div class="loginbtm mt-2">
                            <div class="cstmCheckbox">
                                <input type="checkbox" id="termsCheck" checked name="terms_accepted">
                                <label for="termsCheck">By Clicking you are indicating that you have read and agreed to
                                    the
                                    <a href="{{ route('terms.condition') }}">terms of use</a> & <a
                                        href="{{ route('privacy.policy') }}">Privacy
                                        policy</a></label>
                                @error('terms_accepted')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="text-center my-2 mt-4">
                            <button type="submit" class="btn btn-primary-gradient fs-7 rounded-2 w-75">Register</button>
                        </div>

                        <strong class="signupTxt pb-0">Already have an account?
                            <a href="{{ route('login') }}">Login</a>
                        </strong>

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
            document.addEventListener('DOMContentLoaded', function() {
                const olympiadCheckbox = document.getElementById('isOlympiadUser');
                const accessCodeField = document.querySelector('.access-code-field');

                // Sections to hide when Olympiad checkbox is checked
                const hideSections = [
                    document.getElementById('password').closest('.col-md-6'), // Password
                    document.getElementById('password_confirmation').closest('.col-md-6'), // Confirm password
                    document.querySelector('.form-group.has-error, .form-group[for="captcha"]') || document
                    .querySelector('[name="captcha"]').closest('.form-group'), // Captcha
                ];

                olympiadCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Show only Access Code
                        accessCodeField.style.display = 'block';
                        hideSections.forEach(sec => sec ? sec.style.display = 'none' : null);
                    } else {
                        // Restore everything
                        accessCodeField.style.display = 'none';
                        hideSections.forEach(sec => sec ? sec.style.display = '' : null);
                    }
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const userTypeSelect = document.getElementById('userType');
                const schoolField = document.querySelector('.school-field');
                const classSelectWrapper = document.getElementById('classSelectWrapper2');
                const olympiadCheck = document.querySelector('.olympiad-check');
                const olympiadCheckbox = document.getElementById('isOlympiadUser');
                const accessCodeField = document.querySelector('.access-code-field');

                const schoolOnlyTypes = ['school_admin', 'school_teacher'];
                const studentType = 'school_student';

                function resetOlympiad() {
                    olympiadCheckbox.checked = false;
                    accessCodeField.style.display = 'none';
                }

                userTypeSelect.addEventListener('change', function() {
                    const selectedUserType = this.value;

                    // Reset Olympiad logic
                    resetOlympiad();
                    olympiadCheck.style.display = 'none';
                    schoolField.style.display = 'none';
                    classSelectWrapper.style.display = 'none';

                    if (selectedUserType === studentType) {
                        // Show school + class + olympiad checkbox
                        schoolField.style.display = 'block';
                        classSelectWrapper.style.display = 'block';
                        olympiadCheck.style.display = 'block';
                    } else if (schoolOnlyTypes.includes(selectedUserType)) {
                        schoolField.style.display = 'block';
                    }
                });

                // Olympiad checkbox toggle
                olympiadCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        // Hide school + class
                        schoolField.style.display = 'none';
                        classSelectWrapper.style.display = 'none';
                        // Show access code
                        accessCodeField.style.display = 'block';
                    } else {
                        // Restore school + class
                        schoolField.style.display = 'block';
                        classSelectWrapper.style.display = 'block';
                        accessCodeField.style.display = 'none';
                    }
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                const userTypeSelect = document.getElementById('userType');
                const userComeFor = document.getElementById('userComeFor');
                const schoolField = document.querySelector('.school-field');
                const userComeForField = document.querySelector('.user-come-for');
                const classSelectWrapper = document.getElementById('classSelectWrapper2');
                const schoolNameSearch = document.getElementById('schoolNameSearch');
                const schoolNameSelect = document.getElementById('schoolName');
                const schoolNameLabel = document.querySelector('label[for="schoolName"]');

                const schoolOnlyTypes = ['school_admin', 'school_teacher'];
                const studentType = 'school_student';

                userTypeSelect.addEventListener('change', function() {
                    const selectedUserType = this.value;
                    if (selectedUserType == 'other') {
                        userComeForField.style.display = 'block';
                        userComeForField.setAttribute('required', 'required');
                        userComeFor.addEventListener('change', function() {
                            const selectedComeFrom = this.value;
                            if (selectedComeFrom == 'for_academic_content') {
                                classSelectWrapper.style.display = 'block';
                                classSelectWrapper.setAttribute('required', 'required');
                            } else if (selectedComeFrom == 'both') {
                                classSelectWrapper.style.display = 'block';
                                classSelectWrapper.setAttribute('required', 'required');
                            } else {
                                classSelectWrapper.style.display = 'none';
                            }
                        })
                    }
                    // Reset all fields
                    schoolField.style.display = 'none';
                    classSelectWrapper.style.display = 'none';
                    schoolNameSelect.value = '';
                    document.getElementById('className2').value = '';

                    // Remove required attributes by default
                    schoolNameSearch.removeAttribute('required');
                    schoolNameLabel.classList.remove('required');

                    if (schoolOnlyTypes.includes(selectedUserType)) {
                        // Show only school field for admins/teachers
                        schoolField.style.display = 'block';
                        userComeForField.style.display = 'none';

                    } else if (selectedUserType === studentType) {
                        // Show both fields for students and make school search required
                        schoolField.style.display = 'block';
                        userComeForField.style.display = 'none';

                        classSelectWrapper.style.display = 'block';
                        schoolNameSearch.setAttribute('required', 'required');
                        schoolNameLabel.classList.add('required');
                    }
                    // For 'other' type, nothing is shown (default state)
                });

                // Initialize on page load
                if (schoolOnlyTypes.includes(userTypeSelect.value)) {
                    schoolField.style.display = 'block';
                    userComeForField.style.display = 'none';

                } else if (userTypeSelect.value === studentType) {
                    schoolField.style.display = 'block';
                    classSelectWrapper.style.display = 'block';
                    userComeForField.style.display = 'none';
                    schoolNameSearch.setAttribute('required', 'required');
                    schoolNameLabel.classList.add('required');
                }
            });
        </script>

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
