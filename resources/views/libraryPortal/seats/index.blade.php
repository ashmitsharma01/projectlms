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
        @include('libraryPortal.layouts.flash-messages')

    {{-- <div class="cardBox teacherMain py-md-3 mb-3">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <input type="text" id="filterName" class="form-control" style="font-size: 12px !important"
                        placeholder="Search by name">
                </div>
            </div>

            <!-- Mobile Filter -->
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <input type="text" id="filterMobile" class="form-control" style="font-size: 12px !important"
                        placeholder="Search by Mobile No.">
                </div>
            </div>

            <!-- Admission Filter -->
            <div class="col-md-2">
                <div class="form-group mb-2">
                    <input type="text" id="filterAdmission" class="form-control" style="font-size: 12px !important"
                        placeholder="Search by Admission No.">
                </div>
            </div>

        </div>
    </div> --}}


    <div class="row" >
        <div class="col-md-12 mb-3">
            <div class="teacherTable">  
                <div class="headerTbl">
                    <h6 class="m-0">Seats Management</h6>

                    <div class="teacherrightTable">
                        <div class="d-flex align-items-center gap-2">

                            <input type="text" id="filterName" class="form-control" placeholder="Search by name"
                                style="font-size: 12px !important; width:140px;">

                            <input type="text" id="filterMobile" class="form-control" placeholder="Search by Mobile"
                                style="font-size: 12px !important; width:140px;">

                            <input type="text" id="filterAdmission" class="form-control"
                                placeholder="Search by Admission No." style="font-size: 12px !important; width:180px;">

                        </div>
                        <a href="{{ route('seat.add') }}" class="btn btn-primary-gradient rounded-1 addBtn">Assign
                            Seat</a>
                    </div>
                </div>
                <div class="px-3 py-2">
                    <div class="table-responsive tbleDiv">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Admission No.</th>
                                    <th>Seat No.</th>
                                    <th>Name</th>
                                    <th>Mobile No.</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            {{-- <tbody id="studentTableBody">
                                @foreach ($students as $student)
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
                                            <div class="d-flex gap-2">
                                                <!-- Edit -->
                                                <a href="{{ route('student.edit', $student->id) }}"
                                                    class="btn p-0 bg-transparent border-0 text-primary editBtn"
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
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody> --}}
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


    <div class="modal fade" id="studentInactive">
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
@endsection
