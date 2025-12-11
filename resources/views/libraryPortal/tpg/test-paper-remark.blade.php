@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="test-review.html">Test Review</a></li>
            <li class="breadcrumb-item"><a href="view-test-review.html">View Test Paper</a></li>
            <li class="breadcrumb-item active" aria-current="page">Remark Paper</li>
        </ol>
    </nav>
    <div class="row textReview">
        <div class="col-md-8 mb-3 pe-md-1">
            <div class="cardBox">
                <h6 class="fs-7 mb-3 fw-semibold">Subjective Questions</h6>

                @php
                    $subjectiveTypes = ['short-answer-questions', 'long-answer-questions', 'one-word-answer'];
                    $shownPassages = [];

                @endphp

                @foreach ($questionsList as $index => $item)
                    @php
                        $questionType = $item->questionBank->question_type ?? '';
                    @endphp

                    @if (in_array($questionType, $subjectiveTypes))
                        {{-- Subjective question layout --}}
                        <div class="mb-4">
                            <span class="fw-semibold cardboxSpan qType">
                                {{ ucfirst(str_replace('-', ' ', $item->questionBank->question_type)) ?? '' }}
                            </span>
                        </div>
                        <div class="questionsBx">
                            <div class="d-flex flex-wrap gap-3 align-items-baseline mb-3">
                                <span>Q.{{ $index + 1 }}</span>
                                <p class="mb-0 flex-grow-1">{!! $item->questionBank->question ?? '' !!}</p>
                                <b class="text-black">Marks: {{ $item->questionBank->marks ?? '0' }}</b>
                            </div>
                            <div class="mcqMain ps-0">
                                <div class="d-flex gap-3">
                                    <b>Ans.</b>
                                    <p class="answer">{!! $item->answer ?? '' !!}</p>
                                </div>
                                <div class="d-flex justify-content-between align-items-center w-100 mb-4">
                                    <b class="text-black">Marks:
                                        {!! $item->questionBank->marks ?? '' !!}</b>
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <b>Status:</b>
                                        <input type="number" class="form-control w-50 score" min="0"
                                            value="{{ $item->score ?? '' }}" name="score">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-primary subjective-submit"
                                        data-question-id="{{ $item->questionBank->id }}" data-test-id="{{ $item->test_id }}"
                                        data-marks="{{ $item->questionBank->marks }}" data-user-id="{{ $item->user_id }}">
                                        Submit Score
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif ($questionType === 'passage')
                        @if (!in_array($item->question_id, $shownPassages))
                            @php
                                $shownPassages[] = $item->question_id;
                                $passageData = json_decode($item->questionBank->additional_data ?? '');
                                $subAnswers = $questionsList
                                    ->where('question_id', $item->question_id)
                                    ->sortBy('sub_index')
                                    ->values();
                                $marks = $item->questionBank->marks;
                            @endphp
                            <div class="mb-3">
                                <span class="fw-semibold cardboxSpan qType">
                                    {{ ucfirst(str_replace('-', ' ', $item->questionBank->question_type)) ?? '' }}
                                </span>
                            </div>
                            <div class="questionsBx border rounded p-4 mb-4">
                                <!-- Main Question Number -->
                                <div class="d-flex flex-wrap gap-3 align-items-baseline mb-3">
                                    <span>Q.{{ $index + 1 }}</span>
                                    <p class="mb-0 flex-grow-1">{!! $item->questionBank->question ?? '' !!}</p>
                                    <b class="text-black">Marks: {{ $item->questionBank->marks ?? '0' }}</b>
                                </div>

                                <!-- Passage Content -->
                                <div class="ps-4 mb-4 border-start border-3 border-secondary">
                                    @if ($passageData && isset($passageData->paragraph_statement))
                                        <div class="passage-statement mb-3">
                                            {!! $passageData->paragraph_statement !!}
                                        </div>
                                    @endif
                                </div>

                                <!-- Passage Sub-Questions -->
                                @if ($passageData && isset($passageData->questions_and_answers))
                                    <form class="passage-score-form" action="{{ route('sp.question.score.submit') }}"
                                        method="POST"> @csrf
                                        <!-- Hidden fields outside loop -->
                                        <input type="hidden" name="test_id" value="{{ $item->test_id }}">
                                        <input type="hidden" name="question_id" value="{{ $item->questionBank->id }}">

                                        <div class="passage-questions ps-4">
                                            @foreach ($passageData->questions_and_answers as $qIndex => $qa)
                                                @php
                                                    $subAnswer = $subAnswers->get($qIndex);
                                                    $userAnswer = $subAnswer
                                                        ? $subAnswer->answer
                                                        : 'No answer provided';
                                                @endphp

                                                <div class="question-item mb-4 pb-3 border-bottom">
                                                    <div class="d-flex gap-3 align-items-baseline">
                                                        <span class="fw-bold">{{ chr(97 + $qIndex) }}.</span>
                                                        <p class="mb-0">{!! $qa->question !!}</p>
                                                    </div>

                                                    <div class="mcqMain ps-3 mt-2">
                                                        <div class="d-flex gap-3 align-items-baseline">
                                                            <b>Ans.</b>
                                                            <p class="answer mb-0">{!! $userAnswer !!}</p>
                                                        </div>

                                                        <!-- Hidden sub_index for each sub-question -->
                                                        <input type="hidden" name="sub_indices[{{ $subAnswer->id }}]"
                                                            value="{{ $subAnswer->sub_index ?? '' }}">
                                                        <input type="hidden" name="question_marks"
                                                            value="{{ $item->questionBank->marks }}">
                                                        <input type="hidden" name="user_id" value="{{ $item->user_id }}">

                                                        <!-- Marks and Scoring -->
                                                        <div
                                                            class="d-flex justify-content-between align-items-center w-100 mt-3">
                                                            <div class="d-flex align-items-center gap-2">
                                                                <b>Score:</b>
                                                                <input type="number" class="form-control w-50 score"
                                                                    name="scores[{{ $subAnswer->id }}]" min="0"
                                                                    max="{{ $item->questionBank->marks ?? '1' }}"
                                                                    value="{{ $subAnswer ? $subAnswer->score : '' }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary rounded-1 py-2 px-4">
                                                Submit Score
                                            </button>
                                        </div>
                                    </form>
                                @endif

                            </div>
                        @endif
                    @else
                        <div class="mb-3">
                            <span class="fw-semibold cardboxSpan qType">
                                {{ ucfirst(str_replace('-', ' ', $item->questionBank->question_type)) ?? '' }}
                            </span>
                        </div>
                        <div class="questionsBx">
                            <div class="d-flex flex-wrap gap-3 align-items-baseline mb-3">
                                <span>Q.{{ $index + 1 }}</span>
                                <p class="mb-0 flex-grow-1">{!! $item->questionBank->question ?? '' !!}</p>
                                <b class="text-black">Marks: {{ $item->questionBank->marks ?? '0' }}</b>
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
                                    @foreach (explode(',', $item->answer) as $answerId)
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
                    @endif
                @endforeach
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="cardBox testPaperDetail">
                <h6 class="fs-7 mb-3 fw-semibold">Test Details</h6>
                <div class="d-flex align-items-center mb-3 gap-3">
                    <figure class="m-0">
                        <img src="{{ asset('frontend/images/test-tittle-icon.svg') }}" alt="" width="38">
                    </figure>
                    <span class="fw-semibold fs-8">{{ $testPaper->title }}</span>
                </div>
                <p class="text-secondary fw-medium fs-8">Created by : {{ $testPaper->user->name }}</p>
                <hr>
                <b class="fw-medium text-secondary fs-8 d-block mb-3"> User Name: <span
                        class="text-black fw-normal d-block">{{ $userDetails->name ?? '-' }}</span> </b>
                <b class="fw-medium text-secondary fs-8 d-block mb-3"> Roll No.<span
                        class="text-black fw-normal d-block">{{ $userDetails->studentDetails->roll_number ?? '-' }}</span>
                </b>
                <b class="fw-medium text-secondary fs-8 d-block mb-3"> Test Name:<span
                        class="text-black fw-normal d-block">{{ $testPaper->title ?? '-' }}</span> </b>
                <b class="fw-medium text-secondary fs-8 d-block mb-3"> Email Id:<span
                        class="text-black fw-normal d-block">{{ $userDetails->email ?? '-' }}</span> </b>
                <b class="fw-medium text-secondary fs-8 d-block mb-3">Test Submit Date:<span
                        class="text-black fw-normal d-block">{{ \Carbon\Carbon::parse($testPaper->end_date_time)->toDateString() ?? '-' }}</span>
                </b>
                <b class="fw-medium text-secondary fs-8 d-block mb-3"> Passing Score:<span
                        class="text-black fw-normal d-block">{{ $testPaper->min_passing_percentage ?? '-' }}</span> </b>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Subjective Score Submit (AJAX)
            $('.subjective-submit').on('click', function() {
                let btn = $(this);
                let questionId = btn.data('question-id');
                let testId = btn.data('test-id');
                let questionMarks = btn.data('marks');
                let userId = btn.data('user-id');
                let score = btn.closest('.questionsBx').find('.score').val();

                $.ajax({
                    url: '{{ route('sp.question.subjective.score.submit') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        test_id: testId,
                        question_id: questionId,
                        score: score,
                        questionMarks: questionMarks,
                        userId: userId,
                    },
                    success: function(response) {
                        alert(response.message);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            alert(xhr.responseJSON.message);
                        } else {
                            alert("Failed to submit score.");
                        }
                    }
                });
            });

            // Passage Score Submit (AJAX)
            $('.passage-score-form').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        alert(response.message);
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            alert(xhr.responseJSON.message);
                        } else {
                            alert("Failed to submit passage scores.");
                        }
                    }
                });
            });
        });
    </script>
@endsection
