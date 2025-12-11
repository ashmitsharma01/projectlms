@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('sp.lesson.planner') }}">Lesson Plan</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sp.lesson.planner.subjects', $class_id) }}">Class
                    Subjects</a></li>
            <li class="breadcrumb-item active" aria-current="page">Course Listing</li>
        </ol>
    </nav>
    <div class="cardBox myCourses">
        {{--  <div class="d-md-flex align-items-center justify-content-between mb-3">
                <h2 class="fs-6 fw-semibold">My Courses</h2>
                <div class="d-flex gap-2 align-items-center coursesFilter">
                    <label>Filter by:</label>
                    <select class="form-select">
                        <option>Board</option>
                    </select>
                    <select class="form-select">
                        <option>Medium</option>
                    </select>
                    <select class="form-select">
                        <option>Class</option>
                    </select>
                    <select class="form-select">
                        <option>Subject</option>
                    </select>
                </div>
            </div>  --}}
        <div class="table-responsive tbleDiv ">
            <table class="table ">
                <thead>
                    <tr>
                        <th>
                            <span class="classSubject">{{ $className }} - {{ $subjectName }} -</span>
                            Book Name
                        </th>
                        <th>Class</th>
                        @if (config('COURSES_FILTER_BY_ACCESS_CODE') == 1)
                            <th>Access Code</th>
                            <th>Remaining A.C.</th>
                        @endif
                        <th>Total Chapters</th>
                        <th>Content Group</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($courseListing as $data)
                        @php
                            $bannerImage = $data['metadataValues']
                                ->where('field_name', 'thumbnail_image')
                                ->value('field_value');
                            $bookCoverImage = $data['metadataValues']
                                ->where('field_name', 'book_cover_image')
                                ->value('field_value');
                            $productCode = $data['metadataValues']
                                ->where('field_name', 'product_code')
                                ->value('field_value');
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center myCourseLft">
                                    <figure>
                                        @if ($bannerImage)
                                            <img src="{{ $bannerImage ? Storage::url($bannerImage) : asset('frontend/images/default-image.jpg') }}"
                                                alt="course image">
                                        @else
                                            <img src="{{ $bookCoverImage ? Storage::url($bookCoverImage) : asset('frontend/images/default-image.jpg') }}"
                                                alt="course image">
                                        @endif
                                    </figure>
                                    <div class="coursesName">
                                        <h3>{{ $data->course_name ?? 'N/A' }}</h3>
                                        <p>Book Product code: {{ $productCode ?? 'N/A' }}</p>
                                        {{-- <p>Session: N/A</p> --}}
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $classInfo = $data->metadataValues->where('field_name', 'class')->first();
                                @endphp
                                {{ $classInfo->classInfo->name ?? 'N/A' }}
                            </td>
                            @if (config('COURSES_FILTER_BY_ACCESS_CODE') == 1)
                                <td>{{ $totalAccessCodes }}</td>
                                <td>{{ $unUsedAccessCodes }}</td>
                            @endif
                            <td>{{ $data->totalChapters->count() }}</td>
                            <td>Academic</td>
                            <td>
                                <a href="{{ route('lesson.planner.chapter.details', ['id' => $data->id, 'classId' => $class_id, 'subjectId' => $id]) }}"
                                    class="bg-transparent border-0 p-0">
                                    <img src="{{ asset('frontend/images/icon-eye.svg') }}" alt="" width="28">
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
