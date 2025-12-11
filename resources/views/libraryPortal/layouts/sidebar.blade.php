@php
    $dashboardRoutes = ['dashboard'];
    $isActiveDashboardMenu = isActiveMenu($dashboardRoutes);

    $studentManagerRoutes = ['student.manager','student.edit','student.add'];
    $isActiveStudentManagerMenu = isActiveMenu($studentManagerRoutes);
    
    $seatMgtRoutes = ['seat.index','seat.add','seat.edit','seat.delete'];
    $isActiveSeatMgtMenu = isActiveMenu($seatMgtRoutes);
@endphp


<div class="siderBar" id="siderBar">
    <div class="sideMenu">
        <div class="sideMenuscrl">
            <ul class="menuList">

                <li>
                    <a href="{{ route('dashboard') }}" class="{{ $isActiveDashboardMenu ? 'active' : '' }}">
                        <img src="{{ asset('frontend/images/dashboard-icon.svg') }}" width="16" class="me-2">
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="{{ route('student.manager') }}" class="{{ $isActiveStudentManagerMenu ? 'active' : '' }}">
                        <img src="{{ asset('frontend/images/students-manager-icon.svg') }}" width="16"
                            class="me-2">
                        Students Management
                    </a>
                </li>

                 <li>
                    <a href="{{ route('seat.index') }}" class="{{ $isActiveSeatMgtMenu ? 'active' : '' }}">
                        <img src="{{ asset('frontend/images/planners-manager-icon.svg') }}" width="18" class="me-2">
                        Seat Management
                    </a>
                </li>

               {{-- <li>
                    <a href="#">
                        <img src="{{ asset('frontend/images/planners-manager-icon.svg') }}" width="16"
                            class="me-2">
                        Daily Planner
                    </a>
                </li>

                <li>
                    <a href="#">
                        <img src="{{ asset('frontend/images/lesson-planners-icon.svg') }}" width="16"
                            class="me-2">
                        Lesson Plan
                    </a>
                </li>

                <li>
                    <a href="#">
                        <img src="{{ asset('frontend/images/online-classes-icon.svg') }}" width="16" class="me-2">
                        Online Classes
                    </a>
                </li>

                <li>
                    <a href="#">
                        <img src="{{ asset('frontend/images/content-upload-icon.svg') }}" width="16" class="me-2">
                        Content Upload
                    </a>
                </li>

                <li>
                    <a href="#">
                        <img src="{{ asset('frontend/images/content-upload-icon.svg') }}" width="16" class="me-2">
                        Events Media Gallery
                    </a>
                </li>

                <!-- TPG Menu -->
                <li>
                    <a href="#" class="submenuToggle" data-bs-toggle="collapse">
                        <img src="{{ asset('frontend/images/test-paper-icon.svg') }}" width="16" class="me-2">
                        Test Paper Gen. (TPG)
                    </a>
                    <div class="collapse show">
                        <ul class="submenuList">
                            <li><a href="#">Test Paper</a></li>
                            <li><a href="#">Question Bank</a></li>
                            <li><a href="#">Online Test Review</a></li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#">
                        <img src="{{ asset('frontend/images/access-code.svg') }}" width="16" class="me-2">
                        Licenses / Access Codes
                    </a>
                </li>

                <li>
                    <a href="#">
                        <img src="{{ asset('frontend/images/download-icn.svg') }}" width="16" class="me-2">
                        Download App
                    </a>
                </li> --}}

            </ul>
        </div>
    </div>
</div>
