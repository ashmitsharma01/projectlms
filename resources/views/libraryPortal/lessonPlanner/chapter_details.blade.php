@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('sp.lesson.planner') }}">Lesson Plan</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sp.lesson.planner.subjects', $classId) }}">Class
                    Subjects</a></li>
            <li class="breadcrumb-item active"><a
                    href="{{ route('sp.lesson.planner.course.listing', ['id' => $subjectId, 'class_id' => $classId]) }}">Course
                    Listing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chapter Listing</li>
        </ol>
    </nav>
    <div class="cardBox myCourses">
        {{--  <div class="d-md-flex align-items-center justify-content-between mb-3">
                <h2 class="fs-6 fw-semibold">Subjects/ Courses</h2>
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
        <div class="classSubjectBookName  mb-1">
            <span class="">{{ $className }} - {{ $subjectName }}- {{ $courseName }} </span>
        </div>
        <div class="table-responsive tbleDiv ">
            <table class="table ">
                <thead>
                    <tr>
                        <th>Chapter No.</th>
                        <th>Chapter Name</th>
                        <th>Creadted Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($plannerLesson as $data)
                        <tr>
                            <td>{{ $data->sort_order }}</td>
                            <td>
                                <div class="d-flex align-items-center myCourseLft">
                                    <div class="coursesName">
                                        <h3>{{ $data->chapter_name ?? 'N/A' }}</h3>
                                    </div>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($data->created_date)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('lesson.chapter-plannner', ['id' => $data->id, 'course_id' => $id, 'subject_id' => $subjectId, 'class_id' => $classId]) }}"
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
