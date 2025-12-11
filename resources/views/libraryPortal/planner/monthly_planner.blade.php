@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <div class="cardBox dailyPlanner">
        @if ($classes->unique('class_id')->isNotEmpty())
            <div class="">
                <div class="d-md-flex justify-content-between align-items-center">
                    <h2 class="fs-6 fw-semibold mb-3">Monthly Planner</h2>
                    @if (getUserRoles() == 'school_admin')
                        <button type="button" class="btn btn-primary-gradient rounded-1" data-bs-toggle="modal"
                            data-bs-target="#classesModal"> Planner Visibility For Student/Parents
                        </button>
                    @endif
                    {{-- <ul class="filterButtonUl align-items-center">
                            <li class="text-secondary ">
                                Select Planner type
                            </li>
                            <li>
                                <select class="form-select" id="selectType">
                                    <option value="all" {{ request()->query('type') === 'all' ? 'selected' : '' }}>All
                                    </option>
                                    <option value="daily" {{ request()->query('type') === 'daily' ? 'selected' : '' }}>
                                        Daily
                                    </option>
                                    <option value="weekly" {{ request()->query('type') === 'weekly' ? 'selected' : '' }}>
                                        Weekly</option>
                                    <option value="monthly" {{ request()->query('type') === 'monthly' ? 'selected' : '' }}>
                                        Monthly</option>
                                </select>
                            </li>
                        </ul> --}}
                </div>
            </div>
            <div class="tab-content mt-4" id="myTabContent">
                <div class="tab-pane fade show active" id="nurseryTab">
                    <div>
                        <div class="dailypHeader d-md-flex justify-content-end">
                            <ul class="filterButtonUl align-items-center">
                                <li class="text-secondary ">
                                    Select Month
                                </li>
                                <li>
                                    @php
                                        $selectedMonth = request()->query('month');
                                    @endphp

                                    <select class="form-select" id="monthFilter">
                                        <option value="" {{ $selectedMonth == '' ? 'selected' : '' }}>Current Month
                                        </option>
                                        @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                            <option value="{{ $month }}"
                                                {{ $selectedMonth == $month ? 'selected' : '' }}>
                                                {{ $month }}
                                            </option>
                                        @endforeach
                                    </select>
                                </li>
                            </ul>
                        </div>
                        <div class="table-responsive tbleDiv">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-start" style="vertical-align: middle;">Class</th>
                                        <th class="text-center" colspan="5">
                                            <span>Planner -
                                                {{ request()->query('month') ? request()->query('month') : \Carbon\Carbon::now()->format('F') }}
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classPlannerData as $classId => $planners)
                                        @php
                                            $class = $classes->where('class_id', $classId)->first(); // Get class name by class_id
                                        @endphp
                                        <tr>
                                            <td class="text-start fw-semibold">
                                                {{ $class->class->name ?? 'No Class Name' }}
                                            </td>
                                            @foreach ($planners as $planner)
                                                <td>
                                                    <a href="javascript:void(0)" class="open-monthly-planner-modal"
                                                        data-chapters="{{ htmlspecialchars(
                                                            json_encode([
                                                                'ids' => $planner['chapter_id'],
                                                                'titles' => array_values($planner['titles']), // Convert associative array to indexed array
                                                            ]),
                                                            ENT_QUOTES,
                                                            'UTF-8',
                                                        ) }}">
                                                        <div class="shiftBox">
                                                            <strong>{{ $planner['subject'] }}</strong>

                                                        </div>
                                                    </a>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        @else
            <div class="">
                <h2 class="fs-6 fw-semibold mb-3">Planner</h2>
                <p class="text-secondary fw-medium">Not Found</p>
            </div>
        @endif
    </div>
    <div class="modal fade plannerChapterModal" id="monthlyPlannerChapterModal" tabindex="-1"
        aria-labelledby="monthlyPlannerChapterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header align-items-start border-0">
                    <h1 class="modal-title fs-5" id="weekName">Chapters</h1>
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
    <script>
        $(document).ready(function() {
            $('.open-monthly-planner-modal').on('click', function(event) {
                event.preventDefault(); // Prevent default anchor behavior

                var button = $(this); // Get the clicked button
                var chaptersData = button.attr('data-chapters'); // Get chapters JSON string

                try {
                    var decodedData = $('<textarea/>').html(chaptersData).text();
                    var chapters = JSON.parse(decodedData);

                    // Set modal title
                    // $('#weekName').text(weekName + ' Chapters');

                    // Clear previous content in table body
                    var tableBody = $('#monthlyPlannerChapterModal tbody');
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
                    $('#monthlyPlannerChapterModal').modal('show');

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
        document.getElementById('monthFilter').addEventListener('change', function() {
            const selectedMonth = this.value;
            const url = new URL(window.location.href);
            if (selectedMonth) {
                url.searchParams.set('month', selectedMonth);
            } else {
                url.searchParams.delete('month');
            }
            window.location.href = url.toString(); // reload with filter
        });
    </script>
@endsection
