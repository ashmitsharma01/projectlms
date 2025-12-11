@extends('libraryPortal.layouts.master')

@section('content')
    @include('admin.layouts.flash-messages')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('content.upload') }}">Content Upload</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $folder->folder_name }}</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12">
            <div class="cardBox classDetails">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <h6 class="m-0 fw-semibold">{{ $folder->folder_name }}</h6>
                    <div class="plannerHeader">
                        <span>Filter by:</span>
                        <div class="d-flex gap-md-3 flex-wrap">
                            {{--  <select class="form-select">
                                    <option selected="">Select Subject</option>
                                </select>  --}}
                            <select class="form-select" id="typeFilter">
                                <option selected="all" {{ request()->query('type') === 'all' ? 'selected' : '' }}>All file
                                    type</option>
                                <option value="image" {{ request()->query('type') === 'image' ? 'selected' : '' }}>Image
                                </option>
                                <option value="video" {{ request()->query('type') === 'video' ? 'selected' : '' }}>Video
                                </option>
                                <option value="document" {{ request()->query('type') === 'document' ? 'selected' : '' }}>
                                    Document</option>
                            </select>
                        </div>
                        @if ($folder->is_mittlearn_folder == 0)
                            <button type="button" class="btn btn-primary-gradient-folder rounded-1 "
                                data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Add New Content"><span
                                    data-bs-target="#createFile" data-bs-toggle="modal">Add Content</span></button>
                        @endif
                    </div>
                </div>
                <div class="classesCourse mb-4">
                    <div class="row">
                        <div id="search-results" class="row mt-3">
                            @if ($contentFolderView && $contentFolderView->count() > 0)
                                @foreach ($contentFolderView as $data)
                                    <div class="col-xl-2 col-lg-3 col-md-3 mb-3 px-2 position-relative class-item"
                                        data-title="{{ $data->original_name }}" title="{{ $data->original_name }}">
                                        <div class="classesBx">
                                            <figure>
                                                @if (str_contains($data->file_extension, 'mp3') || str_contains($data->file_extension, 'wav'))
                                                    <a href="{{ Storage::url('uploads/media-files/' . $data->attachment_file) }}"
                                                        target="_blank"> <img
                                                            src="{{ asset('frontend/images/audio-icon.svg') }}"
                                                            alt="Audio Icon">
                                                    </a>
                                                @elseif (in_array($data->file_extension, [
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
                                                    <!-- For video files, display video icon -->
                                                    <a href="{{ Storage::url('uploads/media-files/' . $data->attachment_file) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('frontend/images/video-icon.svg') }}"
                                                            alt="Video Icon" />
                                                    </a>
                                                @elseif (str_contains($data->file_extension, 'jpg') || str_contains($data->file_extension, 'png'))
                                                    <a href="{{ Storage::url('uploads/media-files/' . $data->attachment_file) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('frontend/images/jpg-icon.svg') }}"
                                                            alt="Audio Icon">
                                                    </a>
                                                @elseif (str_contains($data->file_extension, 'pdf'))
                                                    <a href="{{ Storage::url('uploads/media-files/' . $data->attachment_file) }}"
                                                        target="_blank"> <img
                                                            src="{{ asset('frontend/images/pdf-icon.svg') }}"
                                                            alt="PDF Icon">
                                                    </a>
                                                @elseif (str_contains($data->file_extension, 'xlsx'))
                                                    <a href="{{ Storage::url('uploads/media-files/' . $data->attachment_file) }}"
                                                        target="_blank">
                                                        <img src="{{ asset('frontend/images/xls-img.svg') }}"
                                                            alt="xls Icon">
                                                    </a>
                                                @elseif (str_contains($data->file_extension, 'docx'))
                                                    <a href="{{ Storage::url('uploads/media-files/' . $data->attachment_file) }}"
                                                        target="_blank"> <img
                                                            src="{{ asset('frontend/images/wordpress-icon.svg') }}"
                                                            alt="PDF Icon">
                                                    </a>
                                                @else
                                                    <img src="{{ asset('frontend/images/default-icon.svg') }}"
                                                        alt="Default Icon">
                                                @endif

                                            </figure>
                                            <span>{{ Str::limit($data->original_name, 12, '...') }}</span>
                                            <p>{{ \Carbon\Carbon::parse($data->created_at)->format('d M Y') }}</p>
                                        </div>
                                        @if ($folder->is_mittlearn_folder == 0)
                                            <button type="button"
                                                class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-5 mediaDelete"
                                                onclick="confirmDelete('{{ route('content.folder.file.delete', $data->id) }}')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="col-xl-2 col-lg-3 col-md-3 mb-3 px-2">
                                    <span>No Data Available</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="createFile">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-header align-items-start border-0">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Upload Files</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <form action="{{ route('sp.content.store.files') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="folderChoosefile" id="dropArea">
                                <div id="fileName" class=""></div> <!-- Display uploaded file name -->
                                <label for="uploader">
                                    <img src="{{ asset('frontend/images/download-file.svg') }}" alt=""
                                        width="25">
                                    <span>Choose file to upload</span>
                                    <p class="m-0 mb-4">or drag and drop</p>
                                    <small class="mt-4">
                                        <strong>Note:</strong> These file types are allowed for upload: JPG, JPEG, PNG, SVG,
                                        DOC, DOCX, XLS, XLSX, PDF, MP3, WAV, MP4, MOV, and AVI.
                                        <small> <input type="file" name="file" id="uploader" class="d-none">
                                            <input type="hidden" name="folder_id" value="{{ $folder->id }}">
                                </label>
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
            const dropArea = document.getElementById('dropArea');
            const fileInput = document.getElementById('uploader');
            const fileNameDisplay = document.getElementById('fileName');
            fileNameDisplay.style.display = 'none'; // Hide drag-and-drop text

            // Prevent default behavior for drag-and-drop events
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, (e) => e.preventDefault());
                dropArea.addEventListener(eventName, (e) => e.stopPropagation());
            });

            // Highlight the drop area on dragover
            dropArea.addEventListener('dragover', () => {
                dropArea.classList.add('dragover');
            });

            // Remove highlight on dragleave or drop
            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.classList.remove('dragover');
                });
            });

            // Handle file drop
            dropArea.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files; // Assign dropped files to the file input
                    displayFileName(files[0]); // Display the file name
                }
            });

            // Display file name
            const displayFileName = (file) => {
                fileNameDisplay.style.display = 'block'; // Hide drag-and-drop text
                fileNameDisplay.textContent = `Selected File: ${file.name}`;
            };

            // Handle file selection through the file input
            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    displayFileName(fileInput.files[0]);
                }
            });
        </script>
    </div>
    <script>
        var globalVar = {
            page: 'content_folder_view',
        };
    </script>
    <script>
        document.getElementById('typeFilter').addEventListener('change', function() {
            var selectedValue = this.value;

            var url = new URL(window.location.href);
            if (selectedValue) {
                url.searchParams.set('type', selectedValue);
            } else {
                url.searchParams.delete('type');
            }

            window.location.href = url.toString();
        });
    </script>
@endsection
