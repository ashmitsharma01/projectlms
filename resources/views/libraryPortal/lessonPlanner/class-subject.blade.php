    @extends('libraryPortal.layouts.master')
    @section('content')
        @include('admin.layouts.flash-messages')
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('sp.lesson.planner') }}">Lesson Plan</a></li>
                <li class="breadcrumb-item active" aria-current="page">Class Subjects</li>
            </ol>
        </nav>
        <div class="cardBox">
            <div class="d-md-flex justify-content-between align-items-center mb-3">
                <div class="headingList my-3 mb-4">
                    <figure class="m-0">
                        <img src="{{ asset('frontend/images/subject-list-icon.svg') }}" alt="" width="36">
                    </figure>
                    <div>
                        <h2 class="fs-6 fw-semibold m-0">{{ $className }} - {{ count($subjects) }} Subject List</h2>
                        <p> Explore the subjects with detailed digital content, assignments, and interactive materials
                            to enhance your learning.</p>
                    </div>
                </div>
            </div>

            <div class="row px-md-1">
                @foreach ($subjects as $index => $data)
                    <div class="col-md-4 col-lg-3 mb-3 px-md-2">
                        <div class="languageBox subjectListDiv h-100 postion-relative pt-0">
                            <h6 class="dataName mb-3">{{ $data->name }} </h6>

                            @if ($data->book_cover_image || $data->thumbnail_image)
                                <a href="#imagesModal-{{ $index }}" data-bs-toggle="modal">
                                    <img src="{{ Storage::url($data->book_cover_image ? $data->book_cover_image : $data->thumbnail_image) }}"
                                        class="cornerImg">
                                </a>
                                <!--Megnify Image  Modal -->
                                <div class="modal fade imagesModal" id="imagesModal-{{ $index }}">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                            <div class="modal-body">
                                                <img src="{{ Storage::url($data->book_cover_image ? $data->book_cover_image : $data->thumbnail_image) }}"
                                                    class="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class=""
                                    style="height: 225px;display:flex; justify-content:center;align-items:center;">
                                    <img src="{{ asset('images/mittlearn-favicon.png') }}" class="">
                                </div>
                            @endif

                            <p style="min-height:20px" class="fw-semibold text-center mt-2">{{ $data->course_name ?? ' ' }}
                            </p> <!-- Course Name -->
                            @if (config('COURSES_FILTER_BY_ACCESS_CODE') == 1)
                                <span><b>Total Access Code</b>{{ count($accessCodes) }} </span>
                                <span class="text-primary"><b>Total Book Purchased</b>{{ count($accessCodes) }} </span>
                            @endif
                            <div class="d-xxl-flex justify-content-between gap-2 align-items-center">
                                <a href="{{ route('sp.lesson.planner.course.listing', [$data->id, $classId]) }}"
                                    class="btn btn-primary-gradient rounded-1 py-2 mt-2 w-100">Explore</a>
                            </div>
                        </div>
                    </div>
                @endforeach


                {{-- # old design that view only subject name
                  @foreach ($subjects as $data)
                    <div class="col-md-4 col-lg-3 mb-3 px-md-2">
                        <div class="languageBox h-100">
                            <img src="{{ checkFile($data->image, 'uploads/subject/') }}" alt="" class="cornerImg">
                            <h6>{{ $data->name }}</h6>
                            @if (config('COURSES_FILTER_BY_ACCESS_CODE') == 1)
                                <span><b>Total Access Code</b>{{ count($accessCodes) }} </span>
                                <span class="text-primary"><b>Total Book Purchased</b>{{ count($accessCodes) }} </span>
                            @else
                                <span class="mt-3"></span>
                                <span class="text-primary"></span>
                            @endif
                            <a href="{{ route('sp.lesson.planner.course.listing', [$data->id, $classId]) }}"
                                class="btn btn-primary-gradient rounded-1 py-2 mt-2">Explore</a>
                        </div>
                    </div>
                @endforeach --}}
            </div>
        </div>
    @endsection
