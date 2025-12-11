@extends('libraryPortal.layouts.master')
@section('content')
    <style>
        #createFolder .modal-dialog {
            max-width: 450px;
        }
    </style>
    @include('admin.layouts.flash-messages')
    <div class="cardBox">
        <div class="headingBx d-block d-md-flex">
            <h4 class="fs-5 mb-2 mb-md-0">Mittsure Important Resourses/Templates</h4>
        </div>
        <div class="row m-0">
            <div id="search-results" class="row mt-3 mb-2">
                @if ($mittlearnFolderListing && $mittlearnFolderListing->isNotEmpty())
                    @foreach ($mittlearnFolderListing as $data)
                        <div class="col-md-4 col-xl-3 px-2 mb-3 position-relative class-item"
                            data-title="{{ $data->folder_name }}" title="{{ $data->folder_name }}">
                            <a href="{{ route('content.folder.view', $data->id) }}" class="digitaluplBox h-100"
                                style="background-color: {{ $data->folder_color }};">
                                <figure class="m-0">
                                    <img src="{{ asset($data->folder_icon) }}" alt="">
                                </figure>
                                <span class="folder-name">{{ Str::limit($data->folder_name, 12, '...') }}
                                    <b>{{ $data->file_count_count }} Files</b></span>


                            </a>
                        </div>
                    @endforeach
                @else
                    <p>
                        No Important Resourses/Templates available yet. We're working on uploading important resources and
                        templates for you. Please check back soon!
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="cardBox mt-4">
        <div class="headingBx d-block d-md-flex">
            <h4 class="fs-5 mb-2 mb-md-0">Your Uploaded Content</h4>
            <div class="d-flex align-items-center gap-2">
                <div class="searchContent">
                    <div class="searchContent">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search folder">
                    </div>
                </div>

                <button type="button" class="btn btn-primary-gradient-folder rounded-1 " data-bs-toggle="tooltip"
                    data-bs-placement="bottom" data-bs-title="Create Folder"><span data-bs-target="#createFolder"
                        data-bs-toggle="modal">Create New Folder</span></button>
            </div>
        </div>
        <h6 class="m-0 pb-3 fw-semibold mt-2">School Digital Content</h6>
        <div class="row m-0">

            <div id="search-results" class="row mt-3">
                @if ($folderListing && $folderListing->isNotEmpty())
                    @foreach ($folderListing as $data)
                        <div class="col-md-4 col-xl-3 px-2 mb-3 position-relative class-item"
                            data-title="{{ $data->folder_name }}" title="{{ $data->folder_name }}">
                            <a href="{{ route('content.folder.view', $data->id) }}" class="digitaluplBox h-100"
                                style="background-color: {{ $data->folder_color }};">
                                <figure class="m-0">
                                    <img src="{{ asset($data->folder_icon) }}" alt="">
                                </figure>
                                <span class="folder-name">{{ Str::limit($data->folder_name, 12, '...') }}
                                    <b> Available to : {{ ucfirst($data->available_to_users) }}</b>
                                    <b>{{ $data->file_count_count }} Files</b></span>


                            </a>
                            <!-- Delete Button -->
                            <button type="button"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-5 mediaDelete"
                                onclick="confirmDelete('{{ route('content.delete', $data->id) }}')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    <p>
                        School Digital Content has no data.
                        <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#createFolder">
                            Click here to create
                        </a>.
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="cardBox mt-4">
        <h6 class="m-0 pb-3 fw-semibold mt-2">Teacher Digital Content</h6>
        <div class="row m-0">

            <div id="search-results" class="row mt-3">
                @if ($teacherFolderListing && $teacherFolderListing->isNotEmpty())
                    @foreach ($teacherFolderListing as $data)
                        <div class="col-md-4 col-xl-3 px-2 mb-3 position-relative class-item"
                            data-title="{{ $data->folder_name }}" title="{{ $data->folder_name }}">
                            <a href="{{ route('content.folder.view', $data->id) }}" class="digitaluplBox h-100"
                                style="background-color: {{ $data->folder_color }};">
                                <figure class="m-0">
                                    <img src="{{ asset($data->folder_icon) }}" alt="">
                                </figure>
                                <span class="folder-name">{{ Str::limit($data->folder_name, 12, '...') }}
                                    <b>{{ $data->file_count_count }}
                                        Files</b></span>
                            </a>
                            <!-- Delete Button -->
                            <button type="button"
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-5 mediaDelete"
                                onclick="confirmDelete('{{ route('content.delete', $data->id) }}')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    @endforeach
                @else
                    <p>
                        Teacher Digital Content has no data.
                        <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#createFolder">
                            Click here to create
                        </a>.
                    </p>
                @endif
            </div>
        </div>
    </div>
    <div class="modal fade" id="createFolder">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header align-items-start border-0">
                    <div class="">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Create New Folder</h1>
                        <p class="m-0 text-muted">Organize and manage digital content into structured folders.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('sp.create.folder') }}" method="POST">
                        @csrf <!-- Include CSRF token for security -->
                        <div class="formPanel">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group mb-3">
                                        {!! Form::label('available_to_users', 'Available to Users', ['class' => 'mt-2 required']) !!}
                                        {!! Form::select('available_to_users', config('constants.AVAILABLE_TO_USERS'), null, [
                                            'class' => 'form-select',
                                            'placeholder' => '--Select--',
                                        ]) !!}

                                        <label for="folder_name"class="mt-2 required">Enter Folder Name</label>
                                        <input type="text" class="form-control" id="folder_name" name="folder_name"
                                            placeholder="Enter here" required>
                                        @if (getUserRoles() == 'school_teacher')
                                            <label for="folder_name"class="mt-2">Select Class</label>
                                            {!! Form::select('class_id', $teacherClasses, null, [
                                                'class' => 'form-select',
                                                'placeholder' => '--Select--',
                                                'required',
                                            ]) !!}
                                            <label for="folder_name"class="mt-2 ">Select Subject</label>
                                            {!! Form::select('subject_id', $teacherSubject, null, [
                                                'class' => 'form-select',
                                                'placeholder' => '--Select--',
                                                'required',
                                            ]) !!}
                                        @endif
                                        <label class="mt-2 required" for="folder_color">Select Folder Color</label>
                                        <input type="color" class="form-control" id="folder_color" value="#DBF8EA"
                                            name="folder_color" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-end flex-column">
                            <button type="submit" class="btn btn-primary-gradient rounded-1 mb-2">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        var globalVar = {
            page: 'content_upload',
        };
    </script>

@endsection
