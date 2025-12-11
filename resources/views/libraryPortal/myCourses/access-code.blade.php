@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('sp.my.courses') }}">Subjects/ Courses</a></li>
            <li class="breadcrumb-item active" aria-current="page">Access Code</li>
        </ol>
    </nav>
    <div class="cardBox viewAccess ">
        <div class="d-md-flex justify-content-between align-items-center mb-3">
            <h2 class="fs-6 fw-semibold mb-3 mb-md-0">Access Code</h2>
            <button type="button" id="submitButton" data-bs-toggle="modal" data-bs-target="#largeModal"
                class="btn btn-primary-gradient rounded-1" style="display: none;">Assign All</button>
            <div class="modal fade" id="largeModal" tabindex="-1" aria-labelledby="largeModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-s">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="largeModalLabel">Assgin Access Code</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-12 text-end">
                                    <p>Total Access Code ~ {{ count($totalAccessCodes) }}</p>
                                </div>
                                <div class="col-12">
                                    <label for="accessCodeInput" class="form-label">Access Code Count</label>
                                    <input type="number" id="accessCodeInput" value="{{ count($remainingAccessCodes) }}"
                                        max="{{ count($remainingAccessCodes) }}" class="form-control"
                                        placeholder="Total Access Code" oninput="enforceMaxValue(this)">
                                </div>
                                <div class="col-12">
                                    <label for="studentCount" class="form-label">Student Count</label>
                                    <input type="number" name="student_count" id="studentCount" value="{{ count($users) }}"
                                        max="{{ count($remainingAccessCodes) }}"
                                        class="form-control @if (count($users) == 0) is-invalid @endif"
                                        placeholder="Total Access Code" oninput="enforceMaxValue(this)"
                                        @if (count($users) == 0) disabled @endif>
                                    @if (count($users) == 0)
                                        <div class="invalid-feedback">No student data available, please add students
                                            first.</div>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            @livewire('assign-access-code', ['totalAccessCodes' => $totalAccessCodes, 'remainingAccessCodes' => $remainingAccessCodes, 'users' => $users])
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-end gap-4">
                <button type="button" id="submitButton" class="btn btn-primary-gradient rounded-1" style="display: none;"
                    onclick="submitAccessCode()">Submit</button>
            </div>
            <div class="dropdown d-inline-block">
                <button class="btn btn-primary-gradient rounded-1 py-2 dropdown-toggle" type="button" id="exportDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Export
                </button>
                <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                    <li>
                        <a class="dropdown-item"
                            href="{{ route('access.code.export', ['type' => 'excel'] + request()->query()) }}">
                            Export as Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item"
                            href="{{ route('access.code.export', ['type' => 'csv'] + request()->query()) }}">
                            Export as CSV
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item"
                            href="{{ route('access.code.export', ['type' => 'print'] + request()->query()) }}">
                            Print Code
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="accessDetail mb-3">
            <!-- Progress Bar Section -->
            <div class="d-flex justify-content-between mb-2 gap-2">
                <div class="w-50">
                    <span class="text-success">Occupied Access Code</span>
                    <b>{{ $occcupiedAccessCodes }}</b>
                </div>
                <div class="w-50">
                    <span class="text-primary">Total Access Code</span>
                    <b>{{ count($totalAccessCodes) }}</b>
                </div>
            </div>
            @php
                $occcupiedAccessCodePercentage = ($occcupiedAccessCodes / count($totalAccessCodes)) * 100;
                $remainingAccessCodePercentage = 100 - $occcupiedAccessCodePercentage;
            @endphp
            <div class="progress-stacked bg-transparent mb-2">
                <div class="progress" style="width: {{ $occcupiedAccessCodePercentage }}%">
                    <div class="progress-bar bg-success"></div>
                </div>
                <div class="progress" style="width: {{ $remainingAccessCodePercentage }}%">
                    <div class="progress-bar bg-primary"></div>
                </div>
            </div>
            <p>Remaining Code ~ {{ count($remainingAccessCodes) }}</p>
        </div>

        <!-- Toggle and Table -->
        <div class="table-responsive tbleDiv">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Student List</th>
                        <th colspan="3">
                            <div class="d-flex align-items-center gap-3 justify-content-end">
                                <div class="searchContent text-end">
                                    <input type="text" id="searchInput" class="form-control"
                                        placeholder="Search Aceess Code">
                                </div>

                                <span>Assigned</span>
                                <div class="toggleBtn">
                                    <input type="checkbox" id="switch" onchange="filterRows()" />
                                    <label for="switch">Toggle</label>
                                </div>
                                <span>Not Assigned</span>
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th>Student Name</th>
                        <th>Access Code</th>
                        <th>Redeemed Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="search-results">
                    @foreach ($redeemedAccessCode as $data)
                        <tr class="status-row assigned" data-status="assigned" data-title="{{ $data->access_code }}">
                            <td>{{ $data->usedAccessCodes->name ?? ' ' }}</td>
                            <td>{{ $data->access_code }}</td>
                            <td>{{ $data->accessCodeLog->created_at ?? ' ' }}</td>
                            <td>
                                <div class="assignStatus">Assigned</div>
                            </td>
                        </tr>
                    @endforeach
                    @foreach ($unRedeemedAccessCode as $data)
                        <tr class="status-row not-assigned" data-status="not-assigned"
                            data-title="{{ $data->access_code }}">
                            <td>
                                <select class="form-select user-select" data-access-code="{{ $data->access_code }}">
                                    <option value="">Select User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{{ $data->access_code }}</td>
                            <td>{{ $data->accessCodeLog->created_at ?? ' ' }}</td>
                            <td>
                                <div class="notassignStatus">Not-assigned</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{--  <div class="d-flex align-items-center justify-content-end gap-4">
                    <button type="button" id="submitButton" class="btn btn-primary-gradient rounded-1"
                        style="display: none;" onclick="submitAccessCode()">Submit</button>
                </div>  --}}
        </div>
    </div>

    <script>
        var globalVar = {
            page: 'my_course_access_code',
        };
    </script>
    <script>
        function enforceMaxValue(input) {
            const max = parseInt(input.max, 10);
            const value = parseInt(input.value, 10);
            if (value > max) {
                input.value = max;
            }
        }

        function filterRows() {
            const toggleSwitch = document.getElementById("switch");
            const rowsAssigned = document.querySelectorAll(".status-row.assigned");
            const rowsNotAssigned = document.querySelectorAll(".status-row.not-assigned");

            if (toggleSwitch.checked) {
                // Show "Not Assigned" and hide "Assigned"
                rowsAssigned.forEach(row => row.style.display = "none");
                rowsNotAssigned.forEach(row => row.style.display = "table-row");
            } else {
                // Show "Assigned" and hide "Not Assigned"
                rowsAssigned.forEach(row => row.style.display = "table-row");
                rowsNotAssigned.forEach(row => row.style.display = "none");
            }
        }

        // Initialize view to show only "Assigned"
        document.addEventListener("DOMContentLoaded", () => {
            const toggleSwitch = document.getElementById("switch");
            toggleSwitch.checked = false; // Ensure it's in the "Assigned" position
            filterRows();
        });

        function filterRows() {
            const toggleSwitch = document.getElementById("switch");
            const rowsAssigned = document.querySelectorAll(".status-row.assigned");
            const rowsNotAssigned = document.querySelectorAll(".status-row.not-assigned");
            const submitButton = document.getElementById("submitButton");
            const assignButton = document.getElementById("assignButton");
            let hasUnassigned = false;

            if (toggleSwitch.checked) {
                rowsAssigned.forEach(row => row.style.display = "none");
                rowsNotAssigned.forEach(row => {
                    row.style.display = "table-row";
                    if (row.querySelector(".user-select")) {
                        hasUnassigned = true;
                    }
                });
                submitButton.style.display = hasUnassigned ? "inline-block" : "none";
            } else {
                rowsAssigned.forEach(row => row.style.display = "table-row");
                rowsNotAssigned.forEach(row => row.style.display = "none");
                submitButton.style.display = "none";
                assignButton.style.display = "none";
            }
        }

        function submitAccessCode() {
            const rows = document.querySelectorAll(".status-row.not-assigned");
            const selectedData = [];
            const assignAccessCodesUrl = "{{ route('sp.assign.access.codes') }}";

            rows.forEach(row => {

                const select = row.querySelector(".user-select");
                const userId = select.value;
                const accessCode = select.dataset.accessCode;

                if (userId) {
                    selectedData.push({
                        user_id: userId,
                        access_code: accessCode
                    });
                }
            });

            if (selectedData.length === 0) {
                alert("Please select a user for at least one access code.");
                return;
            }

            if (confirm("Are you sure you want to assign these access codes?")) {
                fetch(assignAccessCodesUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrf_token,
                        },
                        body: JSON.stringify({
                            data: selectedData
                        }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            alert("Access codes assigned successfully.");
                            location.reload();
                        } else {
                            alert(data.message || "An error occurred. Please try again.");
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("An error occurred. Please try again.");
                    });
            }
        }
    </script>
@endsection
