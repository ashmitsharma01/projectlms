@extends('libraryPortal.layouts.master')
@section('content')

    <div class="col-md-12 pe-md-1 mb-3 mb-md-0">
        <div class="cardBox teacherMain py-md-4  mb-3">
            <div class="row">
                <div class="col-sm-6">
                    <h5 class="fw-semibold">Licenses/ Access Codes </h5>
                </div>
                <div class="col-sm-6 text-end mt-3">
                    <div id="exportDropdownContainer" class="dropdown d-inline-block d-none">
                        @if ($selectedType == 'teachlite')
                            <button class="btn btn-success lincenseBtn dropdown-toggle rounded-1 addBtn" type="button"
                                id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Distribute License/ Export </button>
                        @else
                            <button class="btn btn-success lincenseBtn dropdown-toggle rounded-1 addBtn" type="button"
                                id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Export </button>
                        @endif
                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                            @if ($selectedType == 'teachlite')
                                <li>
                                    <a href="javascript:void(0);" class="dropdown-item export-options" data-type="mail">
                                        Send via Email
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item export-option" data-type="excel">
                                    Download Excel File </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item export-option" data-type="csv">
                                    Download CSV File
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            @if ($accessCodesCounts != 0)
                <hr class="formdivider">
                <form id="exportForm" method="POST" action="{{ route('your-license.embibe.export') }}">
                    @csrf
                    <input type="hidden" name="ids" id="selectedIds" value="">
                    <input type="hidden" name="type" id="exportType" value="">
                </form>
                <form method="GET" action="{{ route('sp.your-license') }}">
                    <div class="row">
                        <div class="col mb-1">
                            <select class="form-control" name="type">
                                <option value="">Search by License Type</option>
                                @foreach (config('constants.YOUR_LICENSE') as $key => $value)
                                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-1">
                            <button type="submit" class="btn btn-primary-gradient rounded-1 addBtn">Search</button>
                            <a href="{{ route('sp.your-license') }}"
                                class="btn btn-secondary lincenseBtn rounded-1 addBtn">Clear</a>
                        </div>
                    </div>
                </form>
            @else
                <p class="fw-medium">It looks like your school doesn't have any active licenses yet. Don't worry! Reach out
                    to your admin to get access and unlock all the features. 🚀 </p>
            @endif
            @if (request('type'))
                <div class="table-responsive tbleDiv mt-3">
                    <table class="table table-striped table-bordered datatable">
                        <thead>
                            <tr>
                                {{--  @if ($selectedType == 'teachlite')  --}}
                                <th class="text-start align-middle">
                                    <div class="form-check d-flex align-items-center gap-2">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label mb-0" for="selectAll">All</label>
                                    </div>
                                </th>

                                {{--  @endif  --}}
                                <th>S.No</th>
                                @if ($selectedType == 'teachlite')
                                    <th><b>Content Bundle</b></th>
                                @endif
                                <th><b>Licence Key</b></th>
                                <th><b>Expiry(days)</b></th>
                                <th><b>Status</b></th>
                                @if ($selectedType == 'teachlite')
                                    <th><b>Teacher Name</b></th>
                                @else
                                    <th><b>Student Name</b></th>
                                @endif
                                {{--  <th><b>Created At</b></th>  --}}
                                @if ($selectedType == 'mittlense')
                                    <th><b>Action</b></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($accessCode) && count($accessCode) > 0)
                                @foreach ($accessCode as $item)
                                    <tr class="access-row" data-type="{{ $item->type }}">
                                        {{--  @if ($selectedType == 'teachlite')  --}}
                                        <td>
                                            @if ($item->is_distribute === 0)
                                                <input type="checkbox" class="row-checkbox" value="{{ $item->id }}">
                                            @endif
                                        </td>
                                        {{--  @endif  --}}
                                        <td>{{ $loop->iteration }}.</td>
                                        @if ($selectedType == 'teachlite')
                                            <td>{{ $item->content_bundle ?? 'N/A' }}</td>
                                        @endif
                                        <td>{{ $item->licence_key ?? 'N/A' }}</td>
                                        <td>{{ $item->licence_expiry ?? 'N/A' }}</td>
                                        <td> <span class="{{ $item->is_distribute == 1 ? 'activeTxt' : 'deactiveTxt' }}">
                                                {{ $item->is_distribute == 1 ? 'Assgined' : 'Unassgined' }}
                                            </span></td>
                                        <td>{{ $item->usedAccessCodes->name ?? 'N/A' }}</td>
                                        {{--  <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') ?? 'N/A' }}</td>  --}}
                                        @if ($selectedType == 'mittlense' && $item->is_distribute == 0)
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button
                                                        class="btn btn-success lincenseBtn rounded-1 addBtn distribute-btn"
                                                        data-toggle="modal" data-target="#distributeModal"
                                                        data-code="{{ $item->id }}">
                                                        Distribute via Email
                                                    </button>
                                                </div>
                                            </td>
                                        @elseif($selectedType == 'mittlense' && $item->is_distribute == 1)
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <button class="btn btn-secondary lincenseBtn rounded-1 addBtn">
                                                        Distribute via Email
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="10" class="text-center">It looks like your school doesn't have any active
                                        licenses yet. Don't worry! Reach out to your admin to get access and unlock all the
                                        features. 🚀.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Distribute Access Code To Teachers Modal  -->
            <div class="modal fade" id="sendModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg"> <!-- Large modal -->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel">Send Notification</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p id="modalMessage"></p>

                            <!-- User List Table inside the modal -->
                            <form id="userSelectionForm">
                                @csrf
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAlll"></th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody id="userListTable">
                                        <!-- Users will be dynamically inserted here -->
                                    </tbody>
                                </table>
                            </form>
                        </div>
                        <div class="modal-footer d-flex justify-content-between align-items-center">
                            <div id="teachloader-spinner" style="display: none;">
                                <div class="spinner-border text-primary me-2" role="status"
                                    style="width: 1.5rem; height: 1.5rem;">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="confirmSend">Confirm Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Distribute Access Code To Students Modal -->
        <div class="modal fade" id="distributeModal" tabindex="-1" aria-labelledby="distributeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg"> <!-- Large modal to match sendModal -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="distributeModalLabel">Distribute Access Code</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="distributeForm">
                            @csrf
                            <input type="hidden" id="access_code_id" name="access_code_id">

                            <div class="mb-3"> <!-- Bootstrap 5 class for spacing -->
                                <label for="class-select" class="form-label">Select Class</label>
                                <select id="class-select" class="form-control" name="class_id" required>
                                    <option value="">--Select Class--</option>
                                    @foreach ($classes as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="user-select" class="form-label">Select User</label>
                                <select id="user-select" class="form-control" name="user_id" required>
                                    <option value="">--Select User--</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer d-flex justify-content-between align-items-center">
                        <div id="loader-spinner" style="display: none;">
                            <div class="spinner-border text-primary me-2" role="status"
                                style="width: 1.5rem; height: 1.5rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>

                        <div class="ms-auto">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" form="distributeForm">Send Access
                                Code</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
    </div>
    <script>
        $(document).ready(function() {
            let accessCodeId = null;

            // Open modal and store access code ID
            $(".distribute-btn").on("click", function() {
                accessCodeId = $(this).data("code");
                $("#access_code_id").val(accessCodeId);
                $("#distributeModal").modal("show");
            });

            // Fetch users when class is selected
            $("#class-select").on("change", function() {
                let classId = $(this).val();
                if (classId) {
                    $.ajax({
                        url: "{{ route('get.class.users') }}", // Define this route in web.php
                        type: "GET",
                        data: {
                            class_id: classId
                        },
                        success: function(response) {
                            let userSelect = $("#user-select");
                            userSelect.empty().append(
                                '<option value="">--Select User--</option>');
                            $.each(response, function(id, name) {
                                userSelect.append(
                                    `<option value="${id}">${name}</option>`);
                            });
                        }
                    });
                }
            });

            // Handle form submission
            $("#distributeForm").on("submit", function(e) {
                e.preventDefault();

                $("#loader-spinner").show(); // Show spinner

                $.ajax({
                    url: "{{ route('send.access.code.mittlense') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        alert("Access code sent successfully!");
                        $("#distributeModal").modal("hide");
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    },
                    error: function() {
                        alert("Something went wrong. Please try again.");
                    },
                    complete: function() {
                        $("#loader-spinner").hide(); // Hide spinner on response
                    }
                });
            });



        });
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const exportDropdownContainer = document.getElementById('exportDropdownContainer');
            const selectedIdsInput = document.getElementById('selectedIds');

            let selectedIds = new Set();

            // Toggle "Select All" functionality
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;

                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                    if (isChecked) {
                        selectedIds.add(checkbox.value);
                    } else {
                        selectedIds.delete(checkbox.value);
                    }
                });

                updateSelectedIdsInput();
                toggleExportDropdown();
            });

            // Handle individual checkbox changes
            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        selectedIds.add(this.value);
                    } else {
                        selectedIds.delete(this.value);
                    }

                    updateSelectAllState();
                    updateSelectedIdsInput();
                    toggleExportDropdown();
                });

                if (selectedIds.has(checkbox.value)) {
                    checkbox.checked = true;
                }
            });

            // Handle export option click
            document.querySelectorAll('.export-option').forEach(option => {
                option.addEventListener('click', function() {
                    const type = this.getAttribute('data-type');
                    if (selectedIds.size === 0) {
                        alert('Please select at least one access code.');
                        return;
                    }

                    if (type === 'print') {
                        // Open print page in a new window
                        const printUrl =
                            `{{ route('your-license.embibe.print') }}?ids=${Array.from(selectedIds).join(',')}`;
                        window.open(printUrl, '_blank');
                    } else {
                        // Handle other export options
                        document.getElementById('exportType').value = type;
                        document.getElementById('exportForm').submit();
                    }
                });
            });

            function updateSelectedIdsInput() {
                selectedIdsInput.value = Array.from(selectedIds).join(',');
            }

            function updateSelectAllState() {
                const allChecked = Array.from(rowCheckboxes).every(checkbox => checkbox.checked);
                const noneChecked = Array.from(rowCheckboxes).every(checkbox => !checkbox.checked);

                if (allChecked) {
                    selectAllCheckbox.checked = true;
                    selectAllCheckbox.indeterminate = false;
                } else if (noneChecked) {
                    selectAllCheckbox.checked = false;
                    selectAllCheckbox.indeterminate = false;
                } else {
                    selectAllCheckbox.indeterminate = true;
                }
            }

            function toggleExportDropdown() {
                if (selectedIds.size > 0) {
                    exportDropdownContainer.classList.remove('d-none');
                } else {
                    exportDropdownContainer.classList.add('d-none');
                }
            }
        });
        document.addEventListener("DOMContentLoaded", function() {
            // Handle export options click
            document.querySelectorAll(".export-options").forEach(function(button) {
                button.addEventListener("click", function() {
                    let sendType = this.getAttribute("data-type"); // 'mail' or 'sms'
                    let selectedType = "{{ $selectedType ?? '' }}";
                    let modalMessage =
                        `You are sending a ${sendType.toUpperCase()} notification for ${selectedType}.`;

                    document.getElementById("modalMessage").innerText = modalMessage;

                    // Fetch user list dynamically
                    let userListTable = document.getElementById("userListTable");
                    userListTable.innerHTML = ""; // Clear previous data

                    let users =
                        @json($userList); // Pass user data from Blade to JavaScript

                    if (users.length === 0) {
                        userListTable.innerHTML =
                            `<tr><td colspan="4">No users available.</td></tr>`;
                    } else {
                        users.forEach(user => {
                            let row = `
                        <tr class="access-row" data-type="${selectedType}">
                            <td><input type="checkbox" name="selectedUsers[]" value="${user.id}"></td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td>${selectedType.charAt(0).toUpperCase() + selectedType.slice(1)}</td>
                        </tr>`;
                            userListTable.innerHTML += row;
                        });
                    }

                    // Open modal
                    let sendModal = new bootstrap.Modal(document.getElementById("sendModal"));
                    sendModal.show();
                });
            });

            // Select all checkboxes inside the modal
            document.getElementById("selectAlll").addEventListener("click", function() {
                document.querySelectorAll("#userListTable input[type='checkbox']").forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        });
        document.getElementById("confirmSend").addEventListener("click", function() {
            let selectedUsers = Array.from(document.querySelectorAll(
                    "#userListTable input[type='checkbox']:checked"))
                .map(checkbox => checkbox.value);

            let selectedAccessCodes = Array.from(document.querySelectorAll(".access-row .row-checkbox:checked"))
                .map(checkbox => checkbox.value);

            if (selectedUsers.length === 0 || selectedAccessCodes.length === 0) {
                alert("Please select at least one user and one access code.");
                return;
            }

            if (selectedUsers.length !== selectedAccessCodes.length) {
                alert("The number of selected users must be equal to the number of selected access codes.");
                return;
            }

            const csrfToken = document.querySelector("#userSelectionForm input[name='_token']").value;

            $("#teachloader-spinner").show(); // 🔄 Show loader

            fetch("{{ route('save.access.codes') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify({
                        user_ids: selectedUsers,
                        access_code_ids: selectedAccessCodes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    $("#teachloader-spinner").hide(); // ✅ Hide loader on success

                    if (data.success) {
                        alert("Access codes assigned successfully!");
                        location.reload();
                    } else {
                        alert("Something went wrong. Please try again.");
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    $("#teachloader-spinner").hide(); // ✅ Hide loader on error
                    alert("Something went wrong. Please try again.");
                });
        });
    </script>
@endsection
