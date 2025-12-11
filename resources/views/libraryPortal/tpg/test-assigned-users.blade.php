@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ route('sp.test-paper.view') }}">Test Review</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Test Paper</li>
        </ol>
    </nav>
    <div class="row textReview">
        <div class="col-md-8 mb-3 pe-md-1">
            <div class="teacherTable">
                <div class="headerTbl">
                    <h6 class="m-0 py-2">Assign Users List</h6>
                </div>
                <div class="px-3 py-2">
                    <div class="table-responsive tbleDiv ">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th width="20%">Sr. No.</th>
                                    <th width="25%">Name</th>
                                    <th width="25%">Admission No.</th>
                                    <th width="30%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attemptedTestUsers as $index => $item)
                                    @php
                                        $questionsList = App\Models\TestAnswer::with('questionBank')
                                            ->where('test_id', $test->id)
                                            ->where('user_id', $item->user->id)
                                            ->get();

                                        // Check if any question type is a subjective type
                                        $subjectiveTypes = [
                                            'short-answer-questions',
                                            'long-answer-questions',
                                            'passage',
                                            'one-word-answer',
                                        ];

                                        $hasSubjective = $questionsList->contains(function ($answer) use (
                                            $subjectiveTypes,
                                        ) {
                                            return isset($answer->questionBank->question_type) &&
                                                in_array($answer->questionBank->question_type, $subjectiveTypes);
                                        });
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><span class="nameTbl"> <img
                                                    src="{{ asset('frontend/images/sarah-deo-img.jpg') }}"
                                                    alt="">{{ $item->user->name }}
                                            </span></td>
                                        <td>{{ $item->user->userAdditionalDetail->admission_no }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @if ($hasSubjective)
                                                    <a href="{{ route('sp.test-paper.remark', ['id' => $item->id, 'user_id' => $item->user->id, 'test_id' => $test->id]) }}"
                                                        class="btn btn-success w-25 rounded-2">Remark</a>
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-secondary w-25 rounded-2 disabled">Review</a>
                                                @else
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-secondary w-25 rounded-2 disabled">Remark</a>
                                                    <a href="{{ route('sp.test-paper.review', ['id' => $item->id, 'user_id' => $item->user->id, 'test_id' => $test->id]) }}"
                                                        class="btn btn-primary-gradient w-25 rounded-2">Review</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="cardBox testPaperDetail">
                <h6 class="fs-7 mb-3 fw-semibold">Test Details</h6>

                <div class="d-flex align-items-center mb-3 gap-3">
                    <figure class="m-0">
                        <img src="{{ asset('frontend/images/test-tittle-icon.svg') }}" alt="" width="38">
                    </figure>
                    <span class="fw-semibold fs-8">{{ $test->title }}</span>
                </div>
                @php
                    $createdBy = \App\Models\User::where('id', $test->created_by)->value('name');
                @endphp
                <p class="text-secondary fw-medium fs-8">Created by : {{ $createdBy }}</p>
                <hr>
                <div class="table-responsive detailsTbl">
                    <table class="table">
                        <tr>
                            <td>Class:</td>
                            <td>{{ $test->Class->name }}</td>
                        </tr>
                        <tr>
                            <td>Subject</td>
                            <td>{{ $test->Subject->name }}</td>
                        </tr>
                        {{-- <tr>
                            <td>Chapters</td>
                            <td style="white-space: normal; word-break: break-word; max-width: 300px;">
                                {{ implode(', ', $test->chapter_names ?? []) }}
                            </td>
                        </tr> --}}
                        <tr>
                            <td>Minimum Marks:</td>
                            <td>{{ $test->min_passing_percentage }}</td>
                        </tr>
                        <tr>
                            <td>Test Duration:</td>
                            <td>{{ $test->duration }}</td>
                        </tr>
                        <tr>
                            <td>Description:</td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <p class="descriptionTxt">{{ $test->description }}</p>
            </div>
        </div>
    </div>
    </div>
@endsection
