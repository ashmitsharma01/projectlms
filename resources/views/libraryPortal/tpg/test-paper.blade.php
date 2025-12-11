@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <div class="cardBox teacherMain pt-md-4 pb-0  mb-3">
        <div class="row ">
            <div class="col-md-8 mb-3 ">
                <div class="teacherLeft">
                    <h5 class="fw-semibold">Test Paper Generator</h5>
                    <p>Create and manage test papers effortlessly with automated question selection based on categories and
                        difficulty levels.</p>
                    <a href="{{ route('sp.test-papers.add') }}" class="btn btn-primary-gradient rounded-1 ">Add Test
                        Paper</a>
                </div>
            </div>
            <div class="col-md-4 mt-auto">
                <div class="teacherRighr text-end">
                    <img src="{{ asset('frontend/images/test-paper-generator.svg') }}" alt="" class=" "
                        width="220">
                </div>
            </div>
        </div>
    </div>
    <div class="teacherTable mb-3">
        <div class="headerTbl">
            <h6 class="m-0">Test Paper List</h6>
            <div class="teacherrightTable">
                <div class="tableSearch">
                    <input type="text" id="search-input" class="form-control" placeholder="Search by Test ">
                </div>
                <button class="bg-transparent border-0 p-0" type="button" data-bs-target="#searchBy"
                    data-bs-toggle="offcanvas">
                    <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Filter">
                        <img src="{{ asset('frontend/images/filter-icon.svg') }}" alt=""></span>
                </button>
            </div>
        </div>
        <div class="px-3 py-2">
            <div class="table-responsive tbleDiv ">
                <table class="table mb-0" id="question-table">
                    <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>Title</th>
                            <th>Paper Type</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Test Term</th>
                            <th>Status</th>
                            <th>Assign</th>
                            <th>Add Question</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($testPapers as $data)
                            <tr data-name="{{ $data->title }}">

                                <td>{{ $testPapers->currentPage() * $testPapers->perPage() - $testPapers->perPage() + $loop->iteration . '.' }}

                                <td>{{ $data->title }}</td>
                                <td>{{ config('constants.PAPER_TYPE')[$data->paper_type] ?? 'N/A' }}</td>
                                <td>{{ $data->Class->name }}</td>
                                <td>{{ $data->Subject->name }}</td>
                                <td>
                                    {{ $data->test_term }}
                                </td>
                                <td>
                                    @if ($data->is_active == 1)
                                        <span class="activeStatus">Active</span>
                                    @elseif ($data->is_active == 2)
                                        <span class="deactiveStatus">Expired</span>
                                    @else
                                        <span class="deactiveStatus">Inactive</span>
                                    @endif
                                </td>
                                @php
                                    $role = getUserRoles();
                                    $parentId = Auth::id();

                                    if ($role === 'school_teacher') {
                                        $parentId = Auth::user()->userAdditionalDetail->school_id;
                                    }

                                    $classId = $data->class_id;
                                    $students = App\Models\User::where('status', 1)
                                        ->whereHas('studentDetails', function ($query) use ($classId, $parentId) {
                                            $query->where('parent_id', $parentId)->where('class', $classId);
                                        })
                                        ->count();
                                    $participantIds = $data->testParticipent->pluck('user_id')->toArray();
                                @endphp
                                @if ($data->paper_type === 'online')
                                    @if ($data->testParticipent->count() == $students && $data->testParticipent->count() != 0)
                                        <td>
                                            <button type="button" data-bs-target="#assignedTo" data-bs-toggle="modal"
                                                class="assignedtxt" data-class-id="{{ $data->class_id }}"
                                                data-start-id="{{ $data->indian_start_date_time }}"
                                                data-end-id="{{ $data->indian_end_date_time }}"
                                                data-test-id="{{ $data->id }}">
                                                <img src="{{ asset('frontend/images/assigned.svg') }}" width="18">
                                                Assigned
                                            </button>
                                        </td>
                                    @elseif ($data->testParticipent->count() != $students && $data->testParticipent->count() != 0)
                                        <td>
                                            <button class="unassignedtxt" data-bs-toggle="modal" id="partiallyAssigned"
                                                data-bs-target="#selectToAssign" data-class-id="{{ $data->class_id }}"
                                                data-start-id="{{ $data->indian_start_date_time }}"
                                                data-end-id="{{ $data->indian_end_date_time }}"
                                                data-test-id="{{ $data->id }}"
                                                data-participant-ids="{{ json_encode($participantIds) }}">
                                                <img src="{{ asset('frontend/images/unassigned.svg') }}" width="18">
                                                Partially Assigned
                                            </button>
                                        </td>
                                    @else
                                        <td>
                                            <button class="unassignedtxt" data-bs-toggle="modal" id="unassignedtxts"
                                                data-bs-target="#selectToAssign" data-class-id="{{ $data->class_id }}"
                                                data-start-id="{{ $data->indian_start_date_time }}"
                                                data-end-id="{{ $data->indian_end_date_time }}"
                                                data-test-id="{{ $data->id }}">
                                                <img src="{{ asset('frontend/images/unassigned.svg') }}" width="18">
                                                Unassigned
                                            </button>
                                        </td>
                                    @endif
                                @else
                                    {{-- <td>
                                        <a href="{{ route('tests.pdf', $data->id) }}"
                                            class="btn btn-sm btn-outline-primary" target="_blank">
                                            <img src="{{ asset('frontend/images/pdf.svg') }}" width="16"> Print PDF
                                        </a>
                                    </td> --}}
                                    {{-- <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#pdfModal" data-id="{{ $data->id }}" id="pdfmodelopenBtn">
                                            <img src="{{ asset('frontend/images/pdf.svg') }}" width="16"> Print PDF
                                        </button>
                                    </td> --}}
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary openPdfModal"
                                            data-id="{{ $data->id }}" data-bs-toggle="modal"
                                            data-bs-target="#pdfModal">

                                            <img src="{{ asset('frontend/images/pdf.svg') }}" width="16">
                                            Print PDF
                                        </button>
                                    </td>
                                @endif
                                @if (!in_array($data->id, $testParticipent))
                                    <td>
                                        <a href="{{ route('sp.test-paper.add-question', $data->id) }}"
                                            class="btn btn-success fs-9 py-2 px-1 rounded-1">Add Questoin</a>
                                    </td>
                                @else
                                    <td>
                                        <span class="text-danger fw-bold">
                                            <i class="bi bi-lock-fill"></i> Test Already Assigned
                                        </span>
                                    </td>
                                @endif
                                <td>
                                    <div class="dropdown">
                                        <button class="bg-transparent border-0 p-0" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <img src="{{ asset('frontend/images/action-icon.svg') }}" alt=""
                                                width="28">
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item"
                                                    href="{{ route('sp.test-paper-view', $data->id) }}">View</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('sp.test-papers.edit', $data->id) }}">Edit</a>
                                            </li>
                                            {{--  <li><a class="dropdown-item" href="#statusMdl" data-bs-toggle="modal">Delete</a>
                                            </li>  --}}
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <div class="modal fade" id="statusMdl">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body pt-0">
                                            <div class="text-center">
                                                <lottie-player src="{{ asset('frontend/images/study-idea.json') }}"
                                                    loop="" autoplay=""
                                                    style="width: 130px;height: 130px;margin: auto;"
                                                    background="transparent"></lottie-player>
                                                <h6 class="fw-semibold">Are you sure !</h6>
                                                <p>Do you want to Delete this Test Paper?</p>
                                                <form id="deleteForm"
                                                    action="{{ route('sp.test-paper.delete', $data->id) }}"
                                                    method="GET" style="display: inline;">
                                                    @csrf
                                                    <button type="submit"
                                                        class="btn btn-primary-gradient rounded-1">Yes</button>
                                                </form>
                                                <div>
                                                    <button type="button" class="btn btnNo">No</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-0 pb-0 mb-3">
                                            <h6 class="fw-semibold">Choose Copy Type</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <input type="hidden" id="pdfTestId">

                                            <div class="d-flex justify-content-center gap-3">
                                                <a href="{{ route('tests.pdf', ['paperId' => $data->id, 'user' => 'student']) }}"
                                                    class="btn btn-outline-primary" target="_blank">
                                                    Student Copy
                                                </a>
                                                <a href="{{ route('tests.pdf', ['paperId' => $data->id, 'user' => 'teacher']) }}"
                                                    class="btn btn-outline-success" target="_blank">
                                                    Teacher Copy
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        @endforeach
                    </tbody>
                </table>
            </div>


            <div class="customPagination mt-4">
                <div class="d-flex justify-content-right text-right">
                    {!! $testPapers->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="pdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 mb-3">
                    <h6 class="fw-semibold">Choose Copy Type</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">

                    <select id="fileType" class="form-select mb-3">
                        <option value="pdf">Download PDF</option>
                        {{-- <option value="word">Download Word (.docx)</option> --}}
                    </select>

                    <input type="hidden" id="selectedTestId">

                    <div class="d-flex justify-content-center gap-3">
                        <button class="btn btn-outline-primary" onclick="triggerDownload('student')">
                            Student Copy
                        </button>

                        <button class="btn btn-outline-success" onclick="triggerDownload('teacher')">
                            Teacher Copy
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- <script>
        function triggerDownload(type) {
            const fileType = document.getElementById('fileType').value;
            console.log({{ $data->id }})
            const url = `/school-portal/tests/download/{{ $data->id }}/${type}/${fileType}`;

            window.open(url, "_blank");
        }
    </script> --}}

    <script>
        // When any Print button is clicked
        document.addEventListener('click', function(e) {
            if (e.target.closest('.openPdfModal')) {
                const button = e.target.closest('.openPdfModal');
                const testId = button.getAttribute('data-id');
                document.getElementById('selectedTestId').value = testId;
            }
        });

        function triggerDownload(type) {
            const fileType = document.getElementById('fileType').value;
            const testId = document.getElementById('selectedTestId').value;

            const url = `/school-portal/tests/download/${testId}/${type}/${fileType}`;
            window.open(url, "_blank");
        }
    </script>


    <div class="offcanvas offcanvas-end " id="searchBy">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fs-6 fw-semibold">Search By</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="formPanel">
                <form action="{{ route('sp.test-papers') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                {!! Form::label('class', 'Select Class') !!}
                                {{ Form::select('class', $classes, request('class'), ['class' => 'form-select', 'placeholder' => 'Select']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                {!! Form::label('subject', 'Select Subject') !!}
                                {{ Form::select('subject', $subjects, request('subject'), ['class' => 'form-select', 'placeholder' => 'Select']) }}
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="d-flex align-items-center justify-content-end gap-4">
                <button type="submit" class="btn btn-primary-gradient rounded-1">Submit</button>
                <a href="{{ url()->current() }}" class="btn backbtn">Clear</a>
                <button type="button" class="btn backbtn" data-bs-dismiss="offcanvas">Back</button>
            </div>
        </div>
        </form>
    </div>

    <div class="modal fade assignedTo" id="assignedTo">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header align-items-start border-0">
                    <h2 class="modal-title fs-6 fw-semibold">Assigned to</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="assignList">
                        <span>Total Students: <b id="totalStudents">0</b></span>
                        {{--  <span>Start Date Time: <b id="start">0</b></span>
                        <span>End Date Time: <b id="end">0</b></span>  --}}
                        <div class="searchStudent mb-3">
                            <input class="form-control" placeholder="Search">
                        </div>
                        <div class="table-responsive tbleDiv">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="ps-0">Name</th>
                                        <th>Admission No.</th>
                                    </tr>
                                </thead>
                                <tbody id="participantList">
                                    <tr>
                                        <td colspan="2" class="text-center">No students assigned.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end flex-column">
                        <button type="button" class="btn backbtn fw-regular my-2" data-bs-dismiss="modal">Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade assignedTo" id="selectToAssign">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header align-items-start border-0">
                    <div class="">
                        <h2 class="modal-title fs-6 fw-semibold">Select to Assign</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="assignList">
                        <span class="text-secondary">Assigned to:</span>
                        <ul class="nav nav-tabs assignTb">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#individualTab"
                                    type="button">Individuals</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#classTab"
                                    type="button">Class</button>
                            </li>
                        </ul>

                        <!-- SINGLE FORM FOR BOTH TABS -->
                        <form id="assignTestForm" method="POST">
                            @csrf
                            <input type="hidden" name="class_id" id="classIdInput">
                            <input type="hidden" name="test_id" id="testIdInput">
                            <input type="hidden" name="assign_type" id="assignType" value="individual">
                            <!-- New hidden field to track assignment type -->

                            <div class="tab-content">
                                <!-- Individuals Tab -->
                                <div class="tab-pane fade show active" id="individualTab">
                                    <div class="searchStudent mb-3">
                                        <span>Select Students</span>
                                        <input class="form-control" placeholder="Search">
                                    </div>

                                    <div class="table-responsive tbleDiv">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="ps-0">Name</th>
                                                    <th>Admission No.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Student rows will be inserted dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Class Tab -->
                                <div class="tab-pane fade" id="classTab">
                                    <div class="text-center">
                                        <lottie-player
                                            src="{{ asset('frontend/images/teacher-is-solving-mathematical-sums-on-blackboard.json') }}"
                                            background="transparent" speed="1"
                                            style="width: 220px; height: 220px;margin:auto" autoplay>
                                        </lottie-player>
                                        <p class="fs-8 text-secondary">Click Confirm button to assign test paper to class
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary-gradient rounded-1"
                                    id="submitAssign">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade assignedTo" id="partiallyAssigned">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header align-items-start border-0">
                    <div class="">
                        <h2 class="modal-title fs-6 fw-semibold">Select to Assign</h2>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="assignList">
                        <span class="text-secondary">Assigned to:</span>
                        <ul class="nav nav-tabs assignTb">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#individualTab"
                                    type="button">Individuals</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#classTab"
                                    type="button">Class</button>
                            </li>
                        </ul>

                        <!-- SINGLE FORM FOR BOTH TABS -->
                        <form id="assignTestForm" method="POST">
                            @csrf
                            <input type="hidden" name="class_id" id="classIdInput">
                            <input type="hidden" name="test_id" id="testIdInput">
                            <input type="hidden" name="assign_type" id="assignType" value="individual">
                            <!-- New hidden field to track assignment type -->

                            <div class="tab-content">
                                <!-- Individuals Tab -->
                                <div class="tab-pane fade show active" id="individualTab">
                                    <div class="searchStudent mb-3">
                                        <span>Select Students</span>
                                        <input class="form-control" placeholder="Search">
                                    </div>

                                    <div class="table-responsive tbleDiv">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th class="ps-0">Name</th>
                                                    <th>Admission No.</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Student rows will be inserted dynamically -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Class Tab -->
                                <div class="tab-pane fade" id="classTab">
                                    <div class="text-center">
                                        <lottie-player
                                            src="{{ asset('frontend/images/teacher-is-solving-mathematical-sums-on-blackboard.json') }}"
                                            background="transparent" speed="1"
                                            style="width: 220px; height: 220px;margin:auto" autoplay>
                                        </lottie-player>
                                        <p class="fs-8 text-secondary">Click Confirm button to assign test paper to class
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-primary-gradient rounded-1"
                                    id="submitAssign">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <script>
        // When any Print button is clicked
        document.addEventListener('click', function(e) {
            if (e.target.closest('.openPdfModal')) {
                const button = e.target.closest('.openPdfModal');
                const testId = button.getAttribute('data-id');
                document.getElementById('selectedTestId').value = testId;
            }
        });

        function triggerDownload(type) {
            const fileType = document.getElementById('fileType').value;
            const testId = document.getElementById('selectedTestId').value;

            const url = `/school-portal/tests/download/${testId}/${type}/${fileType}`;
            window.open(url, "_blank");
        }
    </script>

    <script>
        const searchInput = document.getElementById('search-input');
        const tableRows = document.querySelectorAll('#question-table tbody tr');
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            tableRows.forEach(row => {
                const title = row.getAttribute('data-name').toLowerCase();
                if (title.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".unassignedtxt").forEach(button => {
                button.addEventListener("click", function() {
                    let classId = this.getAttribute("data-class-id");
                    let testId = this.getAttribute("data-test-id");
                    let participantIds = JSON.parse(this.getAttribute("data-participant-ids") ||
                        "[]");

                    document.getElementById("classIdInput").value = classId; // Set class_id in form
                    document.getElementById("testIdInput").value = testId; // Set test_id in form

                    fetchStudents(classId, participantIds);
                });
            });

            function fetchStudents(classId, participantIds) {
                let url = "{{ route('get.students', ':classId') }}".replace(':classId', classId);
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        let tbody = document.querySelector("#assignTestForm tbody");
                        tbody.innerHTML = ""; // Clear previous data

                        if (data.length > 0) {
                            let html = ""; // Store HTML content to append later

                            data.forEach(student => {
                                let admissionNo = student.user_additional_detail ? student
                                    .user_additional_detail.admission_no : "N/A";

                                let imagePath = student.image ?
                                    `/storage/uploads/user/profile_image/${student.image}` // Replace with the correct storage path
                                    :
                                    `/frontend/images/default-image.jpg`; // Use direct path instead of Blade asset

                                let isChecked = participantIds.includes(student.id) ? "checked" : "";

                                html += `
            <tr>
                <td class="ps-0">
                    <span class="nameTbl mb-0 d-flex align-items-center gap-2">
                        <div class="cstmCheckbox">
                            <input type="checkbox" id="st-${student.id}" name="students[]" value="${student.id}" ${isChecked}>
                            <label for="st-${student.id}" class="p-2"></label>
                        </div>
                        <img src="${imagePath}" alt="" class="me-0">
                        ${student.name}
                    </span>
                </td>
                <td>${admissionNo}</td> 
            </tr>`;
                            });

                            tbody.innerHTML = html; // Update once instead of inside the loop
                        } else {
                            tbody.innerHTML =
                                `<tr><td colspan="2" class="text-center text-muted">No students found.</td></tr>`;
                        }
                    })
                    .catch(error => console.error("Error fetching students:", error));
            }

            // Handle tab switch to change assign type
            document.querySelectorAll(".nav-link").forEach(tab => {
                tab.addEventListener("click", function() {
                    let assignTypeInput = document.getElementById("assignType");
                    assignTypeInput.value = this.getAttribute("data-bs-target") === "#classTab" ?
                        "class" : "individual";
                });
            });

            // Submit Form via AJAX
            document.getElementById("assignTestForm").addEventListener("submit", function(event) {
                event.preventDefault();

                let formData = new FormData(this);
                let assignType = document.getElementById("assignType").value;

                // Remove students[] if assigning to class
                if (assignType === "class") {
                    formData.delete("students[]");
                }

                fetch("{{ route('assign.test') }}", {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-Requested-With": "XMLHttpRequest"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert("Error assigning test.");
                        }
                    })
                    .catch(error => console.error("Error submitting form:", error));
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".assignedtxt").forEach(button => {
                button.addEventListener("click", function() {
                    let testId = this.getAttribute("data-test-id");
                    let classId = this.getAttribute("data-class-id");
                    let startDate = this.getAttribute("data-start-id");
                    let endDate = this.getAttribute("data-end-id");
                    console.log(startDate, endDate);
                    // Laravel Route Helper
                    let url =
                        `{{ route('get.participants', ['testId' => '__TEST_ID__', 'classId' => '__CLASS_ID__']) }}`
                        .replace('__TEST_ID__', testId)
                        .replace('__CLASS_ID__', classId);

                    // Clear old data
                    document.getElementById("participantList").innerHTML =
                        `<tr><td colspan="2" class="text-center">Loading...</td></tr>`;
                    document.getElementById("totalStudents").textContent = "0";
                    // document.getElementById("start").textContent = startDate;
                    // document.getElementById("end").textContent = endDate;

                    // Fetch participants
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            let participantList = document.getElementById("participantList");
                            participantList.innerHTML = "";

                            if (data.length > 0) {
                                document.getElementById("totalStudents").textContent = data
                                    .length;

                                data.forEach(participant => {
                                    let imagePath = participant.image !== 'null' &&
                                        participant.image ?
                                        `{{ Storage::url('uploads/user/profile_image/') }}` +
                                        participant.image :
                                        `{{ asset('frontend/images/default-image.jpg') }}`;

                                    let row = `<tr>
                                <td class="ps-0"><span class="nameTbl mb-0">
                                    <img src="${imagePath}" alt="" onerror="this.onerror=null;this.src='{{ asset('frontend/images/default-image.jpg') }}';">
                                    ${participant.name}
                                </span></td>
                                <td>${participant.admission_no}</td>
                            </tr>`;

                                    participantList.innerHTML += row;
                                });
                            } else {
                                participantList.innerHTML =
                                    `<tr><td colspan="2" class="text-center">No students assigned.</td></tr>`;
                            }
                        })
                        .catch(error => console.error("Error fetching participants:", error));
                });
            });
        });
        // document.addEventListener("DOMContentLoaded", function() {
        //     let pdfModal = document.getElementById("pdfModal");
        //     let testIdInput = document.getElementById("pdfTestId");

        //     pdfModal.addEventListener("show.bs.modal", function(event) {
        //         let button = document.getElementById("pdfmodelopenBtn");
        //         let testId = button.getAttribute("data-id");
        //         testIdInput.value = testId;

        //     });

        //     document.getElementById("generatePdfBtn").addEventListener("click", function() {
        //         let testId = testIdInput.value;
        //         let copyType = document.querySelector('input[name="copy_type"]:checked').value;

        //         let url = `/tests/pdf/${testId}?type=${copyType}`;
        //         window.open(url, "_blank"); 
        //         bootstrap.Modal.getInstance(pdfModal).hide();
        //     });
        // });
    </script>
@endsection
