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

    <!-- Card for Teacher Form -->
    {{-- <div class="cardBox teacherMain py-md-4  mb-3">
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="teacherLeft">
                    <h5 class="fw-semibold">{{ $heading }} Student</h5>
                    <p>Easily add, edit, and bulk upload Student information to keep your records accurate and up to date.
                    </p>


                </div>
            </div>
            @if ($flag != 1)
                <div class="col-md-6 mb-3">
                    <h6 class="">Bulk Upload Student</h6>
                    <div class="col-md-12">
                        @livewire('school-bulk-upload', ['roles' => $roles, 'roleName' => 'school_student'])
                    </div>
                </div>
            @endif
        </div>
    </div> --}}

    {{-- @dd($studentData) --}}
    <!-- Card for Add Teacher Form -->
    <div class="cardBox teacherMain py-md-4 mb-3">
        <div class="formPanel">
            <h5 class="mb-3">{{ $heading }} Student</h5>
            <hr class="form-divider">

            <!-- Form Start -->
            <form action="{{ route('sp.student.save') }}" method="POST" id="add-plan-form" class="row g-3">
                @csrf

                <input type="hidden" name="id" id="student_id_field" value="{{ $studentData->user_id ?? '' }}">

                <div class="formPanel">
                    <div class="row">

                        {{-- NAME --}}
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="name">Name</label> <b>*</b>
                                <input type="text" name="name" id="name"
                                    class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                    value="{{ old('name', $studentData->name ?? '') }}" placeholder="Enter here">
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- EMAIL --}}
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="email">Email</label>
                                <input type="text" name="email" id="email"
                                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                    value="{{ old('email', $studentData->user->email ?? '') }}" placeholder="Enter here">
                                @error('email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- MOBILE --}}
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="mobile_no">Mobile No.</label> <b>*</b>
                                <input type="text" name="mobile_no" id="mobile_no"
                                    class="form-control mobile {{ $errors->has('mobile_no') ? 'is-invalid' : '' }}"
                                    value="{{ old('mobile_no', $studentData->user->mobile_no ?? '') }}"
                                    placeholder="Enter here">
                                @error('mobile_no')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- ADDRESS --}}
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="address">Address</label> <b>*</b>
                                <input type="text" name="address" id="address"
                                    class="form-control {{ $errors->has('address') ? 'is-invalid' : '' }}"
                                    value="{{ old('address', $studentData->address ?? '') }}" placeholder="Enter here">
                                @error('address')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- PINCODE --}}
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="pincode">Pincode</label> <b>*</b>
                                <input type="text" name="pincode" id="pincode"
                                    class="form-control {{ $errors->has('pincode') ? 'is-invalid' : '' }}"
                                    value="{{ old('pincode', $studentData->pincode ?? '') }}" placeholder="Enter here">
                                @error('pincode')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        {{-- JOINING DATE --}}
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="joining_date">Joining Date</label> <b>*</b>
                                <input type="date" name="joining_date" id="joining_date"
                                    class="form-control {{ $errors->has('joining_date') ? 'is-invalid' : '' }}"
                                    value="{{ old('joining_date', $studentData->joining_date ?? '') }}">
                                @error('joining_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                <label for="status">Status</label> <b>*</b>
                                <select name="status" id="status"
                                    class="form-select {{ $errors->has('status') ? 'is-invalid' : '' }}">
                                    <option value="">Select</option>

                                    @foreach (config('constants.STATUS_LIST') as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('status', $studentData->status ?? 1) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                <div class="offcanvas-footer">
                    <div class="d-flex align-items-center justify-content-end gap-4">
                        <a href="{{ url()->previous() }}" class="btn backbtn">Back</a>
                        <button type="submit" class="btn btn-primary-gradient rounded-1">Submit</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script>
        var notyf = new Notyf({
            duration: 2500,
            position: {
                x: 'right',
                y: 'top'
            }
        });

        notyf.success("Student created successfully!");
        notyf.error("Something went wrong!");
    </script> --}}
@endsection
