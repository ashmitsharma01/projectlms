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
            <h5 class="mb-3">Collect Fees</h5>
            <hr class="form-divider">

            <!-- Form Start -->
            <form action="{{ route('collect.fees.save') }}" method="POST" id="add-plan-form" class="row g-3">
                @csrf

                <input type="hidden" name="id" id="student_id_field" value="{{ $studentData->user_id ?? '' }}">

                <div class="formPanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="student_id">Student</label> <b>*</b>
                                <select id="student_id" name="student_id" class="form-select">
                                    <option value="">Search student</option>
                                    @foreach ($students as $id => $student)
                                        <option value="{{ $id }}">{{ $student }}</option>
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
                                <input type="date" name="start_date" id="start_date"
                                    class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}"
                                    value="{{ old('start_date', now()->toDateString()) }}">
                                @error('start_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label for="end_date">End Date</label> <b>*</b>
                                <input type="date" name="end_date" id="end_date"
                                    class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}"
                                    value="{{ old('end_date', now()->addMonth()->toDateString()) }}">
                                @error('end_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="amount">Amount</label> <b>*</b>
                                <input type="number" name="amount" id="amount"
                                    class="form-control {{ $errors->has('amount') ? 'is-invalid' : '' }}"
                                    value="{{ old('amount') }}" placeholder="Enter amount">
                                @error('amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="payment_mode">Payment Mode</label> <b>*</b>
                                <select name="payment_mode" id="payment_mode"
                                    class="form-select {{ $errors->has('payment_mode') ? 'is-invalid' : '' }}">
                                    <option value="">Select</option>
                                    <option value="cash" {{ old('payment_mode') == 'cash' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="upi" {{ old('payment_mode') == 'upi' ? 'selected' : '' }}>UPI
                                    </option>
                                    <option value="card" {{ old('payment_mode') == 'card' ? 'selected' : '' }}>Card
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

    <div class="col-lg-12 px-lg-2 mb-3">
        <div class="cardBox">
            <div class="headingBx">
                <h4>Fees Paid in last 2 months</h4>
            </div>
            <div class="table-responsive tbleDiv">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Admission No.</th>
                            <th>Name</th>
                            <th>Payment Date</th>
                            <th>Duration</th>
                            <th>Mobile No.</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        @forelse ($userPaidFeesList as $payment)
                            <tr>
                                <td>{{ $payment->user->student->admission_no ?? '-' }}</td>

                                <td>
                                    <span class="nameTbl student-name">
                                        <img src="{{ asset('frontend/images/default-image.jpg') }}" alt="">
                                        {{ $payment->user->name ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    {{ optional($payment->payment_date) ? \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td>
                                    @if ($payment->start_date && $payment->end_date)
                                        {{ \Carbon\Carbon::parse($payment->start_date)->format('d M') }}
                                        -
                                        {{ \Carbon\Carbon::parse($payment->end_date)->format('d M') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>{{ $payment->user->mobile_no ?? '-' }}</td>

                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('collect.fees.edit', $payment->id) }}" class="btn p-0 bg-transparent border-0 text-primary">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </a>
                                        <button class="btn p-0 bg-transparent border-0 text-danger">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No fee payments found in last 2 months
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    <script>
        new Choices('#student_id', {
            searchEnabled: true,
            shouldSort: false,
        });
    </script>
@endsection
