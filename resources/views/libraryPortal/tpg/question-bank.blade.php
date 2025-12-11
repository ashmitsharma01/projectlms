@extends('libraryPortal.layouts.master')
@section('content')
    @include('admin.layouts.flash-messages')
    <div class="cardBox teacherMain  pt-md-4 pb-0  mb-3">
        <div class="row">
            <div class="col-md-8 mb-3">
                <div class="teacherLeft">
                    <h5 class="fw-semibold">Question Bank</h5>
                    <p>The Question Bank stores curated questions for test paper creation and individual questions, allowing
                        easy selection based on categories and difficulty.</p>

                    <a href="{{ route('sp.create.question.bank') }}" class="btn btn-primary-gradient rounded-1 ">
                        Add Question Bank</a>
                </div>
            </div>
            <div class="col-md-4  mt-auto">
                <div class="teacherRighr text-end">
                    <img src="{{ asset('frontend/images/question-bank-img.svg') }}" alt="" class=""
                        width="220">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="teacherTable">
                <div class="headerTbl">
                    <h6 class="m-0">All Question List</h6>
                    <div class="teacherrightTable">
                        <div class="tableSearch">
                            <input type="text" id="search-input" class="form-control" placeholder="Search by Question ">
                        </div>
                        <div class="dropdown">
                            <button class="bg-transparent border-0 p-0" type="button" data-bs-target="#searchBy"
                                data-bs-toggle="offcanvas">
                                <span data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Filter">
                                    <img src="{{ asset('frontend/images/filter-icon.svg') }}" alt=""></span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="px-3 py-2">
                    <div class="table-responsive tbleDiv ">
                        <table class="table mb-0" id="question-table">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Question</th>
                                    <th>Class</th>
                                    <th>Subject</th>
                                    <th>Marks</th>
                                    <th>Diff. Level</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @dd($questions) --}}
                                @foreach ($questions as $item)
                                    <tr data-name="{{ $item->question }}">
                                        <td>{{ $questions->currentPage() * $questions->perPage() - $questions->perPage() + $loop->iteration . '.' }}

                                        <td style="white-space: normal; word-break: break-word; max-width: 300px;">
                                            {!! $item->question !!}
                                        </td>
                                        <td>{{ $item->class->name }}</td>
                                        <td>{{ $item->subject->name }}</td>
                                        <td>{{ $item->marks }}</td>
                                        <td>{{ config('constants.DIFFICULTY_LEVEL')[$item->difficulty_level] }}</td>
                                        <td>
                                            <span class="{{ $item->is_active == 1 ? 'activeTxt' : 'deactiveTxt' }}">
                                                {{ $item->is_active == 1 ? config('constants.STATUS_LIST')[$item->is_active] : 'Inactive' }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="dropdown">
                                                <button class="bg-transparent border-0 p-0" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <img src="{{ asset('frontend/images/action-icon.svg') }}"
                                                        alt="" width="28">
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item"
                                                            href="{{ route('sp.question-bank.edit', $item->id) }}">Edit</a>
                                                    </li>
                                                    {{--  <li><a class="dropdown-item" href="#statusMdl{{ $item->id }}"
                                                            data-bs-toggle="modal">Delete</a>
                                                        </a>
                                                    </li>  --}}

                                                </ul>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="statusMdl{{ $item->id }}" tabindex="-1"
                                        aria-labelledby="statusMdlLabel{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header border-0 pb-0">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body pt-0">
                                                    <div class="text-center">
                                                        <lottie-player src="{{ asset('frontend/images/study-idea.json') }}"
                                                            loop="" autoplay=""
                                                            style="width: 130px;height: 130px;margin: auto;"
                                                            background="transparent"></lottie-player>
                                                        <h6 class="fw-semibold">Are you sure!</h6>
                                                        <p>Do you want to delete this test paper?</p>

                                                        <!-- Form for deletion -->
                                                        <form id="deleteForm{{ $item->id }}"
                                                            action="{{ route('sp.question-bank.delete', $item->id) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit"
                                                                class="btn btn-primary-gradient rounded-1">Yes</button>
                                                        </form>

                                                        <div>
                                                            <button type="button" class="btn btnNo"
                                                                data-bs-dismiss="modal">No</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="customPagination mt-4">
                        <div class="d-flex justify-content-right text-right">
                            {!! $questions->links('pagination::bootstrap-4') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="offcanvas offcanvas-end" id="searchBy">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fs-6 fw-semibold">Search By</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="formPanel">
                <form action="{{ route('sp.question.bank') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                {!! Form::label('class', 'Select Class') !!}
                                {{ Form::select('class', $classes, request('class'), ['class' => 'form-select', 'placeholder' => 'Select']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                {!! Form::label('subject', 'Select Subject') !!}
                                {{ Form::select('subject', $subjects, request('subject'), ['class' => 'form-select', 'placeholder' => 'Select']) }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group bginput mb-3">
                                {!! Form::label('question_type', 'Select Question Type') !!}
                                {{ Form::select('question_type', $questionTypes, request('question_type'), ['class' => 'form-select', 'placeholder' => 'Select']) }}
                            </div>
                        </div>
                    </div>
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="d-flex align-items-center justify-content-end gap-4">
                <button type="submit" class="btn btn-primary-gradient rounded-1">Submit</button>
                <a href="{{ url()->current() }}" class="btn backbtn">Clear</a>
                <button type="button" class="btn backbtn" data-bs-dismiss="offcanvas">Back</button>
            </div>
        </div>
        </form>
    </div>



    <div class="offcanvas offcanvas-end " id="questionBank">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fs-6 fw-semibold">Add Question Bank</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body ">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-semibold m-0 fs-7">Enter Details</h6>
                <label for="bulk-upload" class="btnSuccess">Bulk Upload</label>
                <input id="bulk-upload" type="file" class="d-none">
            </div>
            <div class="fileUploded">
                <div class="uploadInner">
                    <figure class="m-0">
                        <img src="{{ asset('frontend/images/xls-img.svg') }}" alt="">
                    </figure>
                    <span>Lorem_ipsum_2022.pdf</span>
                    <button type="button" class="crossbtn"><img
                            src="{{ asset('frontend/images/cross-secondary.svg') }}" alt=""
                            width="8"></button>
                </div>
            </div>
            <div class="formPanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Select Board <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Select Medium <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group bginput mb-3">
                            <label>Select Book Series <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Select Class <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Select Subject <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group bginput mb-3">
                            <label>Select Chapters <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Question Type <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Difficult Level <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Marks <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Status <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group bginput mb-3">
                            <label>Question Tittle <b>*</b></label>
                            <textarea class="form-control" placeholder="Enter" style="height: 80px;"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group bginput mb-3">
                            <label>Question Description <b>*</b></label>
                            <textarea class="form-control" placeholder="Enter" style="height: 80px;"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label>Options <b>*</b></label>
                            <div class="d-flex align-items-center gap-3">
                                <input type="text" class="form-control w-50" value="Enter the Option">
                                <button type="button" class="btn btn-success rounded-2 fs-9">Add Option</button>
                            </div>
                        </div>
                        <div class="optionDiv mb-3">
                            <div class="cstmCheckbox mt-2">
                                <input type="checkbox" id="answerCheck1">
                                <label for="answerCheck1">Mark as Correct Answer</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="d-flex align-items-center justify-content-end gap-4">
                <button type="button" class="btn backbtn">Reset</button>
                <button type="button" class="btn btn-primary-gradient rounded-1">Submit</button>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-end " id="editquestionBank">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fs-6 fw-semibold">Edit Question Bank</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body ">
            <div class="formPanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Select Board <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>CBSE</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Select Medium <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>English</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group bginput mb-3">
                            <label>Select Book Series <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Luma Learn</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Select Class <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Nursery</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Select Subject <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>English</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group bginput mb-3">
                            <label>Select Chapters <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Select</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Question Type <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>MCQ</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Difficult Level <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Medium</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Marks <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group bginput mb-3">
                            <label>Status <b>*</b></label>
                            <select class="form-select" aria-label="Default select example">
                                <option selected>Active</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group bginput mb-3">
                            <label>Question Tittle <b>*</b></label>
                            <textarea class="form-control" placeholder="Enter" style="height: 80px;"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group bginput mb-3">
                            <label>Question Description <b>*</b></label>
                            <textarea class="form-control" placeholder="Enter" style="height: 80px;"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label>Options <b>*</b></label>
                            <div class="optionDiv mb-3">
                                <div class="d-flex gap-3 align-items-center">
                                    <input type="text" class="form-control" placeholder="Option 1">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success">Add Option</button>
                                        <button type="button" class="btn btn-danger">Remove</button>
                                    </div>
                                </div>
                                <div class="cstmCheckbox mt-2 mb-3">
                                    <input type="checkbox" id="answerCheck2">
                                    <label for="answerCheck2">Mark as Correct Answer</label>
                                </div>
                                <div class="d-flex gap-3 align-items-center mb-2">
                                    <input type="text" class="form-control" placeholder="Option 2">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success">Add Option</button>
                                        <button type="button" class="btn btn-danger">Remove</button>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-center mb-2">
                                    <input type="text" class="form-control" placeholder="Option 3">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success">Add Option</button>
                                        <button type="button" class="btn btn-danger">Remove</button>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 align-items-center mb-2">
                                    <input type="text" class="form-control" placeholder="Option 4">
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-success">Add Option</button>
                                        <button type="button" class="btn btn-danger">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="offcanvas-footer">
            <div class="d-flex align-items-center justify-content-end gap-4">
                <button type="button" class="btn backbtn">Reset</button>
                <button type="button" class="btn btn-primary-gradient rounded-1">Submit</button>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('search-input');
        const tableRows = document.querySelectorAll('#question-table tbody tr');
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            tableRows.forEach(row => {
                const title = row.getAttribute('data-name').toLowerCase();
                if (title.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
@endsection
