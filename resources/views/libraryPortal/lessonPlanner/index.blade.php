@extends('libraryPortal.layouts.master')
@section('content')
@include('admin.layouts.flash-messages')
    <div class="cardBox">
        <div class="d-md-flex justify-content-between align-items-center mb-3">
            <h2 class="fs-6 fw-semibold mb-3">Lesson Plan</h2>
            {{-- <div class="plannerHeader">
                <span>Filter by:</span>
                <div class="d-flex gap-md-3 flex-wrap">
                    <select class="form-select" id="mediumFilter">
                        <option disabled {{ request('medium') ? '' : 'selected' }}>Medium</option>
                        @foreach ($medium as $item)
                            <option value="{{ $item->id }}" {{ request('medium') == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div> --}}
        </div>
        <div class="row px-md-1">
            @foreach ($classCourses as $data)
                <div class="col-md-4 col-lg-3 mb-3 px-md-2">
                    <div class="exploreBox h-100">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <figure class="m-0 position-relative">
                                <span
                                    class="rounded-circle d-inline-flex justify-content-center align-items-center first-circle">
                                    <span class="rounded-circle second-circle">
                                        {{ substr($data->class->name ?? 'N/A', 0, 1) }}
                                    </span>
                                </span>
                            </figure>

                            <span>{{ $data->class->name ?? 'N/A' }}</span>
                        </div>
                        <a href="{{ route('sp.lesson.planner.subjects', $data->class_id) }}" class="btn-explore">Explore
                            <lottie-player src="{{ asset('frontend/images/right-blue.json') }}" loop autoplay
                                style="width: 20px;height: 20px;"></lottie-player></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script>
        document.getElementById('mediumFilter').addEventListener('change', function() {
            var selectedValue = this.value;

            // Check if the selected value is empty, if so remove the query parameter from URL
            var url = new URL(window.location.href);
            if (selectedValue) {
                url.searchParams.set('medium', selectedValue); // Add or update the 'medium' query parameter
            } else {
                url.searchParams.delete('medium'); // Remove the 'medium' query parameter if empty
            }

            // Redirect to the new URL with the selected filter
            window.location.href = url.toString();
        });
    </script>
@endsection
