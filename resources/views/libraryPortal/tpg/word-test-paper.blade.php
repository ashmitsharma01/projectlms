<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        /* keep styles minimal & safe for PHPWord */
        body {
            font-family: "Times New Roman", serif;
            font-size: 12pt;
            line-height: 1.45;
        }

        .center {
            text-align: center;
        }

        .school-name {
            font-size: 16pt;
            font-weight: bold;
        }

        .test-title {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 6px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .meta-table td {
            padding: 4px 6px;
            vertical-align: middle;
        }

        .section-title {
            font-weight: bold;
            font-size: 13pt;
            margin-top: 14px;
            text-decoration: underline;
        }

        .question {
            margin-top: 10px;
            margin-bottom: 6px;
        }

        .marks {
            float: right;
            font-weight: bold;
        }

        .options-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .options-table td {
            padding: 6px;
            vertical-align: top;
        }

        .img-box {
            border: 1px solid #999;
            padding: 6px;
            text-align: center;
            margin-top: 6px;
        }

        .answer-line {
            border-bottom: 1px solid #000;
            height: 18px;
            display: block;
            margin-top: 6px;
        }

        .long-answer {
            border: 1px dashed #999;
            height: 120px;
            margin-top: 8px;
            padding: 6px;
        }

        .small-note {
            font-style: italic;
            font-size: 10pt;
            margin-top: 6px;
        }

        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 10px 0;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="center">
        {{-- @if (!empty($logoUrl))
            <div style="margin-bottom:8px;">
                <img src="{{ $logoUrl }}" style="height:80px; object-fit:contain;" />
            </div>
        @endif --}}
        <div class="school-name">{{ $schoolName }}</div>
        <div class="test-title">{{ $paper->test_term }}</div>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Class:</strong> {{ $className }}</td>
            <td style="text-align:right;"><strong>Total Marks:</strong> {{ $totalMarks }}</td>
        </tr>
        <tr>
            <td><strong>Subject:</strong> {{ $subjectName }}</td>
            <td style="text-align:right;"><strong>Duration:</strong> {{ $durationInHours }} hours</td>
        </tr>
    </table>

    <p style="margin:8px 0;"><strong>Roll No.:</strong> __________ &nbsp;&nbsp;&nbsp; <strong>Date:</strong> __________
    </p>

    <p><strong>General Instructions:</strong></p>
    <p>{!! $paper->description !!}</p>

    <hr />

    {{-- Sections --}}
    @foreach ($questions as $type => $group)
        @php
            $sectionTitle = match ($type) {
                'mcq' => 'Multiple Choice Questions',
                't/f' => 'True or False',
                'fill-ups' => 'Fill in the Blanks',
                'one-word-answer' => 'One Word Answers',
                'match-the-following' => 'Match the Following',
                'passage' => 'Passage-Based Questions',
                'picture-based-questions' => 'Picture-Based Questions',
                'read-circle' => 'Read & Circle',
                'circle-underline' => 'Circle/Underline',
                'tick' => 'Tick the Correct Option',
                'short-answer-questions' => 'Short Answer Questions (2–3 lines)',
                'long-answer-questions' => 'Long Answer Questions (200 words)',
                default => 'Other Questions',
            };
        @endphp

        <div class="section-title">
            Section {{ chr(65 + $loop->index) }}: {{ $sectionTitle }} ({{ $group->sum('marks') }} Marks)
        </div>

        @foreach ($group as $qIndex => $q)
            {{-- For non-passage question show header --}}
            @if ($q->question_type != 'passage')
                <div class="question">
                    <span><strong>{{ $loop->iteration }}.</strong> {!! $q->question !!}</span>
                    <span class="marks">[{{ number_format($q->marks, 2) }}]</span>
                </div>
            @endif

            {{-- MCQ / Tick / T-F / Options: show in 2-column table --}}
            @if (in_array($type, ['mcq', 'tick', 't/f']))
                <table class="options-table" border="0">
                    @php $opts = $q->options ?? collect(); @endphp
                    @foreach ($opts->chunk(2) as $row)
                        <tr>
                            @foreach ($row as $key => $opt)
                                <td width="50%"><strong>{{ chr(97 + ($loop->parent->index * 2 + $key)) }}.</strong>
                                    {!! $opt->option_text ?? $opt->option !!}</td>
                            @endforeach
                            @if ($row->count() === 1)
                                <td width="50%"></td>
                            @endif
                        </tr>
                    @endforeach
                </table>

                @if ($userType === 'teacher')
                    {{-- teacher answer --}}
                    @php $correct = $q->options->first(fn($o)=>$o->is_correct ?? false); @endphp
                    @if ($correct)
                        <p class="small-note"><strong>Answer:</strong> {!! $correct->option_text ?? $correct->option !!}</p>
                    @endif
                @endif

                {{-- Picture-based --}}
            @elseif($type === 'picture-based-questions')
                <div class="img-box">
                    {!! $q->question !!}
                </div>
                @if (!empty($q->options))
                    <table class="options-table">
                        @foreach ($q->options as $k => $opt)
                            <tr>
                                <td width="30%"><strong>{{ chr(97 + $k) }}.</strong></td>
                                <td>{!! $opt->option_text !!}</td>
                            </tr>
                        @endforeach
                    </table>
                    @if ($userType === 'teacher')
                        <p class="small-note"><strong>Answer:</strong> {!! $q->options->where('is_correct', 1)->first()->option_text ?? '' !!}</p>
                    @endif
                @endif

                {{-- One-word / Fill-ups --}}
            @elseif(in_array($type, ['one-word-answer', 'fill-ups']))
                <div class="answer-line"></div>
                @if ($userType === 'teacher')
                    <p class="small-note"><strong>Answer:</strong> {!! $q->answer_text !!}</p>
                @endif

                {{-- Short answer --}}
            @elseif($type === 'short-answer-questions')
                <div class="long-answer" style="height:80px;"></div>
                @if ($userType === 'teacher')
                    <p class="small-note"><strong>Answer:</strong> {!! $q->answer_text !!}</p>
                @endif

                {{-- Long answer --}}
            @elseif($type === 'long-answer-questions')
                <div class="long-answer" style="height:140px;"></div>
                @if ($userType === 'teacher')
                    <p class="small-note"><strong>Answer:</strong> {!! $q->answer_text !!}</p>
                @endif

                {{-- Match the following --}}
            @elseif($type === 'match-the-following' && $q->options->count() >= 8)
                @php
                    $left = $q->options->slice(0, 4)->values();
                    $right = $q->options->slice(4, 4)->values();
                @endphp
                <table border="1">
                    <tr>
                        <th>Column A</th>
                        <th>Column B</th>
                    </tr>
                    @for ($i = 0; $i < 4; $i++)
                        <tr>
                            <td>{{ chr(65 + $i) }}. {!! $left[$i]->option_text ?? '' !!}</td>
                            <td>{{ chr(97 + $i) }}. {!! $right[$i]->option_text ?? '' !!}</td>
                        </tr>
                    @endfor
                </table>
                @if ($userType === 'teacher')
                    <p class="small-note"><strong>Correct Matches:</strong></p>
                    <p class="small-note">
                        @foreach ($q->options as $opt)
                            {!! $opt->left_text ?? '' !!} → {!! $opt->right_text ?? '' !!}<br />
                        @endforeach
                    </p>
                @endif

                {{-- Passage --}}
            @elseif($type === 'passage')
                @php $data = json_decode($q->additional_data ?? '{}', true); @endphp
                @if (!empty($data['paragraph_statement']))
                    <p><strong>{!! $data['paragraph_statement'] !!}</strong></p>
                @endif
                <p>{!! $data['paragraph'] ?? '' !!}</p>
                @foreach ($data['questions_and_answers'] ?? [] as $si => $sub)
                    <div class="question">
                        <p><strong>{{ chr(65 + $si) }}.</strong> {!! $sub['question'] ?? '' !!}</p>
                        <div class="answer-line"></div>
                        @if ($userType === 'teacher' && !empty($sub['answer']))
                            <p class="small-note"><strong>Answer:</strong> {!! $sub['answer'] !!}</p>
                        @endif
                    </div>
                @endforeach
            @endif
        @endforeach
    @endforeach

    <p class="center" style="margin-top:20px;"><strong>*** END OF QUESTION PAPER ***</strong></p>

</body>

</html>
