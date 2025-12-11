@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="teacherTable">
                <div class="headerTbl">
                    <h6 class="m-2">Test Review</h6>
                </div>
                <div class="px-3 py-2 textReview">
                    <div class="table-responsive tbleDiv ">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>Test Title</th>
                                    <th>Class</th>
                                    <th>Subject</th>
                                    <th>Chapters</th>
                                    {{--  <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>  --}}
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tests as $item)
                                    <tr>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ $item->Class->name }}</td>
                                        <td>{{ $item->Subject->name }}</td>
                                        <td style="white-space: normal; word-break: break-word; max-width: 300px;">
                                            {{ implode(', ', $item->chapter_names ?? []) }}
                                        </td>
                                        {{--  <td><span class="completedbg">Completed</span></td>
                                        <td>{{ \Carbon\Carbon::parse($item->start_date_time)->timezone('Asia/Kolkata')->format('d-m-Y h:i A') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->end_date_time)->timezone('Asia/Kolkata')->format('d-m-Y h:i A') }}
                                        </td>  --}}
                                        <td>
                                            <a href="{{ route('sp.test-paper.assigned.users', $item->id) }}"
                                                class="btn btn-primary-gradient w-25 rounded-2">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="customPagination mt-4">
                        <div class="d-flex justify-content-right text-right">
                            {!! $tests->links('pagination::bootstrap-4') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
