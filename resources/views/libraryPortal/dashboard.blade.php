@extends('libraryPortal.layouts.master')
@section('content')
    @include('libraryPortal.layouts.flash-messages')
    <div class="row px-lg-1">
        <div class="col-lg-6 px-lg-2 mb-3">
            <div class="cardBox adminBx h-100">
                <div class="">
                    <h6>Hi, John Doe
                        <lottie-player src="{{ asset('frontend/images/hand.json') }}" loop autoplay
                            style="width: 35px;height: 30px;"></lottie-player>
                    </h6>
                    <span>Stay Informed with Your School Admin Portal</span>
                    <p>
                        Access real-time updates, manage activities, and streamline your tasks effortlessly. Explore
                        tools and features designed to simplify school administration.
                    </p>
                </div>
                <img src="{{ asset('frontend/images/admin-img.png') }}" alt="" width="200">
            </div>
        </div>

        <!-- STATIC COUNTERS -->
        <div class="col-lg-6 px-lg-2">
            <div class="row px-md-1">
                <div class="col-md-6 px-md-2 mb-3">
                    <div class="cardBox countBx h-100">
                        <div class="d-flex justify-content-between">
                            <figure>
                                <img src="{{ asset('frontend/images/total-student-icon.svg') }}" alt=""
                                    width="70">
                            </figure>
                            <span>Total Students <b>450</b></span>
                        </div>
                        <p>
                            <img src="{{ asset('frontend/images/higher-icon.svg') }}" alt="" width="14"
                                class="me-2">
                            12% Higher than Last Month
                        </p>
                    </div>
                </div>

                <div class="col-md-6 px-md-2 mb-3">
                    <div class="cardBox countBx h-100">
                        <div class="d-flex justify-content-between">
                            <figure>
                                <img src="{{ asset('frontend/images/total-teachers-icon.svg') }}" alt="">
                            </figure>
                            <span>Total Teachers <b>32</b></span>
                        </div>
                        <p>
                            <img src="{{ asset('frontend/images/less-icon.svg') }}" alt="" width="14"
                                class="me-2">
                            4% Less than Last Month
                        </p>
                    </div>
                </div>

                <div class="col-md-6 px-md-2 mb-3">
                    <div class="cardBox countBx h-100">
                        <div class="d-flex justify-content-between">
                            <figure>
                                <img src="{{ asset('frontend/images/digital-content.svg') }}" alt="">
                            </figure>
                            <span>Digital Content <b>125</b></span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 px-md-2 mb-3">
                    <div class="cardBox countBx h-100">
                        <div class="d-flex justify-content-between">
                            <figure>
                                <img src="{{ asset('frontend/images/available-access-icon.svg') }}" alt="">
                            </figure>
                            <span>Licenses/ Access Codes <br>
                                Teachlite: <strong class="accessCodeCount">120</strong> <br>
                                MittsureLens: <strong class="accessCodeCount">80</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SECOND SECTION -->
    <div class="row px-lg-1">
        <div class="col-lg-6 px-lg-2 mb-3">
            <div class="cardBox">
                <div class="headingBx">
                    <h4>Planned Online Classes</h4>

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

                    <!-- STATIC ITEM 1 -->
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

                </ul>
            </div>
        </div>

        <div class="col-lg-6 px-lg-2 mb-3">
            <div class="cardBox">
                <div class="headingBx">
                    <h4>Student Count</h4>
                </div>
                <div id="studentCount" style="height: 240px;"></div>
            </div>
        </div>
    </div>






    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script> --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/variable-pie.js"></script>
    <script src="https://code.highcharts.com/modules/xrange.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
{{-- 
    <script>
        document.getElementById('downloadButton').addEventListener('click', function() {
            // Capture the whole dashboardMain, including scrolled content
            html2canvas(document.querySelector(".dashboardMain"), {
                scrollX: 0,
                scrollY: -window.scrollY,
                useCORS: true,
                onrendered: function(canvas) {
                    let imgData = canvas.toDataURL("image/png");
                    let link = document.createElement('a');
                    link.href = imgData;
                    link.download = 'dashboard.png';
                    link.click();
                }
            });
        });


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
    </script>

    <script>
        // Convert PHP collection to JS object
        const studentsData = @json($studentsPerMonth);

        // Map month numbers to month names (e.g., 1 → "Jan")
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        // Prepare data for Highcharts (fill missing months with 0)
        const chartData = Array(12).fill(0).map((_, index) => {
            const month = index + 1; // Months are 1-12
            return studentsData[month] || 0; // Default to 0 if no data
        });

        // Now create your chart
        document.addEventListener('DOMContentLoaded', function() {
            const yearlyTotal = chartData.reduce((sum, count) => sum + count, 0);

            Highcharts.chart('studentCount', {
                chart: {
                    type: 'column',
                    backgroundColor: 'transparent',
                    events: {
                        load: function() {
                            // Add total label in top-right corner
                            this.renderer.label(
                                    `Total: <b>${yearlyTotal}</b> students`,
                                    this.chartWidth - 120,
                                    15,
                                    undefined,
                                    undefined,
                                    undefined,
                                    true
                                )
                                .css({
                                    fontSize: '13px'
                                })
                                .add();
                        }
                    }
                },
                title: {
                    text: null
                },
                subtitle: {
                    text: `Student Enrollment (${new Date().getFullYear()})`,
                    align: 'left'
                },
                xAxis: {
                    categories: monthNames
                },
                yAxis: {
                    title: {
                        text: 'Number of Students'
                    }
                },
                plotOptions: {
                    column: {
                        color: '#fabc5b',
                        borderRadius: 3,
                        dataLabels: {
                            enabled: true,
                            format: '{y}',
                            style: {
                                textOutline: 'none'
                            }
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    formatter: function() {
                        return `
                    <b>${monthNames[this.x]}</b><br>
                    New Students: <b>${this.points[0].y}</b><br>
                    Cumulative Total: <b>${this.points[0].total}</b>
                `;
                    }
                },
                series: [{
                    name: 'Monthly',
                    data: chartData.map(point => ({
                        y: point,
                        total: yearlyTotal // Pass total for tooltip
                    })),
                    showInLegend: false
                }]
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const filter = document.getElementById('classFilter');
                const classList = document.getElementById('plannedClassesList');

                if (!filter || !classList) {
                    throw new Error('Required filter elements not found');
                }

                filter.addEventListener('change', function() {
                    const selectedClass = this.value;
                    const items = classList.querySelectorAll('li[data-class-id]');

                    items.forEach(item => {
                        const shouldShow = selectedClass === 'all' ||
                            item.dataset.classId === selectedClass.toString();
                        item.style.display = shouldShow ? 'block' : 'none';
                    });
                });

                // Initialize filter on load
                filter.dispatchEvent(new Event('change'));
            } catch (error) {
                console.error('Filter initialization failed:', error);
            }
        });
    </script> --}}
@endsection
