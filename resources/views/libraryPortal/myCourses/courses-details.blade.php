@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <nav aria-label="breadcrumb">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <!-- Breadcrumb Section -->
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="{{ route('sp.my.courses') }}">Subjects/ Courses</a></li>
                <li class="breadcrumb-item"><a href="{{ route('sp.class.subject', $classId) }}">Class Subjects</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('sp.course.listing', ['id' => $subjectId, 'class_id' => $classId]) }}">Book
                        Listing</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chapters</li>

            </ol>

            <!-- Dropdown for selecting the number of chapters to display -->
            <div class="text-center mb-2">
                <label for="chapterLimit" class="fw-semibold me-2 active">Number of Chapters to Display:</label>
                <select id="chapterLimit" class="form-select d-inline-block w-auto">
                    <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10 </option>
                    <option value="15" {{ request('limit') == 15 ? 'selected' : '' }}>15 </option>
                    <option value="20" {{ request('limit') == 20 ? 'selected' : '' }}>20 </option>
                    <option value="25" {{ request('limit') == 25 ? 'selected' : '' }}>25 </option>
                    <option value="all" {{ request('limit') == 'all' ? 'selected' : '' }}>All Chapters</option>
                </select>
            </div>
            <div class="text-center mb-2">
                <form method="GET" action="{{ request()->url() }}" <label for="chapterLimit"
                    class="fw-semibold me-2 active">Content Language</label>
                    <select id="chapterLimit" class="form-select d-inline-block w-auto" onchange="this.form.submit()"
                        name='language'>
                        @foreach (config('constants.CONTENT_LANGUAGE') as $key => $lang)
                            <option value="{{ $key }}"
                                {{ request('language') == $key ? 'selected' : 'bilingual' }}>
                                {{ $lang }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </nav>


    <div class="cardBox">
        <div class="classSubjectBookName mb-1 d-flex justify-content-between align-items-center flex-wrap">
            <span class="fw-semibold">
                {{ $className }} - {{ $subjectName }} - {{ $courseName }}
            </span>

            @if ($chapters instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="customPagination m-0">
                    {{ $chapters->links('vendor.pagination.bootstrap-5') }}
                </div>
            @endif
        </div>


        @if (isset($chapters))
            <div id="chapterContainer">
                @foreach ($chapters as $data)
                    <div class="chapterBox">
                        <div class="d-flex align-items-center gap-3 chapterName mb-4">
                            <div class="chapterNumber">
                                {{ $data->sort_order }}
                            </div>
                            <div>
                                <h3 class="fs-6 fw-semibold mb-0">{{ $data->chapter_name }}</h3>
                                <span>Chapter Description:
                                    <b title="{{ $data->chapter_description }}">
                                        {{ Str::limit($data->chapter_description, 150, '...') }}
                                    </b>
                                </span>
                            </div>
                        </div>

                        <div class="chapterVideos">
                            @php
                                $language = request('language') ?? 'bilingual';
                                $chapterFiles = collect($data->chapterListing)->filter(function ($file) use (
                                    $language,
                                ) {
                                    return $file->language === $language;
                                });
                                $videos = $chapterFiles->whereIn('file_extension', [
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
                                    '3gp',
                                    'm2ts',
                                    'ogv',
                                    'ts',
                                    'mxf',
                                ]);
                                $documents = $chapterFiles->whereIn('file_extension', [
                                    'pdf',
                                    'docx',
                                    'xlsx',
                                    'jpeg',
                                    'jpg',
                                    'png',
                                ]);
                                $resources = collect($data->resources)->whereIn('file_extension', [
                                    'pdf',
                                    'docx',
                                    'xlsx',
                                    'jpeg',
                                    'jpg',
                                    'png',
                                ]);
                            @endphp
                            @if ($videos->isNotEmpty())
                                <div class="mb-4">
                                    <h4 class="fs-6 fw-semibold">Video <b>({{ $videos->count() }})</b></h4>
                                    <ul class="chapterList documentList">
                                        @foreach ($videos as $video)
                                            <li>
                                                <div class="chapterBtn">
                                                    <figure class="position-relative">
                                                        <img src="{{ asset('frontend/images/video-icon.svg') }}"
                                                            alt="Video Icon" />
                                                        <button type="button" class="plybtn" data-bs-toggle="modal"
                                                            data-bs-target="#coursePreview-{{ $video->id }}">
                                                        </button>
                                                    </figure>
                                                    <div class="w-100 p-2">
                                                        <p>{{ $video->file_name ? $video->file_name : $video->original_name }}
                                                        </p>
                                                        <div class="d-flex align-items-center gap-4">
                                                            {{--  <span><img src="{{ asset('frontend/images/clock.svg') }}"
                                                                    alt="" width="12"> 34:45</span>
                                                            <span>4.6 <img src="{{ asset('frontend/images/star3.svg') }}"
                                                                    alt="" width="18"></span>  --}}
                                                        </div>
                                                    </div>
                                                    <div class="modal coursePrv" id="coursePreview-{{ $video->id }}"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content rounded-0 border-0"
                                                                style="    background: rgba(0, 0, 0, .5);color: #fff;">
                                                                <div class="modal-header border-0">
                                                                    <h1 class="modal-title fs-5 fw-normal">Course Preview
                                                                    </h1>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body p-0">
                                                                    <p class="py-2 px-3 fs-8">
                                                                        {{ $video->sort_order }} .
                                                                        {{ $video->file_name ? $video->file_name : $data->chapter_name }}
                                                                    </p>
                                                                    <video width="100%" height="240" controls
                                                                        controlsList="nodownload"
                                                                        oncontextmenu="return false;">
                                                                        <source
                                                                            src="{{ Storage::url('uploads/course_chapter_files/' . $video->attachment_file) }}"
                                                                            type="video/mp4">
                                                                    </video>
                                                                    {{-- <video width="100%" height="240" controls
                                                                        controlsList="nodownload"
                                                                        oncontextmenu="return false;">
                                                                        <source src="{{ $video->signed_url }}"
                                                                            type="video/mp4">
                                                                    </video> --}}

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($documents->isNotEmpty())
                                <div class="mb-4">
                                    <h4 class="fs-6 fw-semibold">Document <b>({{ $documents->count() }})</b></h4>
                                    <ul class="chapterList documentList">
                                        @foreach ($documents as $document)
                                            <li>
                                                <div class="chapterBtn">
                                                    <figure>
                                                        @if (str_contains($document->file_extension, 'mp3') || str_contains($document->file_extension, 'wav'))
                                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $document->attachment_file) }}"
                                                                target="_blank"> <img
                                                                    src="{{ asset('frontend/images/audio-icon.svg') }}"
                                                                    alt="Audio Icon">
                                                            </a>
                                                        @elseif (str_contains($document->file_extension, 'jpg') ||
                                                                str_contains($document->file_extension, 'png') ||
                                                                str_contains($document->file_extension, 'jpeg'))
                                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $document->attachment_file) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('frontend/images/jpg-icon.svg') }}"
                                                                    alt="Audio Icon">
                                                            </a>
                                                        @elseif (str_contains($document->file_extension, 'pdf'))
                                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $document->attachment_file) }}"
                                                                target="_blank"> <img
                                                                    src="{{ asset('frontend/images/pdf-icon.svg') }}"
                                                                    alt="PDF Icon">
                                                            </a>
                                                        @elseif (str_contains($document->file_extension, 'xlsx'))
                                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $document->attachment_file) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('frontend/images/xls-img.svg') }}"
                                                                    alt="xls Icon">
                                                            </a>
                                                        @elseif (str_contains($document->file_extension, 'docx'))
                                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $document->attachment_file) }}"
                                                                target="_blank"> <img
                                                                    src="{{ asset('frontend/images/wordpress-icon.svg') }}"
                                                                    alt="PDF Icon">
                                                            </a>
                                                        @else
                                                            <img src="{{ asset('frontend/images/default-icon.svg') }}"
                                                                alt="Default Icon">
                                                        @endif
                                                    </figure>
                                                    <div class="w-100 p-2">
                                                        <p>{{ $document->original_name }}</p>
                                                        <div class="d-flex align-items-center gap-4">
                                                            {{--  <span><img src="{{ asset('frontend/images/clock.svg') }}"
                                                                    alt="" width="12"> 34:45</span>
                                                            <span>4.6 <img src="{{ asset('frontend/images/star3.svg') }}"
                                                                    alt="" width="18"></span>  --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($resources->isNotEmpty())
                                <hr class="form-divider">
                                <div class="mb-4 mt-2">
                                    <h4 class="fs-6 fw-semibold">Resources <b>({{ $resources->count() }})</b></h4>
                                    <ul class="chapterList documentList">
                                        @foreach ($resources as $document)
                                            <li>
                                                <div class="chapterBtn">
                                                    <figure>
                                                        @if (str_contains($document->file_extension, 'mp3') || str_contains($document->file_extension, 'wav'))
                                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $document->attachment_file) }}"
                                                                target="_blank"> <img
                                                                    src="{{ asset('frontend/images/audio-icon.svg') }}"
                                                                    alt="Audio Icon">
                                                            </a>
                                                        @elseif (str_contains($document->file_extension, 'jpg') ||
                                                                str_contains($document->file_extension, 'png') ||
                                                                str_contains($document->file_extension, 'jpeg'))
                                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $document->attachment_file) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('frontend/images/jpg-icon.svg') }}"
                                                                    alt="Audio Icon">
                                                            </a>
                                                        @elseif (str_contains($document->file_extension, 'pdf'))
                                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $document->attachment_file) }}"
                                                                target="_blank"> <img
                                                                    src="{{ asset('frontend/images/pdf-icon.svg') }}"
                                                                    alt="PDF Icon">
                                                            </a>
                                                        @elseif (str_contains($document->file_extension, 'xlsx'))
                                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $document->attachment_file) }}"
                                                                target="_blank">
                                                                <img src="{{ asset('frontend/images/xls-img.svg') }}"
                                                                    alt="xls Icon">
                                                            </a>
                                                        @elseif (str_contains($document->file_extension, 'docx'))
                                                            <a href="{{ Storage::url('uploads/course_chapter_files/' . $document->attachment_file) }}"
                                                                target="_blank"> <img
                                                                    src="{{ asset('frontend/images/wordpress-icon.svg') }}"
                                                                    alt="PDF Icon">
                                                            </a>
                                                        @else
                                                            <img src="{{ asset('frontend/images/default-icon.svg') }}"
                                                                alt="Default Icon">
                                                        @endif
                                                    </figure>
                                                    <div class="w-100 p-2">
                                                        <p>{{ preg_replace('/[^a-zA-Z\s]/', '', Str::replace('-', ' ', strtok($document->attachment_file, '.'))) }}
                                                        </p>
                                                        <div class="d-flex align-items-center gap-4">
                                                            {{--  <span><img src="{{ asset('frontend/images/clock.svg') }}"
                                                                    alt="" width="12"> 34:45</span>
                                                            <span>4.6 <img src="{{ asset('frontend/images/star3.svg') }}"
                                                                    alt="" width="18"></span>  --}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if ($chapters instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="customPagination mt-4">
                        {{ $chapters->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            </div>
        @endif
    </div>
    <style>
        .small {
            margin-top: 1rem !important;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let chapterLimitSelect = document.getElementById("chapterLimit");

            chapterLimitSelect.addEventListener("change", function() {
                let limit = this.value;
                let currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('limit', limit);
                currentUrl.searchParams.set('page', 1); // reset to first page
                window.location.href = currentUrl.toString();
            });
        });
    </script>

@endsection
