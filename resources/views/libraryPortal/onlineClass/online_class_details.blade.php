@extends('libraryPortal.layouts.master')
@section('content')
@include('admin.layouts.flash-messages')
    <nav aria-label="breadcrumb ">
        <ol class="breadcrumb mb-3">
            <li class="breadcrumb-item"><a href="{{ route('online.class') }}">Online Classes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Online Classe Details</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-lg-8 col-md-12 pe-lg-1 mb-3 mb-lg-0">
            <div class="cardBox p-0 chapterDetail">
                <!-- code -->
                {{-- <video style="width: 100%;height:300px;object-fit: cover;" controls="">
                    <source src="{{ asset('frontend/images/video1.mp4') }}" type="video/mp4">
                </video> --}}
                {{-- @dd($data) --}}
                <div class="p-3">
                    <div class="d-md-flex justify-content-between align-items-center w-100">
                        <div class="chapterNme mb-2">
                            <h4>{{ $data[0]->title }}</h4>
                            <span>Instructor : {{ $data[0]->instructor->name ?? 'Unknown' }} | Duration :
                                {{ \Carbon\Carbon::parse($data[0]->start_time)->diffInMinutes(\Carbon\Carbon::parse($data[0]->end_time)) }}
                                Min</span>
                        </div>
                        {{-- <span class="fw-medium fs-9 text-secondary pb-2 d-block">CHAPTERS 1 / 12</span> --}}
                    </div>

                    <div class="">
                        <span class="fw-medium text-primary fs-7 mb-2 d-block">CLASS DETAILS</span>
                        <p>{{ $data[0]->agenda }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="cardBox goingBoxmain">
                <h2 class="fs-6 fw-semibold mb-3">Study Material</h2>
                <div class="materialStudy">
                    <span><strong>Documents ( <b class="text-black">{{ count($studyMaterial) }}</b> )</strong></span>
                    {{-- <div class="row px-md-1 mb-3">
                            <div class="col-md-3 col-lg-6 px-md-2 mb-3">
                                <div class="digitalBox">
                                    <figure>
                                        <img src="{{ asset('frontend/images/digital-content-img1.jpg') }}" alt="">
                                        <a href=""><img src="{{ asset('frontend/images/play-gray-icon.jpg') }}"
                                                alt=""></a>
                                    </figure>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-6 px-md-2 mb-3">
                                <div class="digitalBox">
                                    <figure>
                                        <img src="{{ asset('frontend/images/digital-content-img2.jpg') }}" alt="">
                                        <a href=""><img src="{{ asset('frontend/images/play-gray-icon.jpg') }}"
                                                alt=""></a>
                                    </figure>
                                </div>
                            </div>
                        </div> --}}
                    {{-- <span>PDF <strong>Documents ( <b class="text-black">3</b> )</strong></span> --}}
                    <div class="row ">
                        @foreach ($studyMaterial as $item)
                            <div class="col-md-4 col-xl-6 px-2 mb-3">
                                <a href="">
                                    <div class="classesBx">
                                        <figure>
                                            {{-- @dd($item->file_extension) --}}
                                            @if (in_array($item->file_extension, ['mp4','avi','mov','m4v','m4p','mpg','mp2','mpeg','mpe','mpv','m2v','wmv','flv','mkv','webm','3gp','m2ts','ogv','ts','mxf']))
                                                <!-- For video files, display video icon -->
                                                <a href="{{ Storage::url('uploads/media-files/' . $item->attachment_file) }}"
                                                    target="_blank">
                                                    <img src="{{ asset('frontend/images/video-icon.svg') }}"
                                                        alt="Video Icon" />
                                                </a>
                                            @elseif (str_contains($item->file_extension, 'jpg') || str_contains($item->file_extension, 'png'))
                                                <a href="{{ Storage::url('uploads/media-files/' . $item->attachment_file) }}"
                                                    target="_blank">
                                                    <img src="{{ asset('frontend/images/jpg-icon.svg') }}"
                                                        alt="Audio Icon">
                                                </a>
                                            @elseif (str_contains($item->file_extension, 'pdf'))
                                                <a href="{{ Storage::url('uploads/media-files/' . $item->attachment_file) }}"
                                                    target="_blank"> <img src="{{ asset('frontend/images/pdf-icon.svg') }}"
                                                        alt="PDF Icon">
                                                </a>
                                            @elseif (str_contains($item->file_extension, 'xlsx'))
                                                <a href="{{ Storage::url('uploads/media-files/' . $item->attachment_file) }}"
                                                    target="_blank">
                                                    <img src="{{ asset('frontend/images/xls-img.svg') }}" alt="xls Icon">
                                                </a>
                                            @elseif (str_contains($item->file_extension, 'docx'))
                                                <a href="{{ Storage::url('uploads/media-files/' . $item->attachment_file) }}"
                                                    target="_blank"> <img
                                                        src="{{ asset('frontend/images/wordpress-icon.svg') }}"
                                                        alt="PDF Icon">
                                                </a>
                                            @else
                                                <img src="{{ asset('frontend/images/default-icon.svg') }}"
                                                    alt="Default Icon">
                                            @endif

                                        </figure>
                                    </div>
                                </a>

                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
