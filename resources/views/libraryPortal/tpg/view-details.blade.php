@extends('libraryPortal.layouts.master')
@section('content')
@include('admin.layouts.flash-messages')
<nav aria-label="breadcrumb ">
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="../admin/online-classes.html">Online Classes</a></li>
        <li class="breadcrumb-item active" aria-current="page">Course Details</li>
    </ol>
</nav>
<div class="row">
    <div class="col-lg-8 col-md-12 pe-lg-1 mb-3 mb-lg-0">
        <div class="cardBox p-0 chapterDetail">
            <!-- code -->
            <video style="width: 100%;height:300px;object-fit: cover;" controls="">
                <source src="{{ asset('frontend/images/video1.mp4') }}" type="video/mp4">
            </video>
            <div class="p-3">
                <div class="chapterNme mb-3">
                    <h4>Lorem Ipsum Simply dummy text here</h4>
                    <span>Instructor : Neha Yadav | Duration : 35:40 Min | Date & Time : 13-10-2024 02:00 PM</span>
                </div>



                <ul class="nav nav-tabs mb-3 tbs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#classDetail" type="button"
                            aria-selected="true" role="tab">CLASS DETAILS</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#studyDetail" type="button"
                            aria-selected="false" role="tab" tabindex="-1">STUDY MATERIALS</button>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="classDetail" role="tabpanel">
                        <p class="mb-3">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem
                            Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown
                            printer took a galley of type and scrambled it to make a type specimen book. It has survived
                            not only five centuries, but also the leap into electronic typesetting, remaining
                            essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets
                            containing Lorem Ipsum passages, and more recently with desktop publishing software like
                            Aldus PageMaker including versions of Lorem Ipsum.Lorem Ipsum is simply dummy text of the
                            printing and typesetting industry.</p>
                        <p>Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown
                            printer took a galley of type and scrambled it to make a type specimen book. It has survived
                            not only five centuries, but also the leap into electronic typesetting, remaining
                            essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets
                            containing Lorem Ipsum passages, and more recently with desktop publishing software like
                            Aldus PageMaker including versions of Lorem Ipsum.

                        </p>
                    </div>
                    <div class="tab-pane fade" id="studyDetail" role="tabpanel">
                        <ul class="docxList">
                            <li>
                                <button type="button" class="docxTxt">
                                    <span class="fileName"><img src="{{ asset('frontend/images/file-icon.svg') }}" width="12">
                                        introduction.pdf</span>
                                    <img src="{{ asset('frontend/images/pdf.svg') }}" width="50">
                                </button>
                            </li>
                            <li>
                                <button type="button" class="docxTxt">
                                    <span class="fileName"><img src="{{ asset('frontend/images/file-icon.svg') }}" width="12">
                                        introduction.docx</span>
                                    <img src="{{ asset('frontend/images/word.svg') }}" width="50">
                                </button>
                            </li>
                            <li>
                                <button type="button" class="docxTxt">
                                    <span class="fileName"><img src="{{ asset('frontend/images/file-icon.svg') }}" width="12">
                                        introduction.docx</span>
                                    <img src="{{ asset('frontend/images/word.svg') }}" width="50">
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12">
        <div class="cardBox goingBoxmain">
            <h2 class="fs-6 fw-semibold mb-4">Ongoing Classes</h2>
            <div class="mainboxClasses">
                <div class="ongoingBx">
                    <div class="ongoingimg">
                        <figure class="m-0">
                            <img src="{{ asset('frontend/images/classroom-study1.jpg') }}" alt="">
                        </figure>
                        <a href=""><lottie-player src="{{ asset('frontend/images/Play-button.json') }}" loop="" autoplay=""
                                style="width: 70px;height: 70px;" background="transparent"></lottie-player></a>
                        <span class="cornerTxt"><img src="{{ asset('frontend/images/user-imgcorner.svg') }}" alt="" width="16"
                                class="me-1">21</span>
                    </div>
                    <div class="ongoingTxt">
                        <h3>Lorem Ipsum Simply Dummy Text</h3>
                        <div class="profileClasses mb-3">
                            <figure class="m-0">
                                <img src="{{ asset('frontend/images/learnerimg1.png') }}" alt="">
                            </figure>
                            <strong>Neha Dubey</strong>
                        </div>
                        <div class="progress" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 25%"></div>
                        </div>
                        <div class="mt-3">
                            <span class="d-block">Subject: Social Studies</span>
                            <span>Started at: 12:00 PM</span>
                        </div>
                    </div>
                </div>
                <div class="ongoingBx">
                    <div class="ongoingimg">
                        <figure class="m-0">
                            <img src="{{ asset('frontend/images/classroom-study3.jpg') }}" alt="">
                        </figure>
                        <a href=""><lottie-player src="{{ asset('frontend/images/Play-button.json') }}" loop="" autoplay=""
                                style="width: 70px;height: 70px;" background="transparent"></lottie-player></a>
                        <span class="cornerTxt"><img src="{{ asset('frontend/images/user-imgcorner.svg') }}" alt="" width="16"
                                class="me-1">21</span>
                    </div>
                    <div class="ongoingTxt">
                        <h3>Lorem Ipsum Simply Dummy Text</h3>
                        <div class="profileClasses mb-3">
                            <figure class="m-0">
                                <img src="{{ asset('frontend/images/learnerimg1.png') }}" alt="">
                            </figure>
                            <strong>Neha Dubey</strong>
                        </div>
                        <div class="progress" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 25%"></div>
                        </div>
                        <div class="mt-3">
                            <span class="d-block">Subject: Social Studies</span>
                            <span>Started at: 12:00 PM</span>
                        </div>
                    </div>
                </div>
                <div class="ongoingBx">
                    <div class="ongoingimg">
                        <figure class="m-0">
                            <img src="{{ asset('frontend/images/classroom-study4.jpg') }}" alt="">
                        </figure>
                        <a href=""><lottie-player src="{{ asset('frontend/images/Play-button.json') }}" loop="" autoplay=""
                                style="width: 70px;height: 70px;" background="transparent"></lottie-player></a>
                        <span class="cornerTxt"><img src="{{ asset('frontend/images/user-imgcorner.svg') }}" alt="" width="16"
                                class="me-1">21</span>
                    </div>
                    <div class="ongoingTxt">
                        <h3>Lorem Ipsum Simply Dummy Text</h3>
                        <div class="profileClasses mb-3">
                            <figure class="m-0">
                                <img src="{{ asset('frontend/images/learnerimg1.png') }}" alt="">
                            </figure>
                            <strong>Neha Dubey</strong>
                        </div>
                        <div class="progress" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: 25%"></div>
                        </div>
                        <div class="mt-3">
                            <span class="d-block">Subject: Social Studies</span>
                            <span>Started at: 12:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection