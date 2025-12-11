{{-- Subject Card Grid --}}
<div class="row px-md-1">
    @foreach ($subjects as $index => $subject)
        <div class="col-md-4 col-lg-3 mb-3 px-md-2">
            <div class="languageBox subjectListDiv h-100 position-relative pt-0">
                <h6 class="dataName mb-3">{{ $subject->name }}</h6>

                @if ($subject->book_cover_image || $subject->thumbnail_image)
                    <a href="#imagesModal-{{ $index }}" data-bs-toggle="modal">
                        <img src="{{ Storage::url($subject->book_cover_image ? $subject->book_cover_image : $subject->thumbnail_image) }}"
                            class="cornerImg">
                    </a>

                    <!-- Magnify Image Modal -->
                    <div class="modal fade imagesModal" id="imagesModal-{{ $index }}">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                                <div class="modal-body">
                                    <img
                                        src="{{ Storage::url($subject->book_cover_image ? $subject->book_cover_image : $subject->thumbnail_image) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div style="height: 225px; display:flex; justify-content:center; align-items:center;">
                        <img src="{{ asset('images/mittlearn-favicon.png') }}">
                    </div>
                @endif

                <p style="min-height:20px" class="fw-semibold text-center mt-2">
                    {{ $subject->course_name ?? 'N/A' }}</p>

                <div class="d-xxl-flex justify-content-between gap-2 align-items-center">
                    <a href="{{ route('sp.course.listing', [$subject->id, $classId]) }}"
                        class="btn btn-primary-gradient rounded-1 py-2 w-100 mb-2">Explore</a>

                    @if ($subject->course_id)
                        <a href="{{ route('sp.courses.details', ['id' => $subject->course_id, 'classId' => $classId, 'subjectId' => $subject->id]) }}"
                            class="btn btn-success rounded-1 py-2 w-100 mb-2">View Content</a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
{{-- End Subject Card Grid --}}
