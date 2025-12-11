<header class="dashboardHead">
    <div class="leftItem">
        <a href="javascript:void(0)"><img src="{{ asset('frontend/images/mittlearn-logo.svg') }}" alt=""
                width="130"></a>

    </div>
    <div class="rightItem">
        <button type="button" class="toggleBtn">
            <img src="{{ asset('frontend/images/toggletop-icon.svg') }}" alt="" width="16" class="me-md-3">
        </button>

        <div class="alertsSec d-lg-block d-none @if (Session::has('admin_id')) alertWhenAdmin @endif">
            <div class="alertList">
                <a href="javascript:void(0);">This is the useful tool</a>
                <a href="javascript:void(0);">This is the useful tool</a>
            </div>
        </div>
        <button type="button" class="searchBtn d-md-none ms-auto me-3" data-bs-toggle="dropdown"><img
                src="{{ asset('frontend/images/topsearch-icon.svg') }}" alt="img" width="20"></button>

        @if (Session::has('admin_id'))
            <a href="{{ route('superadmin.backToAdmin') }}" class="btn btn-sm btn-warning ms-md-auto me-3 me-md-4">Back
                to Admin</a>
        @endif
        @if (Session::has('parent_school_id'))
            <a href="{{ route('sp.back.to.parent') }}" class="btn btn-sm btn-warning ms-md-auto me-3 me-md-4">Back
                to Parent School</a>
        @endif

        <a href="#" class="ms-md-auto me-3 me-md-4" data-bs-toggle="tooltip"
            data-bs-placement="top" title="User Manual / Guide">
        </a>

        </a>

        <button class="dropdownPrf me-3" type="button" data-bs-target="#profile" data-bs-toggle="modal">
            <img src="{{ Auth::user()->image ? Storage::url('uploads/user/profile_image/' . Auth::user()->image) : asset('frontend/images/default-image.jpg') }}"
                alt="profile-image">{{ ucwords(Auth::user()->name) }}</button>
    </div>
</header>
