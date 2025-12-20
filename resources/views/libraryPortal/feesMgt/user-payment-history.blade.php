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

    <style>
        .payment-card {
            background: #fff;
            transition: all 0.2s ease-in-out;
        }

        .payment-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transform: translateY(-3px);
        }
    </style>


    <div class="cardBox teacherMain py-md-4 mb-3">
        <div class="formPanel">
            <h5 class="mb-3">Collect Fees</h5>
            <hr class="form-divider">

            <!-- Form Start -->
            <form action="{{ route('user.payment.history') }}" method="GET" id="add-plan-form" class="row g-3">
                @csrf

                <input type="hidden" name="id" id="student_id_field" value="{{ $studentData->user_id ?? '' }}">

                <div class="formPanel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="student_id">Student</label> <b>*</b>
                                <select name="student_id" id="student_id"
                                    class="form-select {{ $errors->has('student_id') ? 'is-invalid' : '' }}">
                                    <option value="">Select Student</option>
                                    @foreach ($students as $id => $student)
                                        <option value="{{ $id }}"
                                            {{ request('student_id') == $id ? 'selected' : '' }}>
                                            {{ $student }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('student_id')
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

    @if (request('student_id') && $userPaymentHistory->count())
        @php
            $studentUser = $userPaymentHistory->first()->user;
        @endphp

        <div class="cardBox mb-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('frontend/images/default-image.jpg') }}" class="rounded-circle" width="60"
                    height="60">

                <div>
                    <h5 class="mb-1">{{ $studentUser->name }}</h5>
                    <small class="text-muted">
                        Admission No: {{ $studentUser->student->admission_no ?? '-' }} |
                        Mobile: {{ $studentUser->mobile_no ?? '-' }}
                    </small>
                </div>
            </div>
        </div>
    @endif

    <div class="col-lg-12 px-lg-2 mb-3">
        <div class="cardBox">
            <div class="headingBx">
                <h4>Payment History</h4>
            </div>

            <div class="row g-3">
                @forelse ($userPaymentHistory as $payment)
                    @php
                        $start = \Carbon\Carbon::parse($payment->start_date);
                        $end = \Carbon\Carbon::parse($payment->end_date);

                        if ($start->format('Y-m') === $end->format('Y-m')) {
                            $period = $start->format('M');
                        } else {
                            $period = $start->format('M') . '–' . $end->format('M');
                        }
                    @endphp

                    <div class="col-md-4 col-sm-6">
                        <div class="payment-card border rounded-3 p-3 h-100">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary-subtle text-primary fw-semibold">
                                    {{ $period }}
                                </span>

                                <span class="fw-bold text-success fs-5">
                                    ₹{{ number_format($payment->amount, 2) }}
                                </span>
                            </div>

                            <div class="small text-muted">
                                <div class="mb-1">
                                    <strong>Mode:</strong> {{ ucfirst($payment->mode) }}
                                </div>

                                <div class="mb-1">
                                    <strong>Duration:</strong>
                                    {{ $start->format('d M Y') }} –
                                    {{ $end->format('d M Y') }}
                                </div>

                                <div>
                                    <strong>Paid On:</strong>
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <hr class="form-divider">
                    <div class="text-center text-muted mt-0">
                        No payment history found
                    </div>
                @endforelse
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
