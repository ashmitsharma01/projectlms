@extends('libraryPortal.layouts.master')
@section('content')
    <style>
        /* Hide Bootstrap's default accordion icon */
        .accordion-button::after {
            display: none !important;
            content: none !important;
        }

        .blink-icon {
            animation: blink 1.5s infinite;
            font-size: 20px;
            color: #00BE55;
            transition: transform 0.3s ease;
        }

        .accordion-button:not(.collapsed) .blink-icon {
            transform: rotate(180deg);
            animation: none;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }
    </style>
    @include('admin.layouts.flash-messages')
    <div class="cardBox mb-4">
        <div class="d-md-flex justify-content-between align-items-center mb-3">
            <h2 class="fs-6 fw-semibold mb-3">Subjects</h2>
            @if (config('COURSES_FILTER_BY_ACCESS_CODE') == 1)
                <div class="plannerHeader">
                    <span>Filter by:</span>
                    <div class="d-flex gap-md-3 flex-wrap">
                        <select class="form-select" id="mediumFilter">
                            <option disabled {{ request('medium') ? '' : 'selected' }}>Medium</option>
                            @foreach ($medium as $item)
                                <option value="{{ $item->id }}" {{ request('medium') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
        </div>
        <div class="row px-md-1">
            <!-- Accordion Start -->
            <div class="accordion" id="classAccordion">
                @foreach ($classCourses as $key => $data)
                    <div class="accordion-item mb-3">
                        <h2 class="accordion-header" id="heading{{ $key }}">
                            <h2 class="accordion-header" id="heading{{ $key }}">
                                <button class="accordion-button collapsed d-flex justify-content-between align-items-center"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $key }}"
                                    aria-expanded="false" aria-controls="collapse{{ $key }}">

                                    <div class="d-flex align-items-center gap-3">
                                        <figure class="m-0 position-relative">
                                            <span
                                                class="rounded-circle d-inline-flex justify-content-center align-items-center first-circle">
                                                <span class="rounded-circle second-circle">
                                                    {{ substr($data->class->name ?? 'N/A', 0, 1) }}
                                                </span>
                                            </span>
                                        </figure>
                                        <span class="fw-bold">{{ $data->class->name ?? 'N/A' }}</span>
                                    </div>

                                    {{-- Custom blinking icon --}}
                                    <i class="fa fa-chevron-down blink-icon ms-auto"></i>
                                </button>
                            </h2>

                        </h2>
                        <div id="collapse{{ $key }}" class="accordion-collapse collapse"
                            aria-labelledby="heading{{ $key }}" data-bs-parent="#classAccordion">
                            <div class="accordion-body">
                                <div class="row px-md-1">
                                    @foreach ($data->subjects as $index => $subject)
                                        <div class="col-md-4 col-lg-3 mb-3 px-md-2">
                                            <div class="languageBox subjectListDiv h-100 postion-relative pt-0">
                                                <h6 class="dataName mb-3">{{ $subject->name }} </h6>

                                                @if ($subject->book_cover_image || $subject->thumbnail_image)
                                                    <a href="#imagesModal-{{ $key }}-{{ $index }}"
                                                        data-bs-toggle="modal">
                                                        <img src="{{ Storage::url($subject->book_cover_image ?? $subject->thumbnail_image) }}"
                                                            class="cornerImg">
                                                    </a>
                                                    <!--Magnify Modal -->
                                                    <div class="modal fade imagesModal"
                                                        id="imagesModal-{{ $key }}-{{ $index }}">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                                <div class="modal-body">
                                                                    <img
                                                                        src="{{ Storage::url($subject->book_cover_image ?? $subject->thumbnail_image) }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class=""
                                                        style="height: 225px;display:flex; justify-content:center;align-items:center;">
                                                        <img src="{{ asset('images/mittlearn-favicon.png') }}">
                                                    </div>
                                                @endif

                                                <p style="min-height:20px" class="fw-semibold text-center mt-2">
                                                    {{ $subject->course_name ?? ' ' }}</p>

                                                <div class="d-xxl-flex justify-content-between gap-2 align-items-center">
                                                    <a href="{{ route('sp.course.listing', [$subject->id, $data->class_id]) }}"
                                                        class="btn btn-primary-gradient rounded-1 py-2 w-100 mb-2">Explore</a>
                                                    @if ($subject->course_id)
                                                        <a href="{{ route('sp.courses.details', ['id' => $subject->course_id, 'classId' => $data->class_id, 'subjectId' => $subject->id]) }}"
                                                            class="btn btn-success rounded-1 py-2 w-100 mb-2">View
                                                            content</a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- Accordion End -->
        </div>
    </div>
    @if ($complimentaryCourse->isNotEmpty())
        <div class="cardBox">
            <div class="d-md-flex justify-content-between align-items-center mb-3">
                <h2 class="fs-6 fw-semibold mb-3">Talent Box ( Complementry For Student )</h2>
                <div class="plannerHeader">

                </div>

            </div>
            <div class="row px-md-1">
                @foreach ($complimentaryCourse as $data)
                    <div class="col-md-6 col-lg-4 col-xl-3 px-md-2 mb-3">
                        <div class="coursesBox">
                            @php
                                $bannerImage = $data->courses[0]->metadataValues
                                    ->where('field_name', 'banner_image')
                                    ->value('field_value');
                            @endphp
                            @if ($bannerImage)
                                <img src="{{ Storage::url($bannerImage) }}"
                                    style="width: 100%;height: 160px;border-radius: 4px;object-fit: cover;"
                                    alt="{{ $data->courses[0]->course_name ?? 'N/A' }}">
                            @endif
                            <h3 class="px-2"> {{ $data->courses[0]->course_name ?? 'N/A' }}</h3>
                            <div class="d-flex gap-2 justify-content-between px-2">

                                <div class="blogProfile d-flex align-items-center gap-2">
                                    <figure>
                                        <img src="/frontend/images/blog-profile.jpg" alt="">
                                    </figure>
                                    <p class="text-muted small mb-0">Offered by<br><strong><b>Mittsure</b></strong></p>
                                </div>
                            </div>
                            <hr>
                            @php
                                $price = $data->courses[0]->price;
                                $discount = 0;

                                if ($data->courses[0]->discount_type === 'percent') {
                                    $discount = ($data->courses[0]->discount_value / 100) * $price;
                                } elseif ($data->courses[0]->discount_type === 'flat') {
                                    $discount = $data->courses[0]->discount_value;
                                }
                                $finalPrice = max(0, $price - $discount);

                            @endphp
                            <div class="d-flex gap-2 align-items-center pb-2 justify-content-between px-2">
                                <div class="pricetag ">
                                    <span>₹ {{ number_format($price, 0) ?? 'N/A' }}</span>
                                    ₹{{ $finalPrice ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    @if ($filteredClassActivityCourses->isNotEmpty())
        <div class="cardBox">
            <div class="d-md-flex justify-content-between align-items-center mb-3">
                <h2 class="fs-6 fw-semibold mb-3">Academic Activities ( Complementry For Student )</h2>
                <div class="plannerHeader">

                </div>

            </div>
            <div class="row px-md-1">
                @foreach ($filteredClassActivityCourses as $data)
                    @php
                        $course = $data->courses[0] ?? null;

                        $bannerImage = optional($course?->metadataValues->where('field_name', 'banner_image')->first())
                            ->field_value;
                        $class = optional($course?->metadataValues->where('field_name', 'class')->first()?->classInfo)
                            ->name;
                        $subject = optional(
                            $course?->metadataValues->where('field_name', 'subject')->first()?->subjectInfo,
                        )->name;

                        $price = $course->price ?? null;
                        $discount = 0;

                        if ($course && $course->discount_type === 'percent') {
                            $discount = ($course->discount_value / 100) * $price;
                        } elseif ($course && $course->discount_type === 'flat') {
                            $discount = $course->discount_value;
                        }

                        $finalPrice = $course ? max(0, $price - $discount) : null;
                    @endphp

                    <div class="col-md-6 col-lg-4 col-xl-3 px-md-2 mb-3">
                        <div class="coursesBox">
                            @if ($bannerImage)
                                <img src="{{ Storage::url($bannerImage) }}"
                                    style="width: 100%;height: 160px;border-radius: 4px;object-fit: cover;"
                                    alt="{{ $course->course_name ?? 'N/A' }}">
                            @endif

                            <h3 class="px-2">{{ $course->course_name ?? 'N/A' }}</h3>

                            <div class="d-flex gap-2 justify-content-between px-2">
                                <div class="blogProfile d-flex align-items-center gap-2">
                                    <figure>
                                        <img src="/frontend/images/blog-profile.jpg" alt="">
                                    </figure>
                                    <p class="text-muted small mb-0">Offered by<br><strong><b>Mittsure</b></strong></p>
                                </div>
                            </div>

                            <hr>

                            <div class="d-flex gap-2 align-items-center pb-2 justify-content-between px-2">
                                <span>
                                    <b>Grade:</b> {{ $class ?? 'N/A' }}<br>
                                    <b>Subject:</b> {{ $subject ?? 'N/A' }}
                                </span>

                                <div class="pricetag">
                                    <span>₹ {{ number_format($price ?? 0, 0) }}</span>
                                    ₹{{ $finalPrice ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    @endif


@endsection
