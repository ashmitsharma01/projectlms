@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <div class="cardBox teacherMain py-md-4  mb-3">
        <div class="row">
            <div class="col-md-8 mb-3">
                <div class="teacherLeft">
                    <h5 class="fw-semibold">Media Gallery</h5>
                    <p>Effortlessly organize and manage all your event function media files — photos, videos, and video
                        links — in one convenient location, easily accessible for your users.</p>
                    @if (getUserRoles() == 'school_admin')
                        <button type="button" class="btn btn-primary-gradient-folder rounded-1 " data-bs-toggle="tooltip"
                            data-bs-placement="bottom" data-bs-title="Create Folder"><span data-bs-target="#createFolder"
                                data-bs-toggle="modal">Create New Gallery</span></button>
                    @endif
                </div>
            </div>
            <div class="col-md-4">
                <div class="teacherRighr position-relative">
                    <img src="{{ asset('frontend/images/student-manager-img.svg') }}" alt=""
                        class="teacherImg studentImg">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="teacherTable">
                <div class="headerTbl">
                    <h6 class="m-0">Media Gallery</h6>
                </div>
                <div class="px-3 py-2">
                    <div class="table-responsive tbleDiv">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Album/Gallery Name</th>
                                    <th>Event Name</th>
                                    <th>Available to Users</th>
                                    <th>Available Till Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
                                @foreach ($mediaGallery as $data)
                                    <tr>
                                        <td>{{ $data->gallery_name }}</td>
                                        <td>{{ $data->event_name }}</td>
                                        {{-- @dump($data->available_to_users) --}}
                                        <td>{{ config('constants.AVAILABLE_TO_USERS')[$data->available_to_users] ?? '' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($data->validity_date)->format('d/m/Y') }}
                                        </td>
                                        <td> <a href="{{ route('media.gallery.view', $data->id) }}"
                                                class="bg-transparent border-0 p-0">
                                                <img src="{{ asset('frontend/images/icon-eye.svg') }}" alt=""
                                                    width="28">
                                            </a></td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- <div class="customPagination mt-4">
                        <ul class="pagination">
                            <li class="page-item {{ $students->onFirstPage() ? 'disabled' : '' }} previous-item">
                                <a class="page-link" href="{{ $students->previousPageUrl() }}">
                                    <span><img src="{{ asset('frontend/images/arrowprw.svg') }}" width="6"></span>
                                </a>
                            </li>

                            @foreach ($students->getUrlRange(1, $students->lastPage()) as $page => $url)
                                <li class="page-item {{ $page == $students->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach

                            <li class="page-item {{ $students->hasMorePages() ? '' : 'disabled' }} next-item">
                                <a class="page-link" href="{{ $students->nextPageUrl() }}">
                                    <span><img src="{{ asset('frontend/images/arrownxt.svg') }}" width="6"></span>
                                </a>
                            </li>
                        </ul>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createFolder">
        <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- Added modal-lg for larger size -->
            <div class="modal-content border-0">
                <div class="modal-header align-items-start border-0">
                    <div class="">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Create New Album/Gallery</h1>
                        <p class="m-0">Add details for your new album or gallery</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <hr class="form-divider">

                <div class="modal-body">
                    {!! Form::open([
                        'route' => 'sp.media.gallery.create',
                        'method' => 'POST',
                        'enctype' => 'multipart/form-data',
                        'files' => true,
                    ]) !!}
                    @csrf
                    <div class="formPanel">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-3">
                                    {!! Form::label('gallery_name', 'Album/Gallery Name', ['class' => 'mt-2 required']) !!}
                                    {!! Form::text('gallery_name', null, [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter album/gallery name',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-3">
                                    {!! Form::label('available_to_users', 'Available to Users', ['class' => 'mt-2 required']) !!}
                                    {!! Form::select('available_to_users', config('constants.AVAILABLE_TO_USERS'), null, [
                                        'class' => 'form-select',
                                        'placeholder' => '--Select--',
                                    ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-3">
                                    {!! Form::label('event_name', 'Event Name <small class="text-muted">(Annual function, Republic Day etc.)</small>', ['class' => 'mt-2 required'],false) !!}
                                    {!! Form::text('event_name', null, [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter event ',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-3">
                                    {!! Form::label('validity_date', 'Content Available Till Date <small class="text-muted">(Max 6 months)</small>', ['class' => 'mt-2 required'],false) !!}
                                    {!! Form::date('validity_date', null, [
                                        'class' => 'form-control',
                                        'min' => \Carbon\Carbon::now()->format('Y-m-d'),
                                        'max' => \Carbon\Carbon::now()->addMonths(6)->format('Y-m-d'),
                                    ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-3">
                                    {!! Form::label('media_file', 'Media File <small class="text-muted">(Upload only photos here.)</small>', ['class' => 'mt-2'],false) !!}
                                    {!! Form::file('media_file', [
                                        'class' => 'form-control',
                                    ]) !!}
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group mb-3">
                                    {!! Form::label(
                                        'media_link',
                                        'Media Links <small class="text-muted">(In Case Of Video, YouTube links etc.)</small>',
                                        ['class' => 'mt-2'],
                                        false,
                                    ) !!}
                                    {!! Form::text('media_link', null, [
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter media URL',
                                    ]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-group mb-3">
                                    {!! Form::label('description', 'Description of Event/Album', ['class' => 'mt-2']) !!}
                                    {!! Form::textarea('description', null, [
                                        'class' => 'form-control',
                                        'rows' => 3,
                                        'placeholder' => 'Enter description',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-end flex-column">
                        <button type="submit" class="btn btn-primary-gradient rounded-1 mb-2">Submit</button>
                        <button type="button" class="btn backbtn fw-regular" data-bs-dismiss="modal"
                            aria-label="Close">Back</button>
                    </div>
                    {!! Form::close() !!}
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
