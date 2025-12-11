@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    @php
        $flag = 0;
        $heading = 'Add';
        if (isset($data) && !empty($data)) {
            $flag = 1;
            $heading = 'Update';
            $isDisabled = in_array($data->id, $testParticipent);
        }
    @endphp
    <div class="cardBox teacherMain pt-md-4 pb-0  mb-3">
        <div class="row ">
            <div class="col-md-12 mb-3">
                <div class="teacherLeft">
                    <h5 class="fw-semibold mb-3">Test Paper Generator ({{ $heading }})</h5>
                    <hr class="form-divider">
                </div>
            </div>

            <div class="formPanel">
                @if ($flag == 1)
                    {{ Form::model($data, ['url' => route('sp.test-papers.save'), 'id' => 'edit-plan-form', 'class' => 'row g-3', 'files' => true]) }}
                    {{ Form::hidden('id', null) }}
                @else
                    {{ Form::open(['url' => route('sp.test-papers.save'), 'id' => 'add-plan-form', 'class' => 'row g-3', 'files' => true]) }}
                @endif
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('paper_type', 'Paper Type', ['class' => 'form-label required ']) !!}
                        {!! Form::select('paper_type', config('constants.PAPER_TYPE') ?? [], null, [
                            'class' => 'form-control form-select fs-8',
                            'placeholder' => '--Select--',
                            'id' => 'paper_type_select', // <-- Add ID for JS
                            'disabled' => isset($isDisabled) && $isDisabled ? 'disabled' : null,
                        ]) !!}
                        @if ($flag === 1 && !empty($isDisabled))
                            {!! Form::hidden('paper_type', null) !!}
                        @endif

                        @error('paper_type')
                            <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('test_term', 'Test Term', ['class' => 'form-label required']) !!}
                        {!! Form::text('test_term', null, [
                            'class' => 'form-control fs-8',
                            'placeholder' => 'Enter Test Term',
                            'id' => 'chapter_select',
                            'disabled' => isset($isDisabled) && $isDisabled ? 'disabled' : null,
                        ]) !!}
                        @if ($flag === 1 && !empty($isDisabled))
                            {!! Form::hidden('test_term', null) !!}
                        @endif
                        @error('chapter_ids')
                            <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('class_id', 'Class', ['class' => 'form-label required ']) !!}
                        {!! Form::select('class_id', $class ?? [], null, [
                            'class' => 'form-control form-select fs-8 ',
                            'placeholder' => '--Select--',
                            'disabled' => isset($isDisabled) && $isDisabled ? 'disabled' : null,
                        ]) !!}
                        @if ($flag === 1 && !empty($isDisabled))
                            {!! Form::hidden('class_id', null) !!}
                        @endif

                        @error('class_id')
                            <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('subject_id', 'Subject', ['class' => 'form-label required ']) !!}
                        {!! Form::select('subject_id', $subject ?? [], null, [
                            'class' => 'form-control form-select fs-8 ',
                            'placeholder' => '--Select--',
                            'disabled' => isset($isDisabled) && $isDisabled ? 'disabled' : null,
                        ]) !!}
                        @if ($flag === 1 && !empty($isDisabled))
                            {!! Form::hidden('subject_id', null) !!}
                        @endif

                        @error('subject_id')
                            <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('title', 'Test Title', ['class' => 'form-label required ']) !!}
                        {!! Form::text('title', null, [
                            'class' => 'form-control  ',
                            'placeholder' => 'Enter title',
                            'disabled' => isset($isDisabled) && $isDisabled ? 'disabled' : null,
                        ]) !!}
                        @if ($flag === 1 && !empty($isDisabled))
                            {!! Form::hidden('title', null) !!}
                        @endif

                        @error('title')
                            <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('description', 'Test Description', ['class' => 'form-label required ']) !!}
                        {!! Form::text('description', null, [
                            'class' => 'form-control  ',
                            'placeholder' => 'Enter description',
                        ]) !!}
                        @error('description')
                            <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div id="onlineFields" style="display:none;">
                    <div class="row g-3">

                        @php
                            $startDateTime = old(
                                'start_date_time',
                                isset($data->start_date_time)
                                    ? \Carbon\Carbon::parse($data->start_date_time)->format('Y-m-d\TH:i')
                                    : null,
                            );
                            $endDateTime = old(
                                'start_date_time',
                                isset($data->end_date_time)
                                    ? \Carbon\Carbon::parse($data->end_date_time)->format('Y-m-d\TH:i')
                                    : null,
                            );
                        @endphp
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('start_date_time', 'Start Date Time', ['class' => 'form-label required']) !!}
                                {!! Form::datetimeLocal('start_date_time', old('start_date_time', $startDateTime ?? null), [
                                    'class' => 'form-control',
                                    'id' => 'start_date_time',
                                ]) !!}

                                @error('start_date_time')
                                    <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('end_date_time', 'End Date Time', ['class' => 'form-label required']) !!}
                                {!! Form::datetimeLocal('end_date_time', old('start_date_time', $endDateTime ?? null), [
                                    'class' => 'form-control',
                                    'id' => 'end_date_time',
                                ]) !!}
                                @error('end_date_time')
                                    <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('duration', 'Duration (Time in minutes)', ['class' => 'form-label required']) !!}
                                {!! Form::text('duration', null, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Enter Duration in minutes',
                                ]) !!}
                                @error('duration')
                                    <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('min_passing_percentage', 'Min Passing percentage', ['class' => 'form-label required ']) !!}
                                {!! Form::text('min_passing_percentage', null, [
                                    'class' => 'form-control  ',
                                    'placeholder' => 'Enter Minimum Passing Percentage (Do not use the % symbol)',
                                ]) !!}
                                @error('min_passing_percentage')
                                    <span class="text-danger" style="font-size: 13px;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('question_order_type', 'Question Order Type', ['class' => 'form-label required ']) !!}
                        {!! Form::select('question_order_type', config('constants.QUESTION_ORDER_TYPE'), null, [
                            'class' => 'form-control form-select fs-8 ',
                            'placeholder' => '--Select--',
                            'required',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('logo', 'Logo ', ['class' => 'form-label']) !!}
                        <!-- Instruction text -->
                        <small class="form-text text-muted">
                            (To include your school logo on printed papers, please select your logo for the PDF.)
                        </small>
                        {!! Form::file('logo', [
                            'class' => 'form-control fs-8',
                            'placeholder' => '--Select--',
                        ]) !!}

                        @if (!empty($data->logo) && Storage::disk('public')->exists($data->logo))
                            <div class="mt-2">
                                <img src="{{ Storage::url($data->logo) }}" alt="Logo"
                                    style="max-width: 150px; max-height: 150px;">
                            </div>
                        @endif
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('is_active', 'Status', ['class' => 'form-label required ']) !!}
                        {!! Form::select('is_active', config('constants.STATUS_LIST'), null, [
                            'class' => 'form-control form-select fs-8 ',
                            'placeholder' => '--Select--',
                            'required',
                            'disabled' => isset($isDisabled) && $isDisabled ? 'disabled' : null,
                        ]) !!}
                        @if ($flag === 1 && !empty($isDisabled))
                            {!! Form::hidden('is_active', null) !!}
                        @endif
                    </div>
                </div>
                <div class="offcanvas-footer">
                    <div class="d-flex align-items-center justify-content-end gap-4">
                        <button type="Submit" class="btn btn-primary-gradient rounded-1">Submit</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const paperTypeSelect = document.getElementById('paper_type_select');
            const onlineFields = document.getElementById('onlineFields');

            function toggleFields() {
                const value = paperTypeSelect.value?.toLowerCase();
                if (value === 'online' || !value) {
                    onlineFields.style.display = 'block';
                } else {
                    onlineFields.style.display = 'none';
                }
            }

            // Initial check (for edit mode)
            toggleFields();

            // On change
            paperTypeSelect.addEventListener('change', toggleFields);
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const startDateTime = document.getElementById("start_date_time");
            const endDateTime = document.getElementById("end_date_time");

            if (startDateTime && endDateTime) {
                // Initialize min end date if start date is already set (edit mode)
                if (startDateTime.value) {
                    endDateTime.min = startDateTime.value;
                }

                // When start date changes
                startDateTime.addEventListener("change", function() {
                    endDateTime.min = startDateTime.value;
                    if (endDateTime.value) {
                        validateDateTime();
                    }
                });

                // When end date changes
                endDateTime.addEventListener("change", validateDateTime);

                function validateDateTime() {
                    if (!startDateTime.value || !endDateTime.value) return;

                    const start = new Date(startDateTime.value);
                    const end = new Date(endDateTime.value);

                    // Convert to timestamps for comparison
                    const startTimestamp = start.getTime();
                    const endTimestamp = end.getTime();

                    // Case 1: End datetime is before or equal to start datetime
                    if (endTimestamp <= startTimestamp) {
                        showError("End date and time must be after start date and time");
                        return;
                    }
                    // Case 2: Same date but end time is before start time
                    if (isSameDate(start, end)) {

                        const startTime = start.getHours() * 3600 + start.getMinutes() * 60;
                        const endTime = end.getHours() * 3600 + end.getMinutes() * 60;

                        if (endTime < startTime) {
                            showError("End time must be after start time when dates are the same");
                            return;
                        }
                    }
                }

                function isSameDate(date1, date2) {
                    return date1.getFullYear() === date2.getFullYear() &&
                        date1.getMonth() === date2.getMonth() &&
                        date1.getDate() === date2.getDate();
                }

                function showError(message) {
                    alert(message);
                    endDateTime.value = "";
                    endDateTime.min = startDateTime.value;
                    endDateTime.focus();
                }
            }
        });
    </script>
@endsection
