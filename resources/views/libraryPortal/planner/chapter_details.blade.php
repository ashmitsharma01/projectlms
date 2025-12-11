@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <nav aria-label="breadcrumb ">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('daily.planner') }}">Daily Planner</a></li>
            <li class="breadcrumb-item active" aria-current="page">Chapter Details</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-8 pe-md-1 mb-3 mb-md-0">
            <div class="cardBox planDetails">
                <h6 class="m-0 pb-3 fw-semibold">Lesson Plan Detail</h6>
                <div class="accordion accordion-flush mb-4" id="lessonAccordion">
                    <div class="accordion" id="lessonAccordion">
                        {{-- @dd($groupedDetails)  --}}
                        @foreach ($groupedDetails as $type => $details)
                            <div class="accordion-item">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#type-{{ $loop->index }}">
                                    {{-- <span class="numbaring">{{ $loop->iteration }}</span> --}}
                                    {{ ucwords(str_replace('_', ' ', $type)) }}
                                </button>
                                <div id="type-{{ $loop->index }}" class="accordion-collapse collapse"
                                    data-bs-parent="#lessonAccordion">
                                    <div class="accordion-body">
                                        <ul class="accordianUl">
                                            @foreach ($details as $detail)
                                                <li>
                                                    <figure class="m-0">
                                                        @if ($detail->image)
                                                            <img src="{{ Storage::url('uploads/planner-files/' . $detail->image) }}"
                                                                alt="Image">
                                                        @else
                                                            @php
                                                                // Determine the icon based on the title
                                                                $icon =
                                                                    strtolower(str_replace(' ', '-', $type)) . '.svg';
                                                                // dd($icon);
                                                            @endphp
                                                            <img src="{{ asset('frontend/images/planner-icons/' . $icon) }}"
                                                                alt="{{ $type }}">
                                                            {{-- <img src="{{ asset('frontend/images/default-image.jpg') }}"
                                                                alt="default"> --}}
                                                        @endif
                                                    </figure>
                                                    <div>
                                                        <strong>{{ $loop->iteration }}. {{ $detail->title }}</strong>
                                                        <p>{{ $detail->description }}</p>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <h6 class="m-0 pb-3 fw-semibold">Digital Contents</h6>
                <div class="row px-md-1">
                    <ul class="chapterList documentList">
                        @foreach ($digitalContent->chapters as $data)
                            <li>
                                <div class="chapterBtn">
                                    <figure class="position-relative">
                                        @if (in_array($data->file_extension, [
                                                'mp4',
                                                'avi',
                                                'mov',
                                                'm4v',
                                                'm4p',
                                                'mpg',
                                                'mp2',
                                                'mpeg',
                                                'mpe',
                                                'mpv',
                                                'm2v',
                                                'wmv',
                                                'flv',
                                                'mkv',
                                                'webm',
                                                '3gp',
                                                'm2ts',
                                                'ogv',
                                                'ts',
                                                'mxf',
                                            ]))
                                            <button type="button" class="plybtn" data-bs-toggle="modal"
                                                data-bs-target="#coursePreview-{{ $data->id }}">
                                                <img src="{{ asset('frontend/images/video-icon.svg') }}"
                                                    alt="Video Icon" /></button>
                                            <div class="modal coursePrv" id="coursePreview-{{ $data->id }}"
                                                tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content rounded-0 border-0"
                                                        style="    background: rgba(0, 0, 0, .5);color: #fff;">
                                                        <div class="modal-header border-0">
                                                            <h1 class="modal-title fs-5 fw-normal">Video Preview
                                                            </h1>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-0">
                                                            <p class="py-2 px-3 fs-8">{{ $data->chapter_name }}</p>
                                                            {{-- <video width="100%" height="240" controls
                                                                controlsList="nodownload" oncontextmenu="return false;">
                                                                <source src="{{ $data->signed_url }}"
                                                                    type="video/mp4">
                                                            </video> --}}
                                                            <video width="100%" height="240" controls
                                                                controlsList="nodownload" oncontextmenu="return false;">
                                                                <source
                                                                    src="{{ Storage::url('uploads/course_chapter_files/' . $data->attachment_file) }}"
                                                                    type="video/mp4">
                                                            </video>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif (str_contains($data->file_extension, 'mp3') || str_contains($data->file_extension, 'wav'))
                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $data->attachment_file) }}"
                                                target="_blank"> <img src="{{ asset('frontend/images/audio-icon.svg') }}"
                                                    alt="Audio Icon">
                                            </a>
                                        @elseif (str_contains($data->file_extension, 'jpg') || str_contains($data->file_extension, 'png'))
                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $data->attachment_file) }}"
                                                target="_blank">
                                                <img src="{{ asset('frontend/images/jpg-icon.svg') }}" alt="Audio Icon">
                                            </a>
                                        @elseif (str_contains($data->file_extension, 'pdf'))
                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $data->attachment_file) }}"
                                                target="_blank"> <img src="{{ asset('frontend/images/pdf-icon.svg') }}"
                                                    alt="PDF Icon">
                                            </a>
                                        @elseif (str_contains($data->file_extension, 'xlsx'))
                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $data->attachment_file) }}"
                                                target="_blank">
                                                <img src="{{ asset('frontend/images/xls-img.svg') }}" alt="xls Icon">
                                            </a>
                                        @elseif (str_contains($data->file_extension, 'docx'))
                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $data->attachment_file) }}"
                                                target="_blank"> <img
                                                    src="{{ asset('frontend/images/wordpress-icon.svg') }}" alt="PDF Icon">
                                            </a>
                                        @else
                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $data->attachment_file) }}"
                                                target="_blank">
                                                <img src="{{ asset('frontend/images/default-icon.svg') }}" width="100%"
                                                    height="100px" alt="Default Icon">
                                            </a>
                                        @endif
                                    </figure>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @if ($supportingFiles && $supportingFiles->isNotEmpty())
                    <hr class="form-divider-school">
                    <h6 class="m-0 pb-3 fw-semibold">Important Documents</h6>
                    <div class="footerBtn">
                        @foreach ($supportingFiles as $data)
                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $data->attachment_file) }}"
                                target="_blank"
                                class="btn btn-primary-gradient rounded-1">{{ preg_replace('/[^a-zA-Z\s]/', '', Str::replace('-', ' ', strtok($data->attachment_file, '.'))) }}
                            </a>
                        @endforeach
                        <a href="{{ route('chapter.supporting-documents.download', $folderId) }}"
                            class="btn bg-success rounded-1">
                            <img src="{{ asset('frontend/images/download-icon-white.svg') }}" width="14"
                                class="me-2">
                            Download Documents
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="cardBox lessonDetail">
                <h6 class="m-0 pb-3 fw-semibold">Lesson Detail</h6>
                <div class="d-flex align-items-center gap-3 mb-3" title="{{ $digitalContent->chapter_name }}">
                    <figure class="m-0">
                        <img src="{{ asset('frontend/images/chepter-icon.jpg') }}" alt="" width="35">
                    </figure>
                    <span>{{ Str::limit($digitalContent->chapter_name, 30, '...') }}</span>
                </div>
                <div class="actualStatus">
                    <b>Expected Status</b>
                    <span>{{ $actualPercentage }}%</span>
                    <div class="progress" role="progressbar" aria-label="Basic example"
                        aria-valuenow="{{ $actualPercentage }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: {{ $actualPercentage }}%"></div>
                    </div>
                    <strong class="pt-2">
                        Estimated Status ~ 100%
                        @if (Carbon\Carbon::today()->lt($startDate))
                            (Course has not started yet)
                        @endif
                    </strong>
                </div>

                <div class="actualStatus mt-2">
                    <b>Actual Status</b>
                    <span>{{ $estimatedPercentage }}%</span>
                    <div class="progress" role="progressbar" aria-label="Basic example"
                        aria-valuenow="{{ $estimatedPercentage }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: {{ $estimatedPercentage }}%"></div>
                    </div>
                    <strong class="pt-2">
                        Estimated Status ~ 100%
                        @if (Carbon\Carbon::today()->lt($startDate))
                            (Course has not started yet)
                        @endif
                    </strong>
                </div>

                <div class="table-responsive tbleDiv detailTable mt-3">
                    <table class="table">
                        <tr>
                            <td>Board:</td>
                            <td>{{ $plannerLesson->board->name }}</td>
                        </tr>
                        <tr>
                            <td>Medium:</td>
                            <td>{{ $plannerLesson->medium->name }}</td>
                        </tr>
                        <tr>
                            <td>Series:</td>
                            <td>{{ $plannerLesson->series->name }}</td>
                        </tr>
                        <tr>
                            <td>Class:</td>
                            <td>{{ $plannerLesson->class->name }}</td>
                        </tr>
                        <tr>
                            <td>Subject:</td>
                            <td>{{ $plannerLesson->subject->name }}</td>
                        </tr>
                        <tr>
                            <td>Chapter No:</td>
                            <td>{{ $digitalContent->sort_order }}</td>
                        </tr>
                        <tr>
                            <td>Chapter Title:</td>
                            <td>{{ Str::limit($digitalContent->chapter_name, 30, '...') }}</td>
                        </tr>
                        <tr>
                            <td>Allotted Days:</td>
                            <td>{{ $plannerLesson->allotted_days }}</td>
                        </tr>
                        <tr>
                            <td>Start Date:</td>
                            <td>{{ $plannerLesson->start_date }}</td>
                        </tr>
                        <tr>
                            <td>End Date:</td>
                            <td>{{ $plannerLesson->completion_date }}</td>
                        </tr>
                        <tr>
                            <td>Total Periods:</td>
                            <td>{{ $plannerLesson->total_periods }}</td>
                        </tr>

                    </table>
                </div>

                @php
                    $role = getUserRoles();
                    $schoolId = Auth::id();
                    if ($role === 'school_teacher') {
                        $schoolId = Auth::user()->userAdditionalDetail->school_id;
                    }
                    $isCompleted = App\Models\SchoolCompletedPlanner::where('planner_id', $plannerLesson->id)
                        ->where('school_id', $schoolId)
                        ->first();
                @endphp
                <form method="POST" action="{{ route('sp.confirm.planner.complete', $plannerLesson->id) }}">
                    @csrf
                    @if ($role == 'school_admin')
                        @if ($isCompleted && $isCompleted->completion_date >= $plannerLesson->completion_date)
                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="fas fa-check-circle me-1"></i> Already Marked Completed
                            </button>
                        @elseif($isCompleted == null && $plannerLesson->completion_date > now()->toDateString())
                            <button type="button" class="btn btn-sm btn-secondary rounded-0" data-bs-toggle="modal"
                                disabled>
                                <i class="fas fa-check-double me-1"></i> Mark as Complete
                            </button>
                        @elseif($isCompleted == null && $plannerLesson->completion_date <= now()->toDateString())
                            <button type="button" class="btn btn-sm btn-success rounded-0 btn-attention"
                                data-bs-toggle="modal" data-bs-target="#completionConfirmationModal">
                                <i class="fas fa-check-double me-1"></i> Mark as Complete
                            </button>
                        @endif
                    @else
                        @if ($isCompleted && $isCompleted->completion_date >= $plannerLesson->completion_date)
                            <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="fas fa-check-circle me-1"></i> Already Marked Completed
                            </button>
                        @elseif($isCompleted == null && $plannerLesson->completion_date > now()->toDateString())
                            <button type="button" class="btn btn-sm btn-secondary rounded-0 " data-bs-toggle="modal"
                                disabled>
                                <i class="fas fa-check-double me-1"></i> Mark as Complete
                            </button>
                        @elseif($isCompleted == null && $plannerLesson->completion_date == now()->toDateString())
                            <button type="button" class="btn btn-sm btn-success rounded-0 btn-attention"
                                data-bs-toggle="modal" data-bs-target="#completionConfirmationModal">
                                <i class="fas fa-check-double me-1"></i> Mark as Complete
                            </button>
                        @elseif($isCompleted == null && $plannerLesson->completion_date < now())
                            <button type="button" class="btn btn-sm btn-warning text-black" disabled>
                                <i class="fas fa-clock me-1"></i> Pending Completion Status
                            </button>
                        @endif
                    @endif
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="completionConfirmationModal" tabindex="-1"
        aria-labelledby="completionConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body text-center  pt-0">
                    <p class="fs-5">
                        You're about to mark <br>
                        <span class="fst-italic fw-bold bg-light rounded px-2 py-1 d-inline-block">
                            "Today's Planner"
                        </span> <br>
                        as <span class="text-success fw-bold">COMPLETE!</span>
                    </p>
                </div>

                <div class="modal-footer border-0 justify-content-center">
                    <a href="/not-yet-page" class="btn btn-lg btn-outline-secondary px-4">
                        <i class="fas fa-arrow-left me-2"></i> Not Yet
                    </a>
                    <form method="POST" action="{{ route('sp.confirm.planner.complete', $plannerLesson->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-lg btn-success px-4" id="confirmCompletionAction">
                            <i class="fas fa-check-double me-2"></i> Confirm Complete
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>



@endsection
