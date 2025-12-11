@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="test-review.html">Test Review</a></li>
            <li class="breadcrumb-item"><a href="{{ route('sp.test-paper.assigned.users', $testId) }}">View Test Paper</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Review</li>
        </ol>
    </nav>
    <div class="row textReview">
        <div class="col-md-8 mb-3  pe-md-1">
            <div class="cardBox">
                <h6 class="fs-7 mb-3 fw-semibold">Test Details</h6>
                @foreach ($questionsList as $index => $item)
                    <div class="questionsBx">
                        <strong>{{ $item->questionBank->question_type }}</strong>
                        <div class="d-flex gap-3">
                            <span>Q.{{ $index + 1 }}</span>
                            <p class="mb-4">{{ $item->questionBank->question }}</p>
                        </div>
                        <div class="mcqMain">
                            <ul class="questionsUl mb-4">
                                @foreach ($item->questionBank->options as $key => $option)
                                    <li>
                                        <span>{{ chr(65 + $key) }}.</span>
                                        {!! $option->option_text !!}

                                        @if ($option->is_correct)
                                            <img src="{{ asset('frontend/images/checkGreen.svg') }}" class="ms-2"
                                                alt="" width="18">
                                        @endif
                                    </li>
                                @endforeach

                            </ul>
                            <div class="d-flex justify-content-between">
                                @foreach (explode(',', $item->valid_answer) as $answerId)
                                    @php
                                        $option = $item->questionBank->options->firstWhere('id', $answerId);
                                    @endphp
                                    @if ($option)
                                        <div class="d-flex align-items-center gap-2">
                                            <b>User Answer:</b>
                                            <input type="text" class="form-control" value="{!! $option->option_text !!}"
                                                readonly>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="d-flex align-items-center gap-2">
                                    <b>Status:</b>
                                    <span
                                        class="badgeCorrect">{{ $item->is_correct == '1' ? 'Correct' : 'Incorrect' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="cardBox testPaperDetail">
                <h6 class="fs-7 mb-3 fw-semibold">Test Details</h6>
                <div class="d-flex align-items-center mb-3 gap-3">
                    <figure class="m-0">
                        <img src="{{ asset('frontend/images/test-tittle') }}-icon.svg" alt="" width="38">
                    </figure>
                    <span class="fw-semibold fs-8">{{ $testPaper->title }}</span>
                </div>
                <p class="text-secondary fw-medium fs-8">Created by : {{ $testPaper->user->name }}</p>
                <hr>
                <div class="table-responsive detailsTbl">
                    <table class="table">
                        {{-- <tr>
                            <td>Test Series:</td>
                            <td>{{ $testPaper->series->name }}</td>
                        </tr> --}}
                        <tr>
                            <td>Subject</td>
                            <td>{{ $testPaper->Subject->name }}</td>
                        </tr>
                        <tr>
                            <td>Class:</td>
                            <td>{{ $testPaper->Class->name }}</td>
                        </tr>
                        <tr>
                            <td>Total Question:</td>
                            <td>{{ $testPaper->questionCount->count() }}</td>
                        </tr>
                        <tr>
                            <td>Minimum Passing Percentage:</td>
                            <td>{{ $testPaper->min_passing_percentage }}.%</td>
                        </tr>
                        <tr>
                            <td>Description:</td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <p class="descriptionTxt">{{ $testPaper->description }}</p>
            </div>
        </div>
    </div>
@endsection
