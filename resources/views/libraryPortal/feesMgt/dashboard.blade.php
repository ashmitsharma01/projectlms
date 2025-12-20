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
                            <span>Total Fees <b>450</b></span>
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
                            <span>Pending Students <b>32</b></span>
                        </div>
                        <p>
                            <img src="{{ asset('frontend/images/less-icon.svg') }}" alt="" width="14"
                                class="me-2">
                            4% Less than Last Month
                        </p>
                    </div>
                </div>
                <div class="col-md-3 px-md-2 mb-3">
                    <div class="cardBox countBx h-100">
                        <div class="d-flex justify-content-between">
                            <figure>
                                <img src="{{ asset('frontend/images/total-teachers-icon.svg') }}" alt="">
                            </figure>
                            <span>Expired Students <b>32</b></span>
                        </div>
                        <p>
                            <img src="{{ asset('frontend/images/less-icon.svg') }}" alt="" width="14"
                                class="me-2">
                            4% Less than Last Month
                        </p>
                    </div>
                </div>

                <div class="col-md-3 px-md-2 mb-3">
                    <div class="cardBox countBx h-100">
                        <div class="d-flex justify-content-between">
                            <figure>
                                <img src="{{ asset('frontend/images/digital-content.svg') }}" alt="">
                            </figure>
                            <span>Expiring in 7 days <b>125</b></span>
                        </div>
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

                    <div class="d-flex gap-2">
                        <select id="classFilter" class="form-select">
                            <option value="all" selected>Select Class</option>
                            <option value="1">Class 1</option>
                            <option value="2">Class 2</option>
                            <option value="3">Class 3</option>
                        </select>
                    </div>
                </div>

                <ul id="plannedClassesList" class="classesUl">
                    <li data-class-id="1">
                        <div class="plannedList">
                            <div class="d-flex planUser gap-2">
                                <figure>
                                    <img src="{{ asset('frontend/images/gallery1.jpg') }}" alt="">
                                </figure>
                                <div>
                                    <h4>Science Live Class</h4>
                                    <span>
                                        <img src="{{ asset('frontend/images/list-profile.jpg') }}" alt="">
                                        Jane Smith
                                    </span>
                                </div>
                            </div>
                            <strong>Duration <b>45 Min.</b></strong>
                            <strong>Scheduled Time <b>2025-01-12 10:00 AM</b></strong>
                        </div>
                    </li>

                    <!-- STATIC ITEM 2 -->
                    <li data-class-id="2">
                        <div class="plannedList">
                            <div class="d-flex planUser gap-2">
                                <figure>
                                    <img src="{{ asset('frontend/images/gallery1.jpg') }}" alt="">
                                </figure>
                                <div>
                                    <h4>Mathematics Revision</h4>
                                    <span>
                                        <img src="{{ asset('frontend/images/list-profile.jpg') }}" alt="">
                                        Rahul Verma
                                    </span>
                                </div>
                            </div>
                            <strong>Duration <b>60 Min.</b></strong>
                            <strong>Scheduled Time <b>2025-01-14 03:00 PM</b></strong>
                        </div>
                    </li>
                    <li data-class-id="2">
                        <div class="plannedList">
                            <div class="d-flex planUser gap-2">
                                <figure>
                                    <img src="{{ asset('frontend/images/gallery1.jpg') }}" alt="">
                                </figure>
                                <div>
                                    <h4>Mathematics Revision</h4>
                                    <span>
                                        <img src="{{ asset('frontend/images/list-profile.jpg') }}" alt="">
                                        Rahul Verma
                                    </span>
                                </div>
                            </div>
                            <strong>Duration <b>60 Min.</b></strong>
                            <strong>Scheduled Time <b>2025-01-14 03:00 PM</b></strong>
                        </div>
                    </li>
                    <li data-class-id="2">
                        <div class="plannedList">
                            <div class="d-flex planUser gap-2">
                                <figure>
                                    <img src="{{ asset('frontend/images/gallery1.jpg') }}" alt="">
                                </figure>
                                <div>
                                    <h4>Mathematics Revision</h4>
                                    <span>
                                        <img src="{{ asset('frontend/images/list-profile.jpg') }}" alt="">
                                        Rahul Verma
                                    </span>
                                </div>
                            </div>
                            <strong>Duration <b>60 Min.</b></strong>
                            <strong>Scheduled Time <b>2025-01-14 03:00 PM</b></strong>
                        </div>
                    </li>
                    <li data-class-id="2">
                        <div class="plannedList">
                            <div class="d-flex planUser gap-2">
                                <figure>
                                    <img src="{{ asset('frontend/images/gallery1.jpg') }}" alt="">
                                </figure>
                                <div>
                                    <h4>Mathematics Revision</h4>
                                    <span>
                                        <img src="{{ asset('frontend/images/list-profile.jpg') }}" alt="">
                                        Rahul Verma
                                    </span>
                                </div>
                            </div>
                            <strong>Duration <b>60 Min.</b></strong>
                            <strong>Scheduled Time <b>2025-01-14 03:00 PM</b></strong>
                        </div>
                    </li>
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
                                <th>Admission Date</th>
                                <th>Mobile No.</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">

                            <tr>
                                <td>LIB-001</td>
                                <td>
                                    <span class="nameTbl student-name">
                                        <img src="{{ asset('frontend/images/default-image.jpg') }}" alt="">
                                        Rahul Sharma
                                    </span>
                                </td>
                                <td>01/01/2025</td>
                                <td>9876543210</td>
                                <td><span class="activeTxt">Active</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="#" class="btn p-0 bg-transparent border-0 text-primary">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </a>
                                        <button class="btn p-0 bg-transparent border-0 text-danger">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>LIB-002</td>
                                <td>
                                    <span class="nameTbl student-name">
                                        <img src="{{ asset('frontend/images/default-image.jpg') }}" alt="">
                                        Aman Verma
                                    </span>
                                </td>
                                <td>05/01/2025</td>
                                <td>9123456780</td>
                                <td><span class="activeTxt">Active</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="#" class="btn p-0 bg-transparent border-0 text-primary">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </a>
                                        <button class="btn p-0 bg-transparent border-0 text-danger">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>LIB-003</td>
                                <td>
                                    <span class="nameTbl student-name">
                                        <img src="{{ asset('frontend/images/default-image.jpg') }}" alt="">
                                        Neha Gupta
                                    </span>
                                </td>
                                <td>10/01/2025</td>
                                <td>9988776655</td>
                                <td><span class="deactiveTxt">Inactive</span></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="#" class="btn p-0 bg-transparent border-0 text-primary">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </a>
                                        <button class="btn p-0 bg-transparent border-0 text-danger">
                                            <i class="bi bi-trash fs-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
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
                        text: 'Number of Students'
                    }
                },
                tooltip: {
                    shared: true,
                    valueSuffix: ' students'
                },
                plotOptions: {
                    column: {
                        borderRadius: 5,
                        pointPadding: 0.2,
                        groupPadding: 0.1
                    }
                },
                series: [{
                    name: 'Students',
                    data: [120, 135, 150, 165, 180, 200, 210, 205, 195, 185, 170, 160]
                }],
                credits: {
                    enabled: false
                }
            });
        });
    </script>
@endsection
