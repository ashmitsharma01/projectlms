<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Project LMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="{{ asset('frontend/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.0/themes/base/jquery-ui.css">
    <link href="{{ asset('admin/vendor/quill/quill.snow.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- crooper --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">

    <!-- Include in your layout file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="{{ asset('admin/vendor/sweetalert2-7.0.0/sweetalert2.css') }}" rel="stylesheet">


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link href="{{ asset('frontend/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/css/custom.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script type="text/javascript" src="{{ asset('frontend/js/init.js') }}"></script>
    <script>
        var base_url = "{{ url('/') . '/' }}";
        var csrf_token = "{{ csrf_token() }}";
    </script>
    @livewireStyles
</head>

<body style="background-color: #F9F9F9;">

    @include('libraryPortal.layouts.header')

    @include('libraryPortal.layouts.sidebar')


    <main id="main" class="main">
        <div class="dashboardMain">
            <div class="alertsSec cardBox mb-3 d-lg-none">
                <h3 class="fs-6 fw-regular d-flex align-items-center gap-1 mb-0"><img
                        src="{{ asset('frontend/images/alert.svg') }}" alt="" width="15">Alerts</h3>
                <div class="alertList">
                    <a href="javascript:void(0);">This is the useful tool</a>
                    <a href="javascript:void(0);">This is the useful tool</a>
                </div>
            </div>
            {{-- @include('admin.layouts.flash-messages') --}}
            {{-- @include('libraryPortal.layouts.alerts_sec_mobile') --}}
            {{-- @yield('breadcrumb') --}}
            @yield('content')
        </div>
        {{-- <div class="footerBottom">
            <ul class="footerLeft">
                <li><strong>My Library</strong>
                </li>
                <li><img src="{{ asset('frontend/images/call-icon.svg') }}" alt="" width="13">
                    1478965412</li>
                <li><img src="{{ asset('frontend/images/mail-icon.svg') }}" alt="" width="18">
                    adfsfd@gmail.com </li>
                <li><img src="{{ asset('frontend/images/location-icon.svg') }}" alt="" width="12">Jaipur
                </li>

            </ul>
            <ul class="footerright">
                <li><a target="_blank" href={{ $links['user_facebook'] ?? '' }}><img
                            src="{{ asset('frontend/images/facebook.svg') }}" width="25" height="18"></a>
                </li>
                <li><a target="_blank" href={{ $links['user_instagram'] ?? '' }}><img
                            src="{{ asset('frontend/images/instagram.svg') }}" width="25" height="18"></a></li>
                <li><a target="_blank" href={{ $links['user_twitter'] ?? '' }}><img
                            src="{{ asset('frontend/images/twitter.svg') }}" width="25" height="18"></a>
                </li>
                <li><a target="_blank" href={{ $links['user_linkedin'] ?? '' }}><img
                            src="{{ asset('frontend/images/linkedin.svg') }}" width="25" height="18"></a></li>
                <li><a target="_blank" href={{ $links['user_youtube'] ?? '' }}><img
                            src="{{ asset('frontend/images/youtube.svg') }}" width="25" height="18"></a>
                </li>
            </ul>
            </ul>
        </div> --}}
    </main>
    <div class="modal fade SchoolProfile" id="profile">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content border-0 rounded-1">
                <div class="modal-body p-0">
                    <div class="profileMain">
                        <div class="profileSidebar ">
                            <div class="profileUpload">
                                <figure class="position-relative m-0">
                                    <img id="profileImage" src="{{ asset('frontend/images/default-image.jpg') }}"
                                        alt="Profile Image">
                                    <label for="profileHeader" class="contentprf">
                                        <div class="text-white">
                                            <img src="{{ asset('frontend/images/edit-upload.svg') }}"
                                                class="d-block mx-auto mb-3" alt="" width="14">
                                            Click to change profile <br> Image
                                        </div>
                                        <input type="file" name="image" id="profileHeader" class="d-none"
                                            accept="image/*">
                                    </label>
                                </figure>
                            </div>
                            <ul class="nav nav-pills flex-column mb-3 profileTabs">
                                <li class="nav-item">
                                    <button class="nav-link active" data-bs-toggle="pill"
                                        data-bs-target="#schoolDetails" type="button"><img
                                            src="{{ asset('frontend/images/school-details-icon.svg') }}" alt=""
                                            width="14" class="me-3">School Details</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#addressDetails"
                                        type="button"><img
                                            src="{{ asset('frontend/images/address-details-icon.svg') }}"
                                            alt="" width="12" class="me-3">Address Details</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#passwordChange"
                                        type="button"><img
                                            src="{{ asset('frontend/images/password-change-icon.svg') }}"
                                            alt="" width="16" class="me-3">Password Change</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link text-danger" type="button" data-bs-target="#logOut"
                                        data-bs-toggle="modal"><img
                                            src="{{ asset('frontend/images/logout-icon.svg') }}" alt=""
                                            width="14" class="me-3">Log
                                        out</button>
                                </li>
                            </ul>
                        </div>
                        <div class="profileRight">
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>

                            <div class="tab-content">

                                <!-- SCHOOL DETAILS -->
                                <div class="tab-pane fade show active" id="schoolDetails">
                                    <h1 class="modal-title fs-4 fw-semibold">School Details</h1>

                                    <div class="formPanel">
                                        <form>
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>School Name</label>
                                                        <input type="text" class="form-control readonly"
                                                            value="Sample School" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Parent School Name</label>
                                                        <input type="text" class="form-control readonly"
                                                            value="Parent School" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Email</label>
                                                        <input type="text" class="form-control readonly"
                                                            value="sample@email.com" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Website</label>
                                                        <input type="text" class="form-control"
                                                            value="www.samplewebsite.com">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Decision Maker Name</label>
                                                        <input type="text" class="form-control" value="John Doe">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Decision Maker Mobile No</label>
                                                        <input type="text" class="form-control"
                                                            value="9876543210">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Decision Maker Role</label>
                                                        <input type="text" class="form-control readonly"
                                                            value="Principal" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Strength</label>
                                                        <input type="text" class="form-control" value="1200">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>School Affiliation Number / PAN Number</label>
                                                        <input type="text" class="form-control readonly"
                                                            value="AFF12345" readonly>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>School Registration Number</label>
                                                        <input type="text" class="form-control readonly"
                                                            value="REG67890" readonly>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-12 text-center my-2 mt-4">
                                                <button type="button"
                                                    class="btn btn-primary-gradient fs-7 rounded-2 w-75">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- ADDRESS DETAILS -->
                                <div class="tab-pane fade" id="addressDetails">
                                    <h1 class="modal-title fs-4 fw-semibold">Address Details</h1>

                                    <div class="formPanel">
                                        <form>
                                            <div class="row">

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Pin Code</label>
                                                        <input type="text" class="form-control" value="110001">
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>State</label>
                                                        <select class="form-select">
                                                            <option>Select</option>
                                                            <option selected>Delhi</option>
                                                            <option>Maharashtra</option>
                                                            <option>Rajasthan</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>District</label>
                                                        <select class="form-select">
                                                            <option>Select</option>
                                                            <option selected>New Delhi</option>
                                                            <option>Mumbai</option>
                                                            <option>Jaipur</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label>Address</label>
                                                        <input type="text" class="form-control"
                                                            value="123 Sample Street">
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="col-md-12 text-center my-2 mt-4">
                                                <button type="button"
                                                    class="btn btn-primary-gradient fs-7 rounded-2 w-75">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- CHANGE PASSWORD -->
                                <div class="tab-pane fade" id="passwordChange">
                                    <h1 class="modal-title fs-4 fw-semibold">Change Password</h1>

                                    <div class="formPanel">
                                        <form>

                                            <div class="form-group mb-3">
                                                <label>Current Password</label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control"
                                                        placeholder="Enter Current Password">
                                                    <span class="eyeInput eye_icon"><i
                                                            class="bi bi-eye-slash"></i></span>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>Enter New Password</label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control"
                                                        placeholder="Enter New Password">
                                                    <span class="eyeInput eye_icon"><i
                                                            class="bi bi-eye-slash"></i></span>
                                                </div>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label>Confirm New Password</label>
                                                <div class="position-relative">
                                                    <input type="password" class="form-control"
                                                        placeholder="Confirm New Password">
                                                    <span class="eyeInput eye_icon"><i
                                                            class="bi bi-eye-slash"></i></span>
                                                </div>
                                            </div>

                                            <div class="col-md-12 text-center my-2 mt-4">
                                                <button type="button"
                                                    class="btn btn-primary-gradient fs-7 rounded-2 w-75">Update</button>
                                            </div>

                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="imageCropModal" tabindex="-1" aria-labelledby="imageCropModalLabel"
        aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageCropModalLabel">Crop Profile Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="img-container">
                        <img id="imageToCrop" src="" alt="Profile Image to Crop" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="cropImageBtn">Crop & Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="logOut">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header justify-content-end align-items-start border-0">
                    <a href="javascript:void(0);" onclick="location.reload();"><button type="button"
                            class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></a>
                </div>
                <div class="modal-body pt-0">
                    <form>
                        <lottie-player src="{{ asset('frontend/images/logout.json') }}" background="transparent"
                            speed="1" style="width: 140px; height: 140px;margin: auto;" loop=""
                            autoplay=""></lottie-player>
                        <h6 class="text-center fw-semibold">Logout Account</h6>
                        <p class="text-center fs-8">Are you sure you want to logout? Once you logout you need to
                            login
                            again.
                        </p>
                        <div class="d-flex align-items-center justify-content-end flex-column mt-4">
                            <a href="{{ route('logout') }}"class="btn btn-primary-gradient fs-7 rounded-2 w-50 mb-2">Yes
                            </a>
                            <a href="javascript:void(0);" onclick="location.reload();">
                                <button type="button" class="btn backbtn fw-regular my-2">Back</button>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- added for the library management tool --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[type="date"]').forEach(function(input) {
                input.addEventListener('click', function() {
                    if (this.showPicker) {
                        this.showPicker();
                    }
                });
            });
        });
    </script>

    <script src="{{ asset('frontend/js/script.js') }}"></script>
    <script src="{{ asset('admin/vendor/sweetalert2-7.0.0/sweetalert2.min.js') }}"></script>
    {{-- cropper --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.js"></script>
    <script src="{{ asset('admin/vendor/quill/quill.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(function() {
            $("#datepicker").datepicker();
            $("#datepicker1").datepicker();
        });
    </script>
    <script>
        $(function() {
            $('#multiSelect').select2({
                placeholder: "--Select--",
                allowClear: true
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Target all modals with the 'coursePrv' class
            const modals = document.querySelectorAll('.coursePrv');

            modals.forEach(modal => {
                modal.addEventListener('hidden.bs.modal', function() {
                    // Pause all videos inside the modal
                    const videos = modal.querySelectorAll('video');
                    videos.forEach(video => {
                        video.pause();
                        video.currentTime = 0; // Optional: Reset video to start
                    });
                });
            });
        });
    </script>
    {{-- @livewireScripts --}}

    {{-- <script>
        let cropper;
        let profileImageInput = document.getElementById('profileHeader');
        let profileImagePreview = document.getElementById('profileImage');

        // When a new image is selected
        profileImageInput.addEventListener('change', function(e) {
            const files = e.target.files;

            if (files && files.length > 0) {
                const file = files[0];

                if (!file.type.match('image.*')) {
                    alert('Please select an image file (jpeg, png, jpg, gif)');
                    return;
                }

                const imageURL = URL.createObjectURL(file);
                const imageToCrop = document.getElementById('imageToCrop');
                imageToCrop.src = imageURL;

                // Destroy previous cropper instance if it exists
                if (cropper) {
                    cropper.destroy();
                }

                // Initialize the modal without the event listener that causes recursion
                const cropModal = new bootstrap.Modal(document.getElementById('imageCropModal'));
                cropModal.show();

                // Initialize cropper after a small delay to ensure modal is fully shown
                setTimeout(() => {
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 0.8,
                        responsive: true
                    });
                }, 200);
            }
        });

        // Handle the crop button click
        document.getElementById('cropImageBtn').addEventListener('click', function() {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 500,
                height: 500,
                minWidth: 256,
                minHeight: 256,
                fillColor: '#fff',
                imageSmoothingQuality: 'high',
            });

            if (canvas) {
                canvas.toBlob(function(blob) {
                    const file = new File([blob], 'profile-image.jpg', {
                        type: 'image/jpeg'
                    });
                    const formData = new FormData();
                    formData.append('profile_image', file);

                    profileImagePreview.src = URL.createObjectURL(blob);

                    // Hide the crop modal properly
                    bootstrap.Modal.getInstance(document.getElementById('imageCropModal')).hide();

                    uploadProfileImage(formData);
                }, 'image/jpeg', 0.9);
            }
        });

        // Clean up when modal is hidden
        document.getElementById('imageCropModal').addEventListener('hidden.bs.modal', function() {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        function uploadProfileImage(formData) {
            fetch('{{ route('sp.upload.profile.image') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const timestamp = new Date().getTime();
                        profileImagePreview.src = data.filePath + '?t=' + timestamp;
                        alert('Profile image updated successfully!');
                    } else {
                        throw new Error(data.message || 'Failed to upload profile image');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error uploading image: ' + error.message);
                });
        }
    </script>


    <script>
        // Disable right-click
        document.addEventListener("contextmenu", function(event) {
            event.preventDefault();
        });
    </script>>
    <script>
        $(function() {
            $("#datepicker").datepicker();
        });
        $(".js-select2").select2({
            closeOnSelect: false,
            placeholder: "Select",
            allowClear: false,
            tags: true
        });


        $('.toggleBtn').click(function() {
            $('body').toggleClass("open-sidebar");
        });


        $('.alertList').slick({
            autoplay: true,
            slidesToShow: 1,
            arrows: false,
            dots: false,
            autoplaySpeed: 0,
            speed: 30000,
            cssEase: 'linear',
            variableWidth: true,
            pauseOnHover: true
        });

        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <script>
        $(document).ready(function() {
            $('#admin-state-select').on('change', function() {
                var stateId = $(this).val();
                $('#admin-city-select').html('<option value="">Select</option>');
                if (stateId) {
                    var url = "{{ route('sp.getCities', ':state') }}".replace(':state', stateId);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(data) {
                            if (data && Object.keys(data).length > 0) {
                                $.each(data, function(id, name) {
                                    $('#admin-city-select').append('<option value="' +
                                        id +
                                        '">' + name + '</option>');
                                });
                            } else {
                                $('#admin-city-select').html(
                                    '<option value="">No cities available</option>');
                            }
                        },
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#teacher-state-select').on('change', function() {
                var stateId = $(this).val();
                $('#teacher-city-select').html('<option value="">Select</option>');
                if (stateId) {
                    var url = "{{ route('sp.getCities', ':state') }}".replace(':state', stateId);
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(data) {
                            if (data && Object.keys(data).length > 0) {
                                $.each(data, function(id, name) {
                                    $('#teacher-city-select').append('<option value="' +
                                        id +
                                        '">' + name + '</option>');
                                });
                            } else {
                                $('#teacher-city-select').html(
                                    '<option value="">No cities available</option>');
                            }
                        },
                    });
                }
            });
        });


        const dateInputs = document.querySelectorAll('.dateInput');
        // Loop through all date input elements and add the event listener
        dateInputs.forEach(function(input) {
            input.addEventListener('click', function() {
                this.showPicker(); // Show the date picker when the input is clicked
            });
        });
    </script> --}}
</body>

</html>
