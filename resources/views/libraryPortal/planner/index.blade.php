@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <div class="cardBox dailyPlanner">
        @if (isset($classes) && $classes->unique('class_id')->isNotEmpty())
            <div class="">
                <div class="d-md-flex justify-content-between align-items-center">
                    <h2 class="fs-6 fw-semibold mb-3">
                        @if ($plannerType === 'daily')
                            Daily
                        @elseif ($plannerType === 'weekly')
                            Weekly
                        @else
                            Monthly
                        @endif Planner
                    </h2>
                    @if (getUserRoles() == 'school_admin')
                        <button type="button" class="btn btn-primary-gradient rounded-1" data-bs-toggle="modal"
                            data-bs-target="#classesModal"> Planner Visibility For Student/Parents
                        </button>
                    @endif
                </div>
                <p class="text-secondary fw-medium">Select Class</p>
            </div>
            <ul class="nav nav-tabs classTabs" id="classTabs">
                @if (isset($classes) && $classes->unique('class_id')->isNotEmpty())
                    @foreach ($classes->unique('class_id') as $item)
                        <li class="nav-item">
                            <button
                                class="nav-link {{ (request('class_id') ? request('class_id') == $item->class_id : $loop->first) ? 'active' : '' }}"
                                type="button" onclick="updateURL({{ $item->class_id }})">
                                <span>{{ substr($item->class->name ?? 'N/A', 0, 1) }}</span>{{ $item->class->name }}
                            </button>
                        </li>
                    @endforeach
                @else
                    <p class="fw-medium">Planner isn’t ready just yet!
                        Please check back soon to see it in action! </p>
                @endif
            </ul>
            @if (isset($classes) && $classes->unique('class_id')->isNotEmpty())
                @if ($plannerType === 'daily')
                    @include('libraryPortal.planner.daily_planner', [
                        'allDates' => $allDates,
                        'subjects' => $subjects,
                        'dayWiseData' => $dayWiseData,
                    ])
                @elseif ($plannerType === 'weekly')
                    @include('libraryPortal.planner.weekly_planner', [])
                @elseif ($plannerType === 'monthly')
                    @include('libraryPortal.planner.monthly_planner', [])
                @else
                    <p class="fw-medium">Your class planner isn’t ready just yet!
                        Please check back soon to see it in action! </p>
                @endif
            @else
                <div class="">
                    <h2 class="fs-6 fw-semibold mb-3">Planner</h2>
                    <p class="fw-medium">Your class planner isn’t ready just yet!
                        Please check back soon to see it in action!</p>
                </div>
            @endif
        @else
            <div class="">
                <h2 class="fs-6 fw-semibold mb-3">Planner</h2>
                <p class="fw-medium">Planner isn’t ready just yet!
                    Please check back soon to see it in action!</p>
            </div>
        @endif

    </div>

    <div class="offcanvas offcanvas-end " id="editPlanner">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fs-6 fw-semibold">Edit Planner</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body ">
            <div class="formPanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Allotted Days</label>
                            <input type="text" class="form-control" placeholder="2">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Start Date</label>
                            <input type="text" class="form-control" id="datepicker" placeholder="02/10/2023">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Completion Date</label>
                            <input type="text" class="form-control" id="datepicker1" placeholder="04/10/2023">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label>Total Periods</label>
                            <input type="text" class="form-control" placeholder="2">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="d-flex align-items-center justify-content-end gap-4">
                <button type="button" class="btn backbtn">Back</button>
                <button type="button" class="btn btn-primary-gradient rounded-1">Submit</button>
            </div>
        </div>
    </div>
    <div class="modal fade" id="statusMdl">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="text-center">
                        <lottie-player src="{{ asset('frontend/images/study-idea.json') }}" loop="" autoplay=""
                            style="width: 130px;height: 130px;margin: auto;" background="transparent"></lottie-player>
                        <h6 class="fw-semibold">Are you sure?</h6>
                        <p>Do you want to assign off on the <br> selected date</p>
                        <input type="hidden" id="modal-school-id">
                        <input type="hidden" id="modal-day-index">
                        <input type="hidden" id="modal-date">
                        <button type="button" class="btn btn-primary-gradient rounded-1"
                            id="confirmHolidayBtn">Yes</button>
                        <div>
                            <button type="button" class="btn btnNo" data-bs-dismiss="modal">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="classesModal" tabindex="-1" aria-labelledby="classesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h5 class="modal-title fs-5" id="classesModalLabel">Assigned Classes</h5>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {!! Form::open(['route' => 'sp.planner.visibilty', 'method' => 'POST', 'class' => 'needs-validation']) !!}
                <div class="modal-body p-3">
                    @php
                        $existingPlannerVisibilty = App\Models\SchoolPlannerVisibility::where(
                            'school_id',
                            Auth::id(),
                        )->get();
                        $existingTypes = $existingPlannerVisibilty->pluck('type', 'class_id')->toArray();
                        if (isset($classes)) {
                            $uniqueClasses = $classes
                                ->unique('class_id')
                                ->map(function ($planner) use ($existingTypes) {
                                    return [
                                        'id' => $planner->class_id,
                                        'class' => $planner->class->name,
                                        'type' => $existingTypes[$planner->class_id] ?? '',
                                    ];
                                });
                        } else {
                            $uniqueClasses = [];
                        }
                    @endphp
                    @foreach ($uniqueClasses as $class)
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    {!! Form::label('classes[' . $class['id'] . '][name]', 'Class', ['class' => 'form-label small fw-semibold']) !!}
                                    {!! Form::select('classes[' . $class['id'] . '][name]', [$class['id'] => $class['class']], $class['id'], [
                                        'class' => 'form-control form-control-sm',
                                        'disabled' => true,
                                    ]) !!}
                                    <!-- Hidden field to store class ID -->
                                    {!! Form::hidden('classes[' . $class['id'] . '][id]', $class['id']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-2">
                                    {!! Form::label('classes[' . $class['id'] . '][type]', 'Type', ['class' => 'form-label small fw-semibold']) !!}
                                    {!! Form::select(
                                        'classes[' . $class['id'] . '][type]',
                                        config('constants.PLANNER_VISIBILTY'),
                                        $class['type'] ?? null,
                                        [
                                            'class' => 'form-control form-control-sm',
                                            'placeholder' => '--Select--',
                                        ],
                                    ) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3 rounded-1"
                        data-bs-dismiss="modal">Close</button>
                    {!! Form::submit('Save', ['class' => 'btn btn-primary-gradient px-3 rounded-1 addBtn ']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade plannerChapterModal" id="weeklyPlannerChapterModal" tabindex="-1"
        aria-labelledby="weeklyPlannerChapterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header align-items-start border-0">
                    <h1 class="modal-title fs-5" id="weekName"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive tbleDiv ">
                        <table class="table ">
                            <thead>
                                <tr>
                                    <th>Chapter No.</th>
                                    <th>Chapter Name</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <div class="d-flex align-items-center myCourseLft">
                                            <div class="coursesName ps-0">
                                                <h3>ABC</h3>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="" class="bg-transparent border-0 p-0">
                                            <img src="/frontend/images/icon-eye.svg" alt="" width="28">
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('th[class*="currentBg"], th span').on('click', function() {
                var weekNumber = $(this).text().trim().replace('Week ', ''); // Extract week number

                // Remove active class from all headers and add to clicked one
                $('th').removeClass('active-week');
                $(this).closest('th').addClass('active-week');

                // Scroll to the corresponding column
                var targetColumn = $('td[data-week-number="' + weekNumber + '"]');
                if (targetColumn.length) {
                    $('html, body').animate({
                        scrollLeft: targetColumn.offset().left -
                            100 // Adjust offset for better view
                    }, 800);
                }
            });
            $('.open-weekly-planner-modal').on('click', function(event) {
                event.preventDefault(); // Prevent default anchor behavior

                var button = $(this); // Get the clicked button
                var weekNumber = button.data('week-number');
                var weekName = button.data('week-name');
                var chaptersData = button.attr('data-chapters'); // Get chapters JSON string

                try {
                    var decodedData = $('<textarea/>').html(chaptersData).text();
                    var chapters = JSON.parse(decodedData);

                    // Set modal title
                    $('#weekName').text(weekName + ' Chapters');

                    // Clear previous content in table body
                    var tableBody = $('#weeklyPlannerChapterModal tbody');
                    tableBody.empty();

                    // Iterate through chapters and add rows to the table
                    chapters.ids.forEach(function(chapterId, index) {
                        var chapterName = chapters.titles[index] || 'No Title';

                        var tableRow = `
                    <tr>
                        <td>${index + 1}</td> 
                        <td>
                            <div class="d-flex align-items-center myCourseLft">
                                <div class="coursesName ps-0">
                                    <h3>${chapterName}</h3>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="/school-portal/chapter/details/${chapterId}" class="bg-transparent border-0 p-0">
                                <img src="/frontend/images/icon-eye.svg" alt="View Chapter" width="28">
                            </a>
                        </td>
                    </tr>`;

                        tableBody.append(tableRow);
                    });

                    // Manually show the modal
                    $('#weeklyPlannerChapterModal').modal('show');

                } catch (error) {
                    console.error('Error parsing JSON:', error);
                }
            });
        });

        $(document).ready(function() {
            $('#selectType').on('change', function() {
                var selectedType = $(this).val();
                var classId = getParameterByName('class_id');

                updateURL(classId, selectedType);
            });
        });

        function updateURL(classId, typeValue) {
            const url = new URL(window.location.href);

            if (typeValue && typeValue !== 'all') {
                url.searchParams.set('type', typeValue);
                url.searchParams.delete('class_id');
            } else if (classId) {
                url.searchParams.set('class_id', classId);
                url.searchParams.delete('type');
            }

            window.location.href = url.toString();
        }

        // Helper function to get query parameters from URL
        function getParameterByName(name) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(name);
        }
    </script>
    <script>
        // When the modal is opened, store the necessary data in a global variable
        $(document).ready(function() {
            // Listen for when the modal is triggered
            $('#statusMdl').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget); // The button that triggered the modal

                // Get data from the button's attributes
                var schoolId = button.data('school-id');
                var dayIndex = button.data('day-index');
                var selectedDate = button.data('date');

                // Set the hidden input fields in the modal
                $('#modal-school-id').val(schoolId);
                $('#modal-day-index').val(dayIndex);
                $('#modal-date').val(selectedDate);

                // console.log("School ID:", schoolId);
                // console.log("Day Index:", dayIndex);
                // console.log("Selected Date:", selectedDate);
            });

            // When the "Yes" button is clicked in the modal, make the API request
            $('#confirmHolidayBtn').click(function() {
                // Get the values from the hidden input fields
                var schoolId = $('#modal-school-id').val();
                var dayIndex = $('#modal-day-index').val();
                var selectedDate = $('#modal-date').val();

                // Make the AJAX request
                $.ajax({
                    url: '{{ route('daily.planner.mark.holiday') }}', // Define the route for marking a holiday
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        holiday_date: selectedDate,
                        school_id: schoolId,
                        day_index: dayIndex // Send the correct day index
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Holiday successfully assigned!');
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert('Something went wrong!');
                        }
                    }
                });
            });
        });
    </script>
@endsection
