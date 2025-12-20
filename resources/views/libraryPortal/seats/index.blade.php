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

    <style>
        /* Seat Container Styles */
        .seat-grid-container {
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-top: 20px;
        }

        .seat-stats-bar {
            background: #f8f9fa;
            padding: 10px 20px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 12px;
            color: #666;
        }

        .stat-item {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
        }

        .stat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 6px;
        }

        .dot-available {
            background: #e0e0e0;
        }

        .dot-partial {
            background: #ffd700;
        }

        .dot-occupied {
            background: #28a745;
        }

        .seat-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            padding: 20px;
        }

        .seat-card {
            border: 1px solid #e8e8e8;
            border-radius: 6px;
            padding: 12px;
            background: #fff;
            transition: border-color 0.2s;
        }

        /* Background colors for seats */
        .seat-card-available {
            background: #ffffff;
        }

        .seat-card-partial {
            background: #fff8e1;
            /* Yellow background */
        }

        .seat-card-occupied {
            background: #e8f5e9;
            /* Green background */
        }

        .seat-card:hover {
            border-color: #d0d0d0;
        }

        .seat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #f0f0f0;
        }

        .seat-number {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .seat-status {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-free {
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }

        .status-partial {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .status-occupied {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .shift-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            font-size: 12px;
        }

        .shift-row:not(:last-child) {
            border-bottom: 1px dashed #eee;
        }

        .shift-label {
            color: #666;
            font-weight: 500;
        }

        .user-info {
            max-width: 60%;
            overflow: hidden;
        }

        .user-name {
            color: #333;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .assign-btn {
            width: 22px;
            height: 22px;
            padding: 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
            color: #666;
            font-size: 12px;
            transition: all 0.2s;
        }

        .assign-btn:hover {
            border-color: #4a6cf7;
            color: #4a6cf7;
        }

        .modal-footer .btn {
            border-radius: 3px;
            font-weight: normal;
            font-size: 13px;
            padding: 4px 16px;
        }

        .modal-footer .btn-outline-secondary {
            padding: 4px 12px;
        }

        .deleteBtn {
            width: 22px;
            height: 22px;
            padding: 0;
            border: 1px solid rgb(243, 114, 114);
            border-radius: 4px;
            font-size: 12px;
            transition: all 0.2s;
            background-color: #ffffff;
            color: red;
            margin-left: 6px;
        }

        .deleteBtn:hover {
            background-color: rgb(223, 44, 44);
            color: #e8e8e8;
        }
    </style>

    <!-- Your Original Header -->
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="teacherTable">
                <div class="headerTbl">
                    <h6 class="m-0">Seats Management</h6>

                    <div class="teacherrightTable">
                        <div class="d-flex align-items-center gap-2">
                            <input type="text" id="filterSeat" class="form-control" placeholder="Search by Seat No."
                                style="font-size: 12px !important; width:140px;">

                            <input type="text" id="filterName" class="form-control" placeholder="Search by name"
                                style="font-size: 12px !important; width:140px;">

                            <input type="text" id="filterMobile" class="form-control" placeholder="Search by Mobile"
                                style="font-size: 12px !important; width:140px;">
                            <select id="filterStatus" class="form-control" style="font-size:12px; width:140px;">
                                <option value="">All Status</option>
                                <option value="available">Available</option>
                                <option value="partial">Partial</option>
                                <option value="occupied">Booked</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="seat-grid-container">
                    <div class="seat-stats-bar">
                        @php
                            $stats = [
                                'available' => 0,
                                'partial' => 0,
                                'occupied' => 0,
                            ];

                            foreach ($seats as $seat) {
                                $hasFullDay = (bool) $seat->fullDay;
                                $hasFirstHalf = (bool) $seat->firstHalf;
                                $hasSecondHalf = (bool) $seat->secondHalf;

                                if ($hasFullDay) {
                                    $stats['occupied']++;
                                } elseif ($hasFirstHalf && $hasSecondHalf) {
                                    $stats['occupied']++;
                                } elseif ($hasFirstHalf || $hasSecondHalf) {
                                    $stats['partial']++;
                                } else {
                                    $stats['available']++;
                                }
                            }
                        @endphp

                        <div class="stat-item">
                            <span class="stat-dot dot-available"></span>
                            <span>Available: {{ $stats['available'] }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-dot dot-partial"></span>
                            <span>Partial: {{ $stats['partial'] }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-dot dot-occupied"></span>
                            <span>Occupied: {{ $stats['occupied'] }}</span>
                        </div>
                        <div class="stat-item">
                            <span>Total: {{ count($seats) }}</span>
                        </div>
                    </div>

                    <div class="seat-grid">
                        @foreach ($seats as $seat)
                            @php
                                $hasFullDay = (bool) $seat->fullDay;
                                $hasFirstHalf = (bool) $seat->firstHalf;
                                $hasSecondHalf = (bool) $seat->secondHalf;

                                if ($hasFullDay) {
                                    $statusClass = 'status-occupied';
                                    $statusText = 'Occupied';
                                    $bgClass = 'seat-card-occupied';
                                } elseif ($hasFirstHalf && $hasSecondHalf) {
                                    $statusClass = 'status-occupied';
                                    $statusText = 'Occupied';
                                    $bgClass = 'seat-card-occupied';
                                } elseif ($hasFirstHalf || $hasSecondHalf) {
                                    $statusClass = 'status-partial';
                                    $statusText = 'Partial';
                                    $bgClass = 'seat-card-partial';
                                } else {
                                    $statusClass = 'status-free';
                                    $statusText = 'Available';
                                    $bgClass = 'seat-card-available';
                                }
                            @endphp

                            <div class="seat-card {{ $bgClass }}" data-seat-number="{{ $seat->seat_number }}"
                                data-status="{{ strtolower($statusText) }}"
                                data-user-name="{{ $seat->fullDay->user->name ?? ($seat->firstHalf->user->name ?? ($seat->secondHalf->user->name ?? '')) }}"
                                data-mobile="{{ $seat->fullDay->user->mobile_no ?? ($seat->firstHalf->user->mobile_no ?? ($seat->secondHalf->user->mobile_no ?? '')) }}"
                                data-admission="{{ $seat->fullDay->user->admission_no ?? ($seat->firstHalf->user->admission_no ?? ($seat->secondHalf->user->admission_no ?? '')) }}">

                                <div class="seat-header">
                                    <span class="seat-number">Seat {{ $seat->seat_number }}</span>
                                    <span class="seat-status {{ $statusClass }}">{{ $statusText }}</span>
                                </div>

                                @if (!$hasFirstHalf && !$hasSecondHalf)
                                    <div class="shift-row">
                                        <span class="shift-label">Full Day</span>
                                        @if ($hasFullDay)
                                            <div class="user-info d-flex align-items-center justify-content-between">
                                                <span class="user-name">{{ $seat->fullDay->user->name }}</span>

                                                <button type="button" class="btn btn-sm btn-danger assign-btn deleteBtn"
                                                    data-id="{{ $seat->fullDay->id }}"
                                                    data-name="{{ $seat->fullDay->user->name }}">
                                                    ✕
                                                </button>
                                            </div>
                                        @else
                                            <button class="btn assign-btn addSeat" data-seat-id="{{ $seat->id }}"
                                                data-shift="full_day">+</button>
                                        @endif
                                    </div>
                                @endif

                                <!-- First Half -->
                                @if (!$hasFullDay)
                                    <div class="shift-row">
                                        <span class="shift-label">1st Half</span>
                                        @if ($hasFirstHalf)
                                            <div class="user-info d-flex align-items-center justify-content-between">
                                                <span class="user-name">{{ $seat->firstHalf->user->name }}</span>

                                                <button type="button" class="btn btn-sm btn-danger assign-btn deleteBtn"
                                                    data-id="{{ $seat->firstHalf->id }}"
                                                    data-name="{{ $seat->firstHalf->user->name }}">
                                                    ✕
                                                </button>
                                            </div>
                                        @else
                                            <button class="btn assign-btn addSeat" data-seat-id="{{ $seat->id }}"
                                                data-shift="first_half">+</button>
                                        @endif
                                    </div>
                                @endif

                                <!-- Second Half -->
                                @if (!$hasFullDay)
                                    <div class="shift-row">
                                        <span class="shift-label">2nd Half</span>
                                        @if ($hasSecondHalf)
                                            <div class="user-info d-flex align-items-center justify-content-between">
                                                <span class="user-name">{{ $seat->secondHalf->user->name }}</span>

                                                <button type="button" class="btn btn-sm btn-danger assign-btn deleteBtn"
                                                    data-id="{{ $seat->secondHalf->id }}"
                                                    data-name="{{ $seat->secondHalf->user->name }}">
                                                    ✕
                                                </button>
                                            </div>
                                        @else
                                            <button class="btn assign-btn addSeat" data-seat-id="{{ $seat->id }}"
                                                data-shift="second_half">+</button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
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

                    <!-- IMPORTANT -->
                    <input type="hidden" id="deleteAssignmentId">

                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger rounded-1">
                        Yes, Delete
                    </a>

                    <div class="mt-2">
                        <button type="button" class="btn btnNo" data-bs-dismiss="modal">
                            No
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Assign Seat Modal -->
    <div class="modal fade" id="seatAssignModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <form method="POST" action="{{ route('seat.assign') }}">
                @csrf
                <input type="hidden" name="seat_id" id="seat_id">
                <input type="hidden" name="shift" id="shift">

                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Assign Seat</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label required">Shift</label>
                            <select class="form-control form-control-sm" id="shiftSelect" disabled>
                                <option value="full_day">Full Day</option>
                                <option value="first_half">First Half</option>
                                <option value="second_half">Second Half</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label required">User</label>
                            <select name="user_id" id="user_id" class="form-control form-control-sm" required>
                                <option value="">--Select--</option>
                                @foreach ($users as $id => $user)
                                    <option value="{{ $id }}">{{ $user }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-2">
                            <div class="col">
                                <label class="form-label required">Start Date</label>
                                <input type="date" name="start_date" value="{{ now()->format('Y-m-d') }}"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col">
                                <label class="form-label required">End Date</label>
                                <input type="date" name="end_date" class="form-control form-control-sm"
                                    value="{{ now()->addMonth()->format('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Assign</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        new Choices('#user_id', {
            searchEnabled: true,
            shouldSort: false,
        });
    </script>
    <script>
        document.querySelectorAll('.addSeat').forEach(btn => {
            btn.addEventListener('click', function() {

                // READ FROM BUTTON, NOT CARD
                document.getElementById('seat_id').value = this.dataset.seatId;
                document.getElementById('shift').value = this.dataset.shift;
                document.getElementById('shiftSelect').value = this.dataset.shift;

                new bootstrap.Modal(
                    document.getElementById('seatAssignModal')
                ).show();
            });
        });
    </script>

    <script>
        const filterName = document.getElementById('filterName');
        const filterMobile = document.getElementById('filterMobile');
        const filterSeat = document.getElementById('filterSeat');
        const filterStatus = document.getElementById('filterStatus');

        const seatCards = document.querySelectorAll('.seat-card');

        function filterSeats() {
            const nameVal = filterName.value.toLowerCase();
            const mobileVal = filterMobile.value.toLowerCase();
            const seatVal = filterSeat.value.toLowerCase();
            const statusVal = filterStatus.value.toLowerCase();

            seatCards.forEach(card => {
                const userName = (card.dataset.userName || '').toLowerCase();
                const mobile = (card.dataset.mobile || '').toLowerCase();
                const seatNo = (card.dataset.seatNumber || '').toLowerCase();
                const status = (card.dataset.status || '').toLowerCase();

                const matchName = userName.includes(nameVal);
                const matchMobile = mobile.includes(mobileVal);
                const matchSeat = seatNo.includes(seatVal);
                const matchStatus = statusVal === '' || status === statusVal;

                card.style.display =
                    matchName && matchMobile && matchSeat && matchStatus ?
                    '' :
                    'none';
            });
        }

        filterName.addEventListener('keyup', filterSeats);
        filterMobile.addEventListener('keyup', filterSeats);
        filterSeat.addEventListener('keyup', filterSeats);
        filterStatus.addEventListener('change', filterSeats);
    </script>

    <script>
        document.querySelectorAll('.deleteBtn').forEach(btn => {
            btn.addEventListener('click', function() {
                const assignmentId = this.dataset.id;
                const userName = this.dataset.name;

                document.getElementById('statusText').innerText =
                    `Do you really want to remove ${userName} from this seat?`;

                document.getElementById('deleteAssignmentId').value = assignmentId;

                const modal = new bootstrap.Modal(document.getElementById('statusMdl'));
                modal.show();
            });
        });

        document.getElementById('confirmDeleteBtn').addEventListener('click', function(e) {
            e.preventDefault();

            const assignmentId = document.getElementById('deleteAssignmentId').value;

            // Redirect to GET delete route
            window.location.href =
                "{{ route('seat.assignment.delete', ':id') }}".replace(':id', assignmentId);
        });
    </script>
@endsection
