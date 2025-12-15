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
    <style>
        .seat-card {
            border-radius: 8px;
            padding: 10px;
            min-height: 150px;
            cursor: pointer;
        }

        .seat-white {
            background: #fff;
        }

        .seat-yellow {
            background: #fff3cd;
        }

        .seat-green {
            background: #d1e7dd;
        }

        .seat-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-top: 5px;
        }
    </style>


    <div class="row">
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
                    <div class="row g-3">
                        @foreach ($totalSeats as $seat)
                            <div class="col-md-2">
                                <div class="seat-card {{ $seat->status_class }}" data-seat-id="{{ $seat->id }}"
                                    data-seat-no="{{ $seat->seat_no }}">

                                    <h6 class="text-center">Seat {{ $seat->seat_no }}</h6>

                                    {{-- Full Day --}}
                                    @if ($seat->fullDay)
                                        <div class="seat-row text-success fw-bold">
                                            Full Day: {{ $seat->fullDay->user->name }}
                                        </div>
                                    @else
                                        <div class="seat-row">
                                            Full Day
                                            <button class="btn btn-sm btn-outline-primary addSeat"
                                                data-shift="full_day">+</button>
                                        </div>
                                    @endif

                                    {{-- First Half --}}
                                    @if (!$seat->fullDay)
                                        @if ($seat->firstHalf)
                                            <div class="seat-row text-warning">
                                                1st Half: {{ $seat->firstHalf->user->name }}
                                            </div>
                                        @else
                                            <div class="seat-row">
                                                1st Half
                                                <button class="btn btn-sm btn-outline-primary addSeat"
                                                    data-shift="first_half">+</button>
                                            </div>
                                        @endif
                                    @endif

                                    {{-- Second Half --}}
                                    @if (!$seat->fullDay)
                                        @if ($seat->secondHalf)
                                            <div class="seat-row text-warning">
                                                2nd Half: {{ $seat->secondHalf->user->name }}
                                            </div>
                                        @else
                                            <div class="seat-row">
                                                2nd Half
                                                <button class="btn btn-sm btn-outline-primary addSeat"
                                                    data-shift="second_half">+</button>
                                            </div>
                                        @endif
                                    @endif

                                </div>
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
                    <input type="hidden" id="deleteStudentId">
                    <a href="#" id="confirmDeleteBtn" class="btn btn-primary-gradient rounded-1">Yes</a>
                    <div>
                        <button type="button" class="btn btnNo" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="seatAssignModal">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('seat.assign') }}">
                @csrf
                <input type="hidden" name="seat_id" id="seat_id">
                <input type="hidden" name="shift" id="shift">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Assign Seat</h5>
                    </div>

                    <div class="modal-body">
                        <div class="mb-2">
                            <label>Shift</label>
                            <select class="form-control" id="shiftSelect" disabled>
                                <option value="full_day">Full Day</option>
                                <option value="first_half">First Half</option>
                                <option value="second_half">Second Half</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label>User</label>
                            <select name="user_id" class="form-control" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col">
                                <label>Start Date</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col">
                                <label>End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">Assign</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('.addSeat').forEach(btn => {
            btn.addEventListener('click', function() {

                let card = this.closest('.seat-card');

                document.getElementById('seat_id').value = card.dataset.seatId;
                document.getElementById('shift').value = this.dataset.shift;

                document.getElementById('shiftSelect').value = this.dataset.shift;

                new bootstrap.Modal(
                    document.getElementById('seatAssignModal')
                ).show();
            });
        });
    </script>
@endsection
