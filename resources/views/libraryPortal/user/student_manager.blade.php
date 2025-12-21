@extends('libraryPortal.layouts.master')
@section('content')
    @php
        $flag = 0;
        $heading = 'Add Student';
        if (isset($data) && !empty($data)) {
            $flag = 1;
            $heading = 'View/Edit Student Details';
        }
    @endphp
    <style>
        @keyframes bgBlink {
            0% {
                background-color: transparent;
                border-color: currentColor;
            }

            50% {
                background-color: rgba(25, 135, 84, 0.15);
                border-color: currentColor;
            }

            100% {
                background-color: transparent;
                border-color: currentColor;
            }
        }

        @keyframes bgBlinkDanger {
            0% {
                background-color: transparent;
                border-color: currentColor;
            }

            50% {
                background-color: rgba(220, 53, 69, 0.15);
                border-color: currentColor;
            }

            100% {
                background-color: transparent;
                border-color: currentColor;
            }
        }

        @keyframes bgBlinkPrimary {
            0% {
                background-color: transparent;
                border-color: currentColor;
            }

            50% {
                background-color: rgba(26, 185, 233, 0.349);
                border-color: currentColor;
            }

            100% {
                background-color: transparent;
                border-color: currentColor;
            }
        }

        .blink-success {
            animation: bgBlink 1.8s ease-in-out infinite;
        }

        .blink-danger {
            animation: bgBlinkDanger 1.8s ease-in-out infinite;
        }

        .blink-primary {
            animation: bgBlinkPrimary 1.8s ease-in-out infinite;
        }
    </style>


    @include('libraryPortal.layouts.flash-messages')

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="teacherTable">
                <div class="headerTbl">
                    <h6 class="m-0">Student Management</h6>

                    <div class="teacherrightTable">
                        <div class="d-flex align-items-center gap-2">

                            <input type="text" id="filterName" class="form-control" placeholder="Search by name"
                                style="font-size: 12px !important; width:140px;">

                            <input type="text" id="filterMobile" class="form-control" placeholder="Search by Mobile"
                                style="font-size: 12px !important; width:140px;">

                            <input type="text" id="filterAdmission" class="form-control"
                                placeholder="Search by Admission No." style="font-size: 12px !important; width:180px;">

                        </div>
                        <a href="{{ route('student.add') }}" class="btn btn-primary-gradient rounded-1 addBtn">Add
                            Student</a>
                    </div>
                </div>
                <div class="px-3 py-2">
                    <div class="table-responsive tbleDiv">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Admission No.</th>
                                    <th>Name</th>
                                    <th>Admission Date</th>
                                    <th>Mobile No.</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
                                @foreach ($students as $student)
                                    @php
                                        $noSeatAssigned = App\Models\SeatAssignment::where(
                                            'user_id',
                                            $student->user_id,
                                        )->first();
                                    @endphp
                                    <tr id="studentRow_{{ $student->id }}">
                                        <td>{{ $student->admission_no ?? null }}</td>
                                        <td>
                                            <span class="nameTbl student-name"> <img
                                                    src="{{ $student->image ? Storage::url('uploads/user/profile_image/' . $student->image) : asset('frontend/images/default-image.jpg') }}"
                                                    alt="">{{ $student->name }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($student->joining_date ?? null)->format('d/m/Y') }}
                                        </td>
                                        <td>{{ $student->user->mobile_no }}</td>
                                        <td>
                                            <span class="{{ $student->status == 1 ? 'activeTxt' : 'deactiveTxt' }}">
                                                {{ $student->status == 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">

                                                <!-- Edit -->
                                                <a href="{{ route('student.edit', $student->id) }}"
                                                    class="btn p-0 bg-transparent border-0 text-primary"
                                                    title="Edit Student">
                                                    <i class="bi bi-pencil-square fs-5"></i>
                                                </a>

                                                <!-- Delete -->
                                                <button type="button"
                                                    class="btn p-0 bg-transparent border-0 text-danger deleteStudentBtn"
                                                    data-id="{{ $student->user_id }}" data-name="{{ $student->name }}"
                                                    data-bs-toggle="modal" data-bs-target="#statusMdl">
                                                    <i class="bi bi-trash fs-5"></i>
                                                </button>

                                                @if ($student->is_new == 1 && !$noSeatAssigned)
                                                    <button type="button"
                                                        class="btn btn-outline-success btn-sm px-3 blink-success collectFeesBtn"
                                                        data-bs-toggle="modal" data-bs-target="#collectFeesModal"
                                                        data-student-id="{{ $student->user_id }}">
                                                        Collect Fees
                                                    </button>
                                                @elseif ($student->is_renew == 1)
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm px-3 blink-danger"
                                                        data-id="{{ $student->user_id }}"
                                                        data-name="{{ $student->name }}">
                                                        Renew Fees
                                                    </button>
                                                @endif

                                                @if ($student->is_new == 0 && $student->is_renew == 0 && !$noSeatAssigned)
                                                    <a type="button" href="{{ route('seat.index') }}"
                                                        class="btn btn-outline-primary btn-sm px-3 blink-primary collectFeesBtn">
                                                        Assign Seat
                                                    </a>
                                                @endif

                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="statusMdl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-0 text-center">
                    <lottie-player src="{{ asset('frontend/images/study-idea.json') }}" loop autoplay
                        style="width:130px;height:130px;margin:auto;">
                    </lottie-player>
                    <h6 class="fw-semibold">Are you sure?</h6>
                    <p id="statusText"></p>
                    <input type="hidden" id="deleteStudentId">
                    <a href="#" id="confirmDeleteBtn" class="btn btn-primary-gradient rounded-1">Yes</a>
                    <div>
                        <button type="button" class="btn btnNo" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- <div class="modal fade" id="studentInactive">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 align-items-baseline">
                    <div>
                        <h6 class="modal-title fw-semibold">Inactive Student</h6>
                        <p>Enter inactive date for changing the status of student from active to Inactive.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="">
                        <div class="formPanel">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group bginput mb-3">
                                        <label>Enter Date</label>
                                        <input type="text" class="form-control dateBirth" value="Select date">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn btn-primary-gradient rounded-1">Submit</button>
                                <div>
                                    <button type="button" class="btn btnNo">Back</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="modal fade" id="collectFeesModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title fw-semibold">Collect Fees</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('collect.fees.save') }}" method="POST" class="row g-3">
                    @csrf

                    <div class="modal-body">

                        <input type="hidden" name="id" id="student_id_field">
                        <input type="hidden" name="isFromStudentManagment" value="1" id="student_id_field">

                        <div class="row formPanel">

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Student <b>*</b></label>
                                    <select name="student_id" id="student_select" class="form-select">
                                        <option value="">Select student</option>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->user_id }}">{{ $student->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Start Date -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label>Start Date <b>*</b></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control">
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label>End Date <b>*</b></label>
                                    <input type="date" name="end_date" id="end_date" class="form-control">
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Amount <b>*</b></label>
                                    <input type="number" name="amount" class="form-control"
                                        placeholder="Enter amount">
                                </div>
                            </div>

                            <!-- Payment Mode -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label>Payment Mode <b>*</b></label>
                                    <select name="payment_mode" class="form-select">
                                        <option value="">Select</option>
                                        <option value="cash">Cash</option>
                                        <option value="upi">UPI</option>
                                        <option value="card">Card</option>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btnNo" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-gradient rounded-1">
                            Submit
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <script>
        document.addEventListener("click", function(e) {
            if (e.target.closest(".deleteStudentBtn")) {
                let name = e.target.closest(".deleteStudentBtn").dataset.name;
                document.getElementById("statusText").textContent = "Delete " + name + "?";

                // Open modal in Bootstrap 5
                var myModal = new bootstrap.Modal(document.getElementById("statusMdl"));
                myModal.show();
            }
        });
    </script>

    <script>
        document.addEventListener('click', function(e) {
            if (e.target.closest('.deleteStudentBtn')) {

                let btn = e.target.closest('.deleteStudentBtn');
                let id = btn.getAttribute('data-id');
                let name = btn.getAttribute('data-name');

                document.getElementById('statusText').innerText =
                    'Do you really want to delete "' + name + '"?';

                document.getElementById('confirmDeleteBtn').href =
                    "{{ route('student.delete', ':id') }}".replace(':id', id);
            }
        });
    </script>

    <script>
        document.addEventListener("keyup", function() {
            let nameFilter = document.getElementById("filterName").value.toLowerCase();
            let mobileFilter = document.getElementById("filterMobile").value.toLowerCase();
            let admissionFilter = document.getElementById("filterAdmission").value.toLowerCase();

            let rows = document.querySelectorAll("#studentTableBody tr");

            rows.forEach(row => {
                let name = row.querySelector(".student-name").innerText.toLowerCase();
                let mobile = row.children[3].innerText.toLowerCase();
                let admission = row.children[0].innerText.toLowerCase();

                let match =
                    name.includes(nameFilter) &&
                    mobile.includes(mobileFilter) &&
                    admission.includes(admissionFilter);

                row.style.display = match ? "" : "none";
            });
        });
    </script>

    <script>
        document.addEventListener('click', function(e) {
            let btn = e.target.closest('.collectFeesBtn');
            if (!btn) return;
            let studentId = btn.dataset.studentId;
            document.getElementById('student_id_field').value = studentId;
            document.getElementById('student_select').value = studentId;
            document.getElementById('start_date').value =
                "{{ \Carbon\Carbon::now()->toDateString() }}";
            document.getElementById('end_date').value =
                "{{ \Carbon\Carbon::now()->addMonth()->toDateString() }}";
        });
    </script>
@endsection
