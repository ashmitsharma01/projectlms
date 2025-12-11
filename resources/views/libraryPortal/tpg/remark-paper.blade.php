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
            <div class="questionsBx">
                <div class="d-flex gap-3">
                    <span>Q.12</span>
                    <p class="mb-4">Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an
                        unknown printer took a galley of type and scrambled it to make a type specimen book. It
                        has survived not only five centuries, but also the leap into electronic typesetting,
                        remaining essentially unchanged.</p>
                </div>
                <div class="mcqMain ps-0">
                    <div class="d-flex gap-3">
                        <b>Ans.</b>
                        <p class="answer">Lorem Ipsum is simply dummy text of the printing and typesetting
                            industry.
                            Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when
                            an
                            unknown printer took a galley of type and scrambled it to make a type specimen book.
                            It
                            has</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 mb-4">
                        <b class="text-black">Marks: 10</b>
                        <div class="d-flex align-items-center justify-content-end gap-2 ">
                            <b>Status:</b>
                            <input type="text" class="form-control w-50 score" value="Enter Score">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn-primary-gradient rounded-1 py-2">Submit Score </button>
                    </div>
                </div>
            </div>
            <div class="questionsBx">
                <div class="d-flex gap-3">
                    <span>Q.9</span>
                    <p class="mb-4">Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industry's standard dummy text ever since the 1500s</p>
                </div>
                <div class="mcqMain ps-0">
                    <div class="d-flex gap-3">
                        <b>Ans.</b>
                        <p class="answer">Lorem Ipsum is simply dummy text of the printing and typesetting
                            industry. Lorem Ipsum has been the industry's standard dummy text ever since the
                            1500s, when an unknown printer took a galley of type and scrambled it to make a type
                            specimen book. It has</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 mb-4">
                        <b class="text-black">Marks: 10</b>
                        <div class="d-flex align-items-center justify-content-end gap-2 ">
                            <b>Status:</b>
                            <input type="text" class="form-control w-50 score" value="Enter Score">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn-primary-gradient rounded-1 py-2">Submit Score </button>
                    </div>
                </div>
            </div>
            <div class="questionsBx">
                <div class="d-flex gap-3">
                    <span>Q.25</span>
                    <p class="mb-4">Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an
                        unknown printer took a galley of type and scrambled it to make a type specimen book. It
                        has survived not only five centuries, but also the leap into electronic typesetting,
                        remaining essentially unchanged.</p>
                </div>
                <div class="mcqMain ps-0">
                    <div class="d-flex gap-3">
                        <b>Ans.</b>
                        <p class="answer">Lorem Ipsum is simply dummy text of the printing and typesetting
                            industry. Lorem Ipsum has been the industry's standard dummy text ever since the
                            1500s, when an unknown printer took a galley of type and scrambled it to make a type
                            specimen book. It has</p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100 mb-4">
                        <b class="text-black">Marks: 10</b>
                        <div class="d-flex align-items-center justify-content-end gap-2 ">
                            <b>Status:</b>
                            <input type="text" class="form-control w-50 score" value="Enter Score">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn-primary-gradient rounded-1 py-2">Submit Score </button>
                    </div>
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
                <span class="fw-semibold fs-8">Test Title here</span>
            </div>
            <p class="text-secondary fw-medium fs-8">Created by : Admin APB</p>
            <hr>

            <b class="fw-medium text-secondary fs-8 d-block mb-3"> User Name: <span
                    class="text-black fw-normal d-block">James Smith</span> </b>
            <b class="fw-medium text-secondary fs-8 d-block mb-3"> Roll No.<span
                    class="text-black fw-normal d-block">123402</span> </b>
            <b class="fw-medium text-secondary fs-8 d-block mb-3"> Test Name:<span
                    class="text-black fw-normal d-block">testtrainee31@gmail.com</span> </b>
            <b class="fw-medium text-secondary fs-8 d-block mb-3"> Email Id:<span
                    class="text-black fw-normal d-block">testtrainee31@gmail.com</span> </b>
            <b class="fw-medium text-secondary fs-8 d-block mb-3">Test Submit Date:<span
                    class="text-black fw-normal d-block">Mathematics</span> </b>
            <b class="fw-medium text-secondary fs-8 d-block mb-3"> Passing Score:<span
                    class="text-black fw-normal d-block">50</span> </b>

        </div>
    </div>
</div>

@endsection