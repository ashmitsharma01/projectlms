@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <div class="row px-lg-1">
        <div class="col-lg-8 px-lg-2 mb-3">
            <div class="cardBox adminBx h-100">
                <div class="">
                    <h6>Welcome, <b class="text-primary fw-semibold"> {{ Auth::user()->name }} </b> <lottie-player
                            src="{{ asset('frontend/images/hand.json') }}" loop autoplay
                            style="width: 35px;height: 30px;"></lottie-player>
                    </h6>
                    <span>Always stay updated in your teacher portal</span>
                    <p>
                        Access real-time updates, manage activities, and streamline your tasks effortlessly. Explore
                        tools and features designed to simplify teacher administration.
                    </p>

                </div>
                <div class="position-relative">
                    <img src="{{ asset('frontend/images/admin-img2.svg') }}" alt="" width="230">
                    <lottie-player src="{{ asset('frontend/images/rocket.json') }}" background="transparent" speed="1"
                        style="width: 100px; height: 100px;position: absolute;top: -20px;left: -30px;" loop
                        autoplay></lottie-player>
                </div>
            </div>
        </div>
        <div class="col-lg-4 px-lg-2">
            <div class="cardBox countBx mb-3">
                <div class="d-flex justify-content-between align-items-center py-1">
                    <figure class="mb-0">
                        <img src="{{ asset('frontend/images/total-students-icon.svg') }}" alt="" width="70">
                    </figure>
                    <span>Total Students <b>{{ $students }}</b></span>
                </div>

            </div>
            <div class="cardBox countBx">
                <div class="d-flex justify-content-between align-items-center py-1">
                    <figure class="mb-0">
                        <img src="{{ asset('frontend/images/total-parents-icon.svg') }}" alt="">
                    </figure>
                    <span>Total Parents/Guardian <b>{{ $students }}</b></span>
                </div>

            </div>
        </div>
    </div>
    <div class="row px-lg-1">
        <div class="col-lg-5 px-lg-2 mb-3">
            <div class="cardBox">
                <div class="headingBx">
                    <h4>Planned Online Classes</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('online.class') }}" class="viewAll">View all</a>
                    </div>
                </div>
                <ul class="listingUl plannedCall">
                    @if ($plannedClasses->isEmpty())
                        <li>
                            <div class="plannedList text-center py-4">
                                <strong>
                                    <h4>No classes scheduled</h4>
                                </strong>
                            </div>
                        </li>
                    @else
                        @foreach ($plannedClasses as $class)
                            @php
                                $startTime = Carbon\Carbon::parse($class->start_time);
                                $endTime = Carbon\Carbon::parse($class->end_time);
                                $duration = $endTime->diffInMinutes($startTime);
                            @endphp
                            <li>
                                <div class="listBox">
                                    <div class="plannedMain">
                                        <div class="d-flex gap-2">
                                            <figure class="m-0">
                                                <img src="{{ asset('frontend/images/notification-img1.jpg') }}"
                                                    alt="">
                                            </figure>
                                            <div>
                                                <span>{{ $class->instructor->name }}</span>
                                                <div class="iconBtm align-items-start">
                                                    <b><img src="{{ asset('frontend/images/time-date-icon.svg') }}"
                                                            alt="" class="me-2"
                                                            width="15">{{ $class->class_date . '  ' . $class->start_time }}</b>
                                                </div>
                                                <span class="badge green">{{ $class->subject->name }}</span>
                                            </div>
                                        </div>
                                        {{-- <a href="{{ route('online.class.details', $class->id) }}"> <button type="button" class="btnremoveBg">
                                                <img src="{{ asset('frontend/images/eye-bg.svg') }}" alt=""
                                                    class="eyebtn">
                                            </button></a> --}}
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
        <div class="col-lg-7 px-lg-2 mb-3">
            <div class="cardBox">
                <div class="headingBx d-block d-md-flex justify-content-between overallSelect">
                    <h4>Class-wise count of students</h4>
                    {{-- <select class="form-select w-auto ms-auto">
                        <option selected>Select Session</option>
                    </select> --}}
                    <div class="d-flex align-items-center  gap-2 mt-3 mt-md-0">


                    </div>
                </div>
                <div id="courseStatistics" style="height: 242px;"></div>
            </div>
        </div>
    </div>
    {{-- <div class="cardBox dailyPlanner">
        @if ($classes->unique('class_id')->isNotEmpty())
            <div class="">
                <h2 class="fs-6 fw-semibold mb-3">Daily Planner</h2>
                <p class="text-secondary fw-medium">Select Class</p>
            </div>
            <ul class="nav nav-tabs classTabs" id="classTabs">
                @foreach ($classes->unique('class_id') as $item)
                    <li class="nav-item">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                            data-bs-target="#classTab{{ $item->class_id }}" type="button">
                            <span>{{ substr($item->class->name ?? 'N/A', 0, 1) }}</span>{{ $item->class->name }}
                        </button>
                    </li>
                @endforeach
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="classTab">
                    <div>
                        <div class="dailypHeader d-md-flex">
                            <div class="d-flex flex-column">
                                <span>Select Stage</span>
                            </div>
                            <ul class="filterButtonUl">
                                <ul class="filterButtonUl">
                                    <li>
                                        <button type="button" class="filterbutton active" data-scroll-target="day-1">Stage
                                            1</button>
                                    </li>
                                    <li>
                                        <button type="button" class="filterbutton" data-scroll-target="day-6">Stage
                                            2</button>
                                    </li>
                                    <li>
                                        <button type="button" class="filterbutton" data-scroll-target="day-11">Stage
                                            3</button>
                                    </li>
                                    <li>
                                        <button type="button" class="filterbutton" data-scroll-target="day-16">Stage
                                            4</button>
                                    </li>
                                </ul>
                        </div>
                        <div class="table-responsive tbleDiv plannerTblFix">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-start" style="vertical-align: middle;">Subjects</th>
                                        @foreach ($allDates as $index => $date)
                                            @php
                                                $dayName = $weekDays[$date->format('w')]; // Get weekday name
                                            @endphp
                                            <th class="day-header">
                                                <div class="d-flex justify-content-between">
                                                    <span>Day {{ $index + 1 }}
                                                        <b>{{ $dayName }}</b>
                                                    </span>
                                                    
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subjects as $subject)
                                        <tr>
                                            <td class="text-start fw-semibold">{{ $subject->subject->name }}</td>
                                            @foreach ($allDates as $index => $date)
                                                @php
                                                    $day = $index + 1; // Adjust day number for dayWiseData
                                                @endphp
                                                <td>
                                                    @if (isset($dayWiseData[$day][$subject->subject->id]))
                                                        @foreach ($dayWiseData[$day][$subject->subject->id] as $chapter)
                                                            <a href="{{ route('chapter.details', $chapter['chapter_id']) }}"
                                                                title="{{ $chapter['title'] }}">
                                                                <div class="shiftBox {{ $chapter['class'] }}">
                                                                    <strong>{{ Str::limit($chapter['title'], 20, '...') }}</strong>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    @else
                                                        <a href="javascript:void(0)">
                                                            <div class="shiftBox lightred">
                                                                <strong>No Task</strong>
                                                            </div>
                                                        </a>
                                                    @endif
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
                <h2 class="fs-6 fw-semibold mb-3">Daily Planner</h2>
                <p class="text-secondary fw-medium">Not Found</p>
            </div>
        @endif
    </div> --}}

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/variable-pie.js"></script>
    <script src="https://code.highcharts.com/modules/xrange.js"></script>

    <script>
        document.getElementById('classFilter').addEventListener('change', function() {
            const selectedClass = this.value;
            const classItems = document.querySelectorAll('#plannedClassesList li');

            classItems.forEach(item => {
                if (selectedClass === 'all' || item.dataset.classId === selectedClass) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    </script>
    <script>
        $('.alertList').slick({
            autoplay: true,
            slidesToShow: 1,
            arrows: false,
            dots: false,
            autoplaySpeed: 0,
            speed: 15000,
            cssEase: 'linear',
            variableWidth: true,
        });
        // Create the chart
        Highcharts.chart('courseStatistics', {
            chart: {
                type: 'column'
            },
            title: {
                align: 'left',
                text: null
            },
            subtitle: {
                align: 'left',
                text: null
            },
            accessibility: {
                announceNewData: {
                    enabled: true
                }
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: null
                }
            },
            legend: {
                enabled: false
            },
            tooltip: {
                headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: ' +
                    '<b>{point.y:.2f}%</b> of total<br/>'
            },
            series: [{
                name: 'Subjects',
                colorByPoint: true,
                data: @json($chartData) // Pass the PHP data to JavaScript
            }]
        });
    </script>
@endsection
