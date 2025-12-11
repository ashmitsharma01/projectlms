@extends('libraryPortal.layouts.master')

@section('content')
    @include('admin.layouts.flash-messages')

    <div class="row px-lg-1">
        <div class="col-lg-12 px-lg-2 mb-3">
            <div class="cardBox adminBx h-100">
                <div class="">
                    <h5 class="fw-semibold">User Manual 📘 </h5>
                    <p>
                        Explore and download user-friendly manuals designed to help you navigate and make the most of every
                        feature in the system. Whether you're new or need a quick refresher, our step-by-step guides are
                        here to support you.
                    </p>

                </div>
                <img src="{{ asset('frontend/images/admin-img.png') }}" alt="" width="200">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="teacherTable">
                    <div class="px-3 py-2">
                        <div class="table-responsive tbleDiv">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th style="width: 10%">Title</th>
                                        <th style="width: 65%">Description</th>
                                        <th style="width: 20%">View</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @if ($manuals->isNotEmpty())
                                        @foreach ($manuals as $data)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-wrap">{{ $data->title }}</td>
                                                <td class="text-wrap">{{ $data->description }}</td>
                                                <td>
                                                    <div class="gap-2">
                                                        @if (isset($data->pdf_path))
                                                            <a href="{{ Storage::url('uploads/user_manuals/' . $data->pdf_path) }}"
                                                                target="_blank"
                                                                class="btn btn-sm btn-success rounded-1 manulBtn">
                                                                <i class="fa fa-file-pdf-o me-1"></i> PDF
                                                            </a>
                                                        @endif
                                                        @if (isset($data->video_path))
                                                            <a href="{{ Storage::url('uploads/user_manuals/' . $data->video_path) }}"
                                                                target="_blank"
                                                                class="btn btn-sm btn-primary rounded-1 manulBtn">
                                                                <i class="fa fa-file-video-o me-1"></i> Video
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Currently, there are no user
                                                manuals uploaded. Please check back soon or contact support for assistance.
                                            </td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
