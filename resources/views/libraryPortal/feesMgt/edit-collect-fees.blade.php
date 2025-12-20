@extends('libraryPortal.layouts.master')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @include('libraryPortal.layouts.flash-messages')

    @php
        $flag = 0;
        $heading = 'Add';
        if (isset($studentData) && !empty($studentData)) {
            $flag = 1;
            $heading = 'Edit';
        }
    @endphp

    <div class="cardBox teacherMain py-md-4 mb-3">
        <div class="formPanel">
            <h5 class="mb-3">Edit Fees</h5>
            <hr class="form-divider">

            <form action="{{ route('collect.fees.save') }}" method="POST" id="add-plan-form" class="row g-3">
                @csrf
                <input type="hidden" name="id" id="student_id_field" value="{{ $studentData->user_id ?? '' }}">
                <input type="hidden" name="payment_id" value="{{ $payment->id ?? '' }}">
                <div class="formPanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="student_id">Student</label> <b>*</b>
                                <select id="student_id" name="student_id" class="form-select">
                                    <option value="">Search student</option>
                                    @foreach ($students as $id => $student)
                                        <option value="{{ $id }}"
                                            {{ old('student_id', $payment->student_user_id ?? '') == $id ? 'selected' : '' }}>
                                            {{ $student }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="start_date">Start Date</label> <b>*</b>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ old('start_date', optional($payment->start_date)->format('Y-m-d')) }}">
                                @error('start_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="end_date">End Date</label> <b>*</b>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ old('end_date', optional($payment->end_date)->format('Y-m-d')) }}">
                                @error('end_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="amount">Amount</label> <b>*</b>
                                <input type="number" name="amount" id="amount" class="form-control"
                                    value="{{ old('amount', $payment->amount ?? '') }}" placeholder="Enter amount">
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="payment_mode">Payment Mode</label> <b>*</b>
                                <select name="payment_mode" id="payment_mode" class="form-select">
                                    <option value="">Select</option>
                                    <option value="cash"
                                        {{ old('payment_mode', $payment->mode ?? '') == 'cash' ? 'selected' : '' }}>
                                        Cash
                                    </option>
                                    <option value="upi"
                                        {{ old('payment_mode', $payment->mode ?? '') == 'upi' ? 'selected' : '' }}>
                                        UPI
                                    </option>
                                    <option value="card"
                                        {{ old('payment_mode', $payment->mode ?? '') == 'card' ? 'selected' : '' }}>
                                        Card
                                    </option>
                                </select>

                                @error('payment_mode')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                <div class="offcanvas-footer" style="padding: 15px !important; padding-bottom:0px !important">
                    <div class="d-flex align-items-center justify-content-end gap-4">
                        <a href="{{ url()->previous() }}" class="btn backbtn">Back</a>
                        <button type="submit" class="btn btn-primary-gradient rounded-1">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>



    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>


    <script>
        new Choices('#student_id', {
            searchEnabled: true,
            shouldSort: false,
        });
    </script>
@endsection
