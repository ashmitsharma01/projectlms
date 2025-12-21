@extends('libraryPortal.layouts.master')
@section('content')
    @include('libraryPortal.layouts.flash-messages')
    <style>
        .headerTbl {
            height: 50px;
            width: 1381px;
            margin-left: 9px;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .blink {
            animation: blinkBg 1s infinite;
        }

        @keyframes blinkBg {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
    <div class="row px-lg-1">
        <div class="headerTbl">
            <h6 class="m-0">Seats Management</h6>
        </div>
        <div class="col-lg-12 px-lg-2">
            <div class="row px-md-1">
                <div class="col-md-3 px-md-2 mb-3">
                    <div class="cardBox countBx h-100">
                        <div class="d-flex justify-content-between">
                            <figure>
                                <img src="{{ asset('frontend/images/total-student-icon.svg') }}" alt=""
                                    width="70">
                            </figure>
                            <span>Fees Collected<b>₹{{ $totalFeesCollected }}</b></span>
                        </div>
                        <p>
                            <img src="{{ asset('frontend/images/higher-icon.svg') }}" alt="" width="14"
                                class="me-2">
                            12% Higher than Last Month
                        </p>
                    </div>
                </div>

                <div class="col-md-3 px-md-2 mb-3">
                    <div class="cardBox countBx h-100">
                        <div class="d-flex justify-content-between">
                            <figure>
                                <img src="{{ asset('frontend/images/total-teachers-icon.svg') }}" alt="">
                            </figure>
                            <span>Expected Fees(This Month)<b>₹{{ $expectedFeesThisMonth }}</b></span>
                        </div>
                        @if ($expectedFeesUserCount != 0)
                            <p>
                                <img src="{{ asset('frontend/images/higher-icon.svg') }}" alt="" width="14"
                                    class="me-2">
                                Total {{ $expectedFeesUserCount }} Students
                            </p>
                        @endif
                    </div>
                </div>
                <div class="col-md-3 px-md-2 mb-3">
                    <div class="cardBox countBx h-100">
                        <div class="d-flex justify-content-between">
                            <figure>
                                <img src="{{ asset('frontend/images/total-teachers-icon.svg') }}" alt="">
                            </figure>
                            <span>Today's Income<b>{{ $todaysIncome }}</b></span>
                        </div>
                        @if ($todaysIncomeCount != 0)
                            <p>
                                <img src="{{ asset('frontend/images/higher-icon.svg') }}" alt="" width="14"
                                    class="me-2">
                                Total {{ $todaysIncomeCount }} Students
                            </p>
                        @endif
                    </div>
                </div>

                <div class="col-md-3 px-md-2 mb-3">
                    <div class="cardBox countBx h-100">
                        <div class="d-flex justify-content-between">
                            <figure>
                                <img src="{{ asset('frontend/images/digital-content.svg') }}" alt="">
                            </figure>
                            <span>Upcoming Renewals (7 Days)<b>{{ $upcomingRenewalsAmount }}</b></span>
                        </div>
                        @if ($upcomingRenewalsCount != 0)
                            <p>
                                <img src="{{ asset('frontend/images/higher-icon.svg') }}" alt="" width="14"
                                    class="me-2">
                                Total {{ $upcomingRenewalsCount }} Students
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row px-lg-1">
        <div class="col-lg-6 px-lg-2 mb-3">
            <div class="cardBox">
                <div class="headingBx">
                    <h4>Recent Payments</h4>

                    {{-- <div class="d-flex gap-2">
                        <select id="classFilter" class="form-select">
                            <option value="all" selected>Select Class</option>
                            <option value="1">Class 1</option>
                            <option value="2">Class 2</option>
                            <option value="3">Class 3</option>
                        </select>
                    </div> --}}
                </div>

                <ul id="plannedClassesList" class="classesUl">
                    @forelse($recentPayments as $payment)
                        @php
                            $name = $payment->user->name ?? 'User';
                            $initial = strtoupper(substr($name, 0, 1));

                            $colors = ['#4f46e5', '#16a34a', '#ea580c', '#7c3aed', '#dc2626'];
                            $bgColor = $colors[$payment->user->id % count($colors)];
                        @endphp

                        <li>
                            <div class="plannedList">
                                <div class="d-flex planUser gap-2 align-items-center">
                                    <div
                                        style="width:40px;height:40px;border-radius:50%;background-color:{{ $bgColor }};color:#fff;display:flex;
                                                align-items:center;justify-content:center;font-weight:600;font-size:16px; ">
                                        {{ $initial }}
                                    </div>

                                    <div>
                                        <h4>{{ $name }}</h4>
                                    </div>
                                </div>

                                <strong>
                                    <b>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y h:i A') }}</b>
                                </strong>

                                <strong>
                                    <b style="margin-left:45px; color:#30C768;">
                                        ₹{{ number_format($payment->amount) }}
                                    </b>
                                </strong>
                            </div>
                        </li>
                    @empty
                        <li>
                            <div class="plannedList">
                                <p class="text-muted m-0">No recent payments found</p>
                            </div>
                        </li>
                    @endforelse


                </ul>
            </div>
        </div>

        <div class="col-lg-6 px-lg-2 mb-3">
            <div class="cardBox">
                <div class="headingBx">
                    <h4>Month wise Payment States</h4>
                </div>
                <div id="studentCount" style="height: 255px;"></div>
            </div>
        </div>
        <div class="col-lg-12 px-lg-2 mb-3">
            <div class="cardBox">
                <div class="headingBx">
                    <h4>Expiring in 7 days</h4>
                </div>
                <div class="table-responsive tbleDiv">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Admission No.</th>
                                <th>Name</th>
                                <th>Last Payment Date</th>
                                <th>Next Payment Date</th>
                                <th>Days Left</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                            @forelse($expiringStudents as $seat)
                                @php
                                    $lastPayment = $seat->user->payments->first();
                                    $daysLeft = \Carbon\Carbon::today()->diffInDays($seat->end_date, false);
                                @endphp

                                <tr>
                                    <td>{{ $seat->user->student->admission_no }}</td>
                                    <td>
                                        <span class="nameTbl student-name">
                                            {{ $seat->user->name }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $lastPayment ? \Carbon\Carbon::parse($lastPayment->payment_date)->format('d-m-Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($seat->end_date)->format('d-m-Y') ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="deactiveTxt {{ $daysLeft == 0 ? 'blink' : '' }}">
                                            {{ $daysLeft }} Day{{ $daysLeft > 1 ? 's' : '' }} Left
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No students expiring in next 7 days
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/variable-pie.js"></script>
    <script src="https://code.highcharts.com/modules/xrange.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.chart('studentCount', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: [
                        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                    ],
                    crosshair: true
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Monthly Earnings (₹)'
                    }
                },
                tooltip: {
                    shared: true,
                    valuePrefix: '₹'
                },
                plotOptions: {
                    column: {
                        borderRadius: 5,
                        pointPadding: 0.2,
                        groupPadding: 0.1
                    }
                },
                series: [{
                    name: 'Fees Collected',
                    data: @json($monthlyPayments)
                }],
                credits: {
                    enabled: false
                }
            });
        });
    </script>
@endsection
