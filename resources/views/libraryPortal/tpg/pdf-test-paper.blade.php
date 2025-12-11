{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Test Paper</title>
    <style>
        @page {
            size: A3;
            margin: 1.5cm;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-top {
            text-align: center;
        }

        .school-name {
            font-size: 16pt;
            font-weight: bold;
        }

        .test-title {
            font-size: 14pt;
            font-weight: bold;
            margin: 10px 0;
        }

        .section {
            page-break-inside: avoid;
        }

        .section-title {
            font-weight: bold;
            font-size: 13pt;
            margin-bottom: 10px;
            text-decoration: underline;
        }


        .row {
            display: flex;
            flex-wrap: wrap;
            margin-left: 20px;
            margin-top: 8px;
            margin-right: -15px;
            margin-left: -15px;
        }

        .col-lg-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 15px;
            box-sizing: border-box;
        }

        .col-m-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding: 0 15px;
            box-sizing: border-box;
            margin-bottom: 8px;
        }

        .option {
            display: flex;
            align-items: flex-start;
            min-height: 24px;
        }

        .option label {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            cursor: default;
            width: 100%;
        }

        .option-letter {
            display: inline-block;
            min-width: 20px;
            font-weight: bold;
        }

        .fill-blank {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 100px;
            margin: 0 5px;
        }

        .matching {
            display: flex;
            margin-left: 20px;
        }

        .matching-column {
            flex: 1;
        }

        .picture-question {
            text-align: center;
            margin: 15px 0;
        }

        .picture-box {
            width: 100%;
            max-width: 100%;
            height: auto;
            min-height: 150px;
            margin: 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            box-sizing: border-box;
            border: 1px dashed #ccc;
        }

        .picture-box img {
            max-width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
        }

        .instructions {
            font-style: italic;
            margin-bottom: 10px;
        }

        .marks {
            float: right;
            font-weight: bold;
            min-width: 40px;
            text-align: right;
        }

        .answer-space {
            margin-top: 10px;
            min-height: 20px;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 3px;
        }

        .long-answer-space {
            min-height: 100px;
            border: 1px dashed #ccc;
            margin-top: 10px;
            padding: 5px;
        }

        .page-break {
            page-break-after: always;
        }

        .tick-options input[type="checkbox"],
        .mcq-options input[type="radio"],
        .true-false-options input[type="radio"] {
            transform: scale(1.2);
            margin-top: 2px;
            flex-shrink: 0;
        }

        .test-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .class-subject {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 10px;
        }

        .match-following-wrapper {
            margin: 10px 0 15px 20px;
        }

        .matching-table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px;
        }

        .matching-table th,
        .matching-table td {
            border: 1px solid #000;
            padding: 8px 12px;
            text-align: left;
            vertical-align: top;
        }

        .matching-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .passage-box {
            margin: 10px 0 15px 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .passage-heading {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .teacher-answer {
            background-color: #f0f8ff;
            padding: 5px 8px;
            margin-top: 5px;
            border-radius: 3px;
            font-style: italic;
        }

        @media print {
            .teacher-answer {
                background-color: transparent;
                border: 1px dashed #ccc;
            }
        }

        body {
            font-family: 'notosansdevanagari', 'DejaVu Sans', sans-serif !important;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        * {
            font-family: 'notosansdevanagari', 'DejaVu Sans', sans-serif !important;
        }

        .option-table {
            width: 100%;
            margin-left: 20px;
            border-collapse: collapse;
        }

        .option-table td {
            vertical-align: middle;
        }

        .option-col-radio {
            width: 20px;
        }

        .option-col-letter {
            width: 25px;
        }

        .option-col-text {
            width: auto;
            white-space: nowrap;
        }

        .option-col-icon {
            width: 25px;
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="header-top">
            <table style="width:100%; border:0; border-collapse:collapse;">
                <tr>
                    <td style="width:120px; vertical-align:middle; text-align:left;">
                        @if (!empty($paper->logo) && Storage::disk('public')->exists($paper->logo))
                            <img src="{{ storage_path('app/public/' . $paper->logo) }}" alt="School Logo"
                                style="height:60px; object-fit:contain;">
                        @endif
                    </td>

                    <td style="text-align:center; vertical-align:middle;">
                        <div class="school-name">{{ $schoolName }}</div>
                        <div class="test-title">{{ $paper->test_term }}</div>

                        <div style="margin-top:6px;">
                            <span style="margin-right:20px;">CLASS: {{ $className }}</span>
                            <span>SUBJECT: {{ $subjectName }}</span>
                        </div>
                    </td>

                    <td style="width:120px;"></td>
                </tr>
            </table>
        </div>

        <div class="header-bottom" style="margin-top: 15px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="text-align: left; font-size: 12pt;">
                        Duration: {{ number_format($paper->duration / 60, 1) }} hours
                    </td>
                    <td style="text-align: right; font-size: 12pt;">
                        Total Marks: {{ $totalMarks }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="test-info">
        <div>Roll No.: __________</div>
        <div>Date: __________</div>
    </div>

    <div class="instructions">
        <strong>General Instructions:</strong>
        <p>{{ $paper->description }}</p>
    </div>

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
                'short-answer-questions' => 'Short Answer Questions (Answer in 2–3 lines)',
                'long-answer-questions' => 'Long Answer Questions (Answer in 200 words)',
                default => 'Other Questions',
            };
        @endphp

        <div class="section">
            <div class="section-title">
                Section {{ chr(65 + $loop->index) }}: {{ $sectionTitle }} ({{ $group->sum('marks') }} Marks)
            </div>

            @foreach ($group as $index => $q)
                @if ($q->question_type != 'passage')
                    <div class="question" style="margin: 0px !important;">
                        <div class="question-header">
                            <div class="question-content">
                                <span class="question-number">{{ $loop->iteration }}.</span>
                                <span>{!! $q->question !!}</span>
                            </div>
                            <span class="marks">[{{ $q->marks }}]</span>
                        </div>
                    </div>
                @endif


                @if ($type === 'tick' && !empty($q->options))
                    <table class="option-table" style="margin: 0px !important">
                        @foreach ($q->options as $key => $opt)
                            <tr>
                                <td class="option-col-radio">
                                    <input type="checkbox" disabled @if ($userType === 'teacher' && $opt->is_correct) checked @endif>
                                </td>

                                <td class="option-col-letter">
                                    {{ chr(97 + $key) }}.
                                </td>

                                <td class="option-col-text">
                                    {!! $opt->option_text !!}
                                </td>
                            </tr>
                        @endforeach
                    </table>


                @elseif ($type === 'mcq' && !empty($q->options))
                    <table class="option-table">
                        @foreach ($q->options as $key => $opt)
                            <tr>
                                <td class="option-col-radio">
                                    <input type="checkbox" style="margin-top: 6px" disabled
                                        @if ($userType === 'teacher' && $opt->is_correct) checked @endif>
                                </td>

                                <td class="option-col-letter">
                                    {{ chr(97 + $key) }}.
                                </td>

                                <td class="option-col-text">
                                    {!! $opt->option_text !!}
                                </td>
                            </tr>
                        @endforeach
                    </table>

                @elseif ($type === 'picture-based-questions')
                    <div class="picture-box">{!! $q->question !!}</div>
                    @if (!empty($q->options))
                        <div class="mcq-options">
                            <div class="row">
                                @foreach ($q->options as $key => $opt)
                                    <div class="col-m-6">
                                        <div class="option">
                                            <label>
                                                <input type="radio" name="q{{ $q->id }}" disabled
                                                    @if ($userType === 'teacher' && $opt->is_correct) checked @endif>
                                                <span class="option-letter">{{ chr(97 + $key) }}</span>
                                                <span>{!! $opt->option_text ?? '' !!}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                @elseif ($type === 't/f' && !empty($q->options))
                    <div class="true-false-options">
                        <div class="row">
                            @foreach ($q->options as $key => $opt)
                                <div class="col-m-6">
                                    <div class="option">
                                        <label>
                                            <input type="radio" name="q{{ $q->id }}" disabled
                                                @if ($userType === 'teacher' && $opt->is_correct) checked @endif>
                                            <span class="option-letter">{{ chr(97 + $key) }}</span>
                                            <span>{!! $opt->option_text ?? '' !!}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                @elseif ($type === 'one-word-answer')
                    <div class="answer-space"></div>
                    @if ($userType === 'teacher' && $q->answer_text)
                        <div class="teacher-answer">Answer: {!! $q->answer_text !!}</div>
                    @endif

                @elseif ($type === 'fill-ups')
                    <div class="answer-space"></div>
                    @if ($userType === 'teacher' && $q->answer_text)
                        <div class="teacher-answer">Answer: {!! $q->answer_text !!}</div>
                    @endif

                @elseif ($type === 'short-answer-questions')
                    <div class="answer-space" style="min-height: 60px;"></div>
                    @if ($userType === 'teacher' && $q->answer_text)
                        <div class="teacher-answer">Answer: {!! $q->answer_text !!}</div>
                    @endif

                @elseif ($type === 'long-answer-questions')
                    <div class="long-answer-space"></div>
                    @if ($userType === 'teacher' && $q->answer_text)
                        <div class="teacher-answer">Answer: {!! $q->answer_text !!}</div>
                    @endif

                @elseif ($type === 'match-the-following' && $q->options->count() >= 8)
                    @php
                        $leftOptions = $q->options->slice(0, 4)->values();
                        $rightOptions = $q->options->slice(4, 4)->values();
                    @endphp
                    <div class="match-following-wrapper">
                        <table class="matching-table" border="1" cellpadding="6" cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Column A</th>
                                    <th style="width: 50%;">Column B</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 4; $i++)
                                    <tr>
                                        <td>{{ chr(65 + $i) }}. {!! $leftOptions[$i]->option_text ?? '' !!}</td>
                                        <td>{{ chr(97 + $i) }}. {!! $rightOptions[$i]->option_text ?? '' !!}</td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                    @if ($userType === 'teacher')
                        <div class="teacher-answer">Correct Matches:
                            @foreach ($q->options as $opt)
                                {!! $opt->left_text ?? '' !!} → {!! $opt->right_text ?? '' !!}<br>
                            @endforeach
                        </div>
                    @endif

                @elseif ($type === 'passage')
                    @php
                        $data = json_decode($q->additional_data ?? '{}', true);
                    @endphp
                    @if (!empty($data['paragraph_statement']))
                        <div class="passage-heading">{!! $data['paragraph_statement'] !!}</div>
                    @endif
                    <div class="passage-box">
                        {!! $data['paragraph'] ?? '' !!}
                    </div>
                    @if (!empty($data['questions_and_answers']))
                        @foreach ($data['questions_and_answers'] as $index => $sub)
                            <div class="question" style="margin-left: 15px;">
                                <div class="question-header">
                                    <div class="question-content">
                                        <span class="question-number">{{ chr(65 + $index) }}.</span>
                                        <span>{!! $sub['question'] ?? '' !!}</span>
                                    </div>
                                </div>
                                <div class="answer-space"></div>
                                @if ($userType === 'teacher' && !empty($sub['answer']))
                                    <div class="teacher-answer">Answer: {!! $sub['answer'] !!}</div>
                                @endif
                            </div>
                        @endforeach
                    @endif

                @elseif ($type === 'read-circle')
                    <p>{!! $q->paragraph !!}</p>
                    <p>Circle from: {{ implode(' | ', $q->choices ?? []) }}</p>
                    <div class="answer-space"></div>
                @endif

                @if ($q->question_type != 'passage')
        </div>
    @endif
    @endforeach
    </div>
    @endforeach

    <div style="text-align: center; margin-top: 30px;">
        <strong>*** END OF QUESTION PAPER ***</strong>
    </div>
</body>

</html> --}}






<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Test Paper</title>
    <style>
        /* Small allowed CSS: page size. All layout styling is inline. */
        @page {
            size: A3;
            margin: 1.5cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>

    <!-- ========= HEADER (table-based) ========= -->
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:12px; width:100%;">
        <tr>
            <!-- Logo left -->
            <td width="120" valign="middle" style="padding:0;">
                @if (!empty($paper->logo) && Storage::disk('public')->exists($paper->logo))
                    <img src="{{ storage_path('app/public/' . $paper->logo) }}" alt="School Logo"
                        style="height:60px; object-fit:contain; display:block;">
                @endif
            </td>

            <!-- Center -->
            <td valign="middle" style="text-align:center; padding:0 10px;">
                <div style="font-size:18pt; font-weight:700; line-height:1.1;">{{ $schoolName }}</div>
                <div style="font-size:15pt; font-weight:700; margin-top:6px;">{{ $paper->test_term }}</div>
                <div style="margin-top:8px; font-size:11pt;">
                    <span style="margin-right:28px;">CLASS: {{ $className }}</span>
                    <span>SUBJECT: {{ $subjectName }}</span>
                </div>
            </td>

            <!-- Right empty for symmetry -->
            <td width="120" valign="middle" style="padding:0;"></td>
        </tr>
    </table>

    <!-- Header bottom: duration and marks -->
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:12px;">
        <tr>
            <td style="text-align:left; font-size:12pt; padding:2px 0;">
                Duration: {{ number_format($paper->duration / 60, 1) }} hours
            </td>
            <td style="text-align:right; font-size:12pt; padding:2px 0;">
                Total Marks: {{ $totalMarks }}
            </td>
        </tr>
    </table>

    <!-- Roll no and Date -->
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse; margin-bottom:10px;">
        <tr>
            <td style="padding:4px 0;">Roll No.: _______________________</td>
            <td style="text-align:right; padding:4px 0;">Date: _______________________</td>
        </tr>
    </table>

    <!-- Instructions box -->
    <table width="100%" cellpadding="6" cellspacing="0"
        style="border-collapse:collapse; border:1px solid #000; margin-bottom:14px; background-color:#ffffff;">
        <tr>
            <td style="font-weight:700; font-size:12pt;">General Instructions:</td>
        </tr>
        <tr>
            <td style="font-size:11pt; padding-top:4px;">{{ $paper->description }}</td>
        </tr>
    </table>

    <!-- ========== SECTIONS & QUESTIONS ========== -->
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
                'short-answer-questions' => 'Short Answer Questions (Answer in 2–3 lines)',
                'long-answer-questions' => 'Long Answer Questions (Answer in 200 words)',
                default => 'Other Questions',
            };
        @endphp

        <!-- Section title row -->
        <table width="100%" cellpadding="6" cellspacing="0"
            style="border-collapse:collapse; margin-top:10px; margin-bottom:6px;">
            <tr>
                <td style="font-weight:700; font-size:13pt; text-decoration:underline;">
                    Section {{ chr(65 + $loop->index) }}: {{ $sectionTitle }} ({{ $group->sum('marks') }} Marks)
                </td>
            </tr>
        </table>

        <!-- Iterate questions in group -->
        @foreach ($group as $index => $q)
            @php $qnNumber = $loop->iteration; @endphp

            <!-- If not passage, print question header in table row with marks -->
            @if ($q->question_type != 'passage')
                <table width="100%" cellpadding="4" cellspacing="0"
                    style="border-collapse:collapse; margin-bottom:4px; table-layout:fixed;">
                    <tr>
                        <td width="4%" style="font-weight:700; font-size:11pt; vertical-align:middle;">
                            {{ $qnNumber }}.
                        </td>
                        <td width="88%" style="font-size:11pt; vertical-align:middle; word-wrap:break-word;">
                            {!! $q->question !!}
                        </td>
                        <td width="8%"
                            style="text-align:right; font-weight:700; font-size:11pt; vertical-align:middle; white-space:nowrap;">
                            [{{ $q->marks }}]
                        </td>
                    </tr>
                </table>
            @endif

            <!-- ---------- TICK (checkbox list) ---------- -->
            @if ($type === 'tick' && !empty($q->options))
                <table width="100%" cellpadding="4" cellspacing="0"
                    style="border-collapse:collapse; margin-left:20px; margin-bottom:10px;">

                    @foreach ($q->options->chunk(2) as $row)
                        <tr>

                            @foreach ($row as $key => $opt)
                                <td width="50%" valign="top" style="white-space:wrap;">

                                    <span width="100%" style="white-space:wrap; vertical-align:middle;">

                                        <input width="10%" type="checkbox" disabled
                                            @if ($userType === 'teacher' && $opt->is_correct) checked @endif
                                            style="transform:scale(1.1); margin-right:6px; vertical-align:middle;">

                                        <span width="20%"
                                            style="font-weight:bold; margin-right:6px; vertical-align:middle;">
                                            {{ chr(97 + $key) }}.
                                        </span>

                                        <span width="30%" style="vertical-align:middle;">
                                            {!! preg_replace('/^<p>(.*)<\/p>$/si', '$1', $opt->option_text) !!}
                                        </span>

                                    </span>

                                </td>
                            @endforeach

                            {{-- Fill empty cell if odd number of options --}}
                            @if ($row->count() == 1)
                                <td width="50%"></td>
                            @endif

                        </tr>
                    @endforeach

                </table>


                <!-- ---------- MCQ (radio-style) ---------- -->
            @elseif ($type === 'mcq' && !empty($q->options))
                <table width="100%" cellpadding="4" cellspacing="0"
                    style="border-collapse:collapse; margin-left:20px; margin-bottom:10px;">

                    @foreach ($q->options->chunk(2) as $row)
                        <tr>

                            @foreach ($row as $key => $opt)
                                <td width="50%" valign="top" style="white-space:wrap;">

                                    <span width="100%" style="white-space:wrap; vertical-align:middle;">

                                        <input width="10%" type="checkbox" disabled
                                            @if ($userType === 'teacher' && $opt->is_correct) checked @endif
                                            style="transform:scale(1.1); margin-right:6px; vertical-align:middle;">

                                        <span width="20%"
                                            style="font-weight:bold; margin-right:6px; vertical-align:middle;">
                                            {{ chr(97 + $key) }}.
                                        </span>

                                        <span width="30%" style="vertical-align:middle;">
                                            {!! preg_replace('/^<p>(.*)<\/p>$/si', '$1', $opt->option_text) !!}
                                        </span>

                                    </span>

                                </td>
                            @endforeach

                            {{-- Fill empty cell if odd number of options --}}
                            @if ($row->count() == 1)
                                <td width="50%"></td>
                            @endif

                        </tr>
                    @endforeach

                </table>




                <!-- ---------- PICTURE-BASED ---------- -->
            @elseif ($type === 'picture-based-questions')
                <!-- Picture area boxed -->
                <table width="100%" cellpadding="8" cellspacing="0"
                    style="border-collapse:collapse; margin:12px 0; border:1px dashed #999;">
                    <tr>
                        <td style="text-align:center; padding:10px;">
                            {!! $q->question !!}
                        </td>
                    </tr>
                </table>

                <!-- options as MCQ table (if any) -->
                @if (!empty($q->options))
                    <table width="100%" cellpadding="3" cellspacing="0"
                        style="border-collapse:collapse; margin-left:20px; margin-bottom:8px;">
                        @foreach ($q->options as $key => $opt)
                            <tr>
                                <td width="30" valign="top" style="padding:3px 6px 3px 0;">
                                    <input type="radio" name="q{{ $q->id }}" disabled
                                        @if ($userType === 'teacher' && $opt->is_correct) checked @endif
                                        style="transform:scale(1.15);">
                                </td>
                                <td width="30" valign="top" style="padding:3px 6px 3px 0; font-weight:700;">
                                    {{ chr(97 + $key) }}.</td>
                                <td valign="top" style="padding:3px 0; font-size:11pt;">{!! $opt->option_text ?? '' !!}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif

                <!-- ---------- TRUE / FALSE ---------- -->
            @elseif ($type === 't/f' && !empty($q->options))
                @php $optionIndex = 0; @endphp

                <table width="100%" cellpadding="4" cellspacing="0"
                    style="border-collapse:collapse; margin-left:20px; margin-bottom:10px; table-layout:fixed;">

                    @foreach ($q->options->chunk(2) as $row)
                        <tr>

                            @foreach ($row as $opt)
                                <td width="50%" valign="top" style="padding:4px; vertical-align:middle;">

                                    <!-- Keep everything inline but allow text to wrap within the cell -->
                                    <span style="vertical-align:middle;">

                                        <!-- radio -->
                                        <input type="radio" name="q{{ $q->id }}" disabled
                                            @if ($userType === 'teacher' && $opt->is_correct) checked @endif
                                            style="transform:scale(1.15); margin-right:8px; vertical-align:middle;">

                                        <!-- letter -->
                                        <span style="font-weight:700; margin-right:6px; vertical-align:middle;">
                                            {{ chr(97 + $optionIndex) }}.
                                        </span>

                                        <!-- option text (strip single wrapping <p>..</p>) -->
                                        <span style="font-size:11pt; vertical-align:middle;">
                                            {!! preg_replace('/^<p>(.*)<\/p>$/si', '$1', $opt->option_text ?? '') !!}
                                        </span>

                                    </span>

                                </td>

                                @php $optionIndex++; @endphp
                            @endforeach

                            {{-- Fill empty cell if odd number of options --}}
                            @if ($row->count() == 1)
                                <td width="50%" style="padding:4px;"></td>
                            @endif

                        </tr>
                    @endforeach

                </table>

                <!-- ---------- ONE WORD / FILL-UPS ---------- -->
            @elseif (in_array($type, ['one-word-answer', 'fill-ups']))
                <table width="100%" cellpadding="6" cellspacing="0"
                    style="border-collapse:collapse; margin-left:20px; margin-bottom:6px;">
                    <tr>
                        <td style="padding:6px 0; border-bottom:1px dashed #999; height:28px;"></td>
                    </tr>
                </table>
                @if ($userType === 'teacher' && $q->answer_text)
                    <table width="100%" cellpadding="6" cellspacing="0"
                        style="border-collapse:collapse; margin-left:20px; margin-bottom:6px;">
                        <tr>
                            <td style="border:1px dashed #999; padding:6px; font-style:italic;">Answer:
                                {!! $q->answer_text !!}</td>
                        </tr>
                    </table>
                @endif

                <!-- ---------- SHORT ANSWER ---------- -->
            @elseif ($type === 'short-answer-questions')
                <table width="100%" cellpadding="4" cellspacing="0"
                    style="border-collapse:collapse; margin-left:20px; margin-bottom:6px;">
                    @for ($i = 0; $i < 3; $i++)
                        <tr>
                            <td style="height:22px; border-bottom:1px dashed #999;"></td>
                        </tr>
                    @endfor
                </table>
                @if ($userType === 'teacher' && $q->answer_text)
                    <table width="100%" cellpadding="6" cellspacing="0"
                        style="border-collapse:collapse; margin-left:20px; margin-bottom:6px;">
                        <tr>
                            <td style="border:1px dashed #999; padding:6px; font-style:italic;">Answer:
                                {!! $q->answer_text !!}</td>
                        </tr>
                    </table>
                @endif

                <!-- ---------- LONG ANSWER ---------- -->
            @elseif ($type === 'long-answer-questions')
                <table width="100%" cellpadding="4" cellspacing="0"
                    style="border-collapse:collapse; margin-left:20px; margin-bottom:6px;">
                    @for ($i = 0; $i < 8; $i++)
                        <tr>
                            <td style="height:22px; border-bottom:1px dashed #999;"></td>
                        </tr>
                    @endfor
                </table>
                @if ($userType === 'teacher' && $q->answer_text)
                    <table width="100%" cellpadding="6" cellspacing="0"
                        style="border-collapse:collapse; margin-left:20px; margin-bottom:6px;">
                        <tr>
                            <td style="border:1px dashed #999; padding:6px; font-style:italic;">Answer:
                                {!! $q->answer_text !!}</td>
                        </tr>
                    </table>
                @endif

                <!-- ---------- MATCH THE FOLLOWING (expects >=8 options, displayed as 2 columns) ---------- -->
            @elseif ($type === 'match-the-following' && $q->options->count() >= 8)
                @php
                    $leftOptions = $q->options->slice(0, 4)->values();
                    $rightOptions = $q->options->slice(4, 4)->values();
                @endphp
                <table width="100%" cellpadding="6" cellspacing="0"
                    style="border-collapse:collapse; margin-left:20px; margin-bottom:8px; border:1px solid #000;">
                    <thead>
                        <tr>
                            <th style="text-align:left; width:50%; padding:6px; background-color:#f5f5f5;">Column A
                            </th>
                            <th style="text-align:left; width:50%; padding:6px; background-color:#f5f5f5;">Column B
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < 4; $i++)
                            <tr>
                                <td style="padding:6px; vertical-align:top;">{{ chr(65 + $i) }}.
                                    {!! $leftOptions[$i]->option_text ?? '' !!}</td>
                                <td style="padding:6px; vertical-align:top;">{{ chr(97 + $i) }}.
                                    {!! $rightOptions[$i]->option_text ?? '' !!}</td>
                            </tr>
                        @endfor
                    </tbody>
                </table>

                @if ($userType === 'teacher')
                    <table width="100%" cellpadding="6" cellspacing="0"
                        style="border-collapse:collapse; margin-left:20px; margin-bottom:8px;">
                        <tr>
                            <td style="border:1px dashed #999; padding:6px; font-style:italic;">
                                <strong>Correct Matches:</strong><br>
                                @foreach ($q->options as $opt)
                                    {!! $opt->left_text ?? '' !!} → {!! $opt->right_text ?? '' !!}<br>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                @endif

                <!-- ---------- PASSAGE BASED ---------- -->
            @elseif ($type === 'passage')
                @php $data = json_decode($q->additional_data ?? '{}', true); @endphp
                @if (!empty($data['paragraph_statement']))
                    <table width="100%" cellpadding="6" cellspacing="0"
                        style="border-collapse:collapse; margin-left:20px; margin-bottom:4px;">
                        <tr>
                            <td style="font-weight:700;">{!! $data['paragraph_statement'] !!}</td>
                        </tr>
                    </table>
                @endif
                <table width="100%" cellpadding="8" cellspacing="0"
                    style="border-collapse:collapse; margin-left:20px; margin-bottom:8px; border:1px solid #ddd; background-color:#f9f9f9;">
                    <tr>
                        <td style="padding:8px; font-size:11pt;">{!! $data['paragraph'] ?? '' !!}</td>
                    </tr>
                </table>

                @if (!empty($data['questions_and_answers']))
                    @foreach ($data['questions_and_answers'] as $index => $sub)
                        <table width="100%" cellpadding="4" cellspacing="0"
                            style="border-collapse:collapse; margin-left:30px; margin-bottom:6px;">
                            <tr>
                                <td width="92%" valign="top" style="font-size:11pt; padding:4px 0;">
                                    <span style="font-weight:700; margin-right:6px;">{{ chr(65 + $index) }}.</span>
                                    <span>{!! $sub['question'] ?? '' !!}</span>
                                </td>
                                <td width="8%" valign="top"
                                    style="text-align:right; font-weight:700; padding:4px 0;"></td>
                            </tr>
                        </table>
                        <table width="100%" cellpadding="6" cellspacing="0"
                            style="border-collapse:collapse; margin-left:30px; margin-bottom:6px;">
                            <tr>
                                <td style="height:40px; border-bottom:1px dashed #999;"></td>
                            </tr>
                        </table>
                        @if ($userType === 'teacher' && !empty($sub['answer']))
                            <table width="100%" cellpadding="6" cellspacing="0"
                                style="border-collapse:collapse; margin-left:30px; margin-bottom:6px;">
                                <tr>
                                    <td style="border:1px dashed #999; padding:6px; font-style:italic;">Answer:
                                        {!! $sub['answer'] !!}</td>
                                </tr>
                            </table>
                        @endif
                    @endforeach
                @endif

                <!-- ---------- READ & CIRCLE ---------- -->
            @elseif ($type === 'read-circle')
                <table width="100%" cellpadding="6" cellspacing="0"
                    style="border-collapse:collapse; margin-left:20px; margin-bottom:6px;">
                    <tr>
                        <td style="font-size:11pt; padding:4px 0;">{!! $q->paragraph !!}</td>
                    </tr>
                    <tr>
                        <td style="font-size:11pt; padding:4px 0;">Circle from:
                            {{ implode(' | ', $q->choices ?? []) }}</td>
                    </tr>
                    <tr>
                        <td style="height:30px; border-bottom:1px dashed #999;"></td>
                    </tr>
                </table>

                <!-- ---------- CATCH-ALL (other types) ---------- -->
            @else
                <!-- If options exist, show them in option-table style -->
                @if (!empty($q->options))
                    <table width="100%" cellpadding="3" cellspacing="0"
                        style="border-collapse:collapse; margin-left:20px; margin-bottom:8px;">
                        @foreach ($q->options as $key => $opt)
                            <tr>
                                <td width="30" valign="top" style="padding:3px 6px 3px 0;">
                                    <input type="checkbox" disabled @if ($userType === 'teacher' && $opt->is_correct) checked @endif
                                        style="transform:scale(1.15);">
                                </td>
                                <td width="30" valign="top" style="padding:3px 6px 3px 0; font-weight:700;">
                                    {{ chr(97 + $key) }}.</td>
                                <td valign="top" style="padding:3px 0; font-size:11pt;">{!! $opt->option_text !!}
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            @endif

            <!-- End of a non-passage question block: small separator -->
            @if ($q->question_type != 'passage')
                <table width="100%" cellpadding="0" cellspacing="0"
                    style="border-collapse:collapse; margin-bottom:6px;">
                    <tr>
                        <td style="height:4px;"></td>
                    </tr>
                </table>
            @endif
        @endforeach
    @endforeach

    <!-- END OF PAPER -->
    <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse; margin-top:18px;">
        <tr>
            <td style="text-align:center; font-weight:700;">*** END OF QUESTION PAPER ***</td>
        </tr>
    </table>

</body>

</html>
