<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="classTab">
        <div>
            <div class="dailypHeader d-md-flex">
                <div class="d-flex flex-column">
                    <span>Select Stage</span>
                </div>
                <ul class="filterButtonUl">
                    <ul class="filterButtonUl">
                        <li>
                            <button type="button" class="filterbutton active" data-scroll-target="day-1">Stage
                                1</button>
                        </li>
                        <li>
                            <button type="button" class="filterbutton" data-scroll-target="day-6">Stage
                                2</button>
                        </li>
                        <li>
                            <button type="button" class="filterbutton" data-scroll-target="day-11">Stage
                                3</button>
                        </li>
                        <li>
                            <button type="button" class="filterbutton" data-scroll-target="day-16">Stage
                                4</button>
                        </li>
                    </ul>
            </div>
            <div class="table-responsive tbleDiv plannerTblFix">
                <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <th class="text-start" style="vertical-align: middle;">Subjects</th>
                            @foreach ($allDates as $index => $date)
                                @php
                                    $dayName = $weekDays[$date->format('w')]; // Get weekday name
                                @endphp
                                <th class="day-header">
                                    <div class="d-flex justify-content-between">
                                        <span>Day {{ $index + 1 }}
                                            <b>{{ $dayName }}</b>
                                        </span>
                                        <button type="button" class="btnremoveBg" data-bs-toggle="modal"
                                            data-bs-target="#statusMdl" data-school-id="{{ $schoolId }}"
                                            data-day-index="{{ $index + 1 }}"
                                            data-date="{{ $date->format('Y-m-d') }}">
                                            <img src="{{ asset('frontend/images/sorting-icon.svg') }}" alt=""
                                                width="25">
                                        </button>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects as $subject)
                            <tr>
                                <td class="text-start fw-semibold">{{ $subject->subject->name }}</td>
                                @foreach ($allDates as $index => $date)
                                    @php
                                        $day = $index + 1; // Adjust day number for dayWiseData
                                    @endphp
                                    <td>
                                        @if (isset($dayWiseData[$day][$subject->subject->id]))
                                            @foreach ($dayWiseData[$day][$subject->subject->id] as $chapter)
                                                @if (!empty($chapter['chapter_id']))
                                                    <a href="{{ route('chapter.details', $chapter['chapter_id']) }}"
                                                        title="{{ $chapter['title'] }}">
                                                        <div class="shiftBox {{ $chapter['class'] }}">
                                                            <strong>{{ Str::limit($chapter['title'], 20, '...') }}</strong>
                                                        </div>
                                                    </a>
                                                @else
                                                    <div class="shiftBox lightred">
                                                        <strong>No Task</strong>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <a href="javascript:void(0)">
                                                <div class="shiftBox lightred">
                                                    <strong>No Task</strong>
                                                </div>
                                            </a>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dayHeaders = document.querySelectorAll('.day-header');
        const stageButtons = document.querySelectorAll('.filterbutton');
        const currentDate = new Date();

        // Get the totalPlannerDays value from the server (pass this from Blade to JS)
        const totalPlannerDays =
            {{ $totalPlannerDays }}; // Assuming you are passing totalPlannerDays to JavaScript from the backend

        // Calculate number of days per stage
        const daysPerStage = Math.ceil(totalPlannerDays / 4);

        // Dynamically create the stages based on totalPlannerDays
        const stages = [];
        let startDay = 1;
        for (let i = 0; i < 4; i++) {
            const endDay = startDay + daysPerStage - 1;
            stages.push({
                id: `stage-${i + 1}`,
                startDay: startDay,
                endDay: endDay > totalPlannerDays ? totalPlannerDays : endDay,
            });
            startDay = endDay + 1;
        }

        // Assign IDs to headers (day-1, day-2, day-3, ...)
        dayHeaders.forEach((header, index) => {
            header.id = `day-${index + 1}`;
        });

        // Determine the current stage based on the current date
        let currentStageIndex = null;
        const currentDay = currentDate.getDate();
        stages.forEach((stage, index) => {
            if (currentDay >= stage.startDay && currentDay <= stage.endDay) {
                currentStageIndex = index;
            }
        });

        // If a current stage is found, activate it and scroll to its header
        if (currentStageIndex !== null) {
            const currentStage = stages[currentStageIndex];
            const targetHeader = document.getElementById(`day-${currentStage.startDay}`);
            stageButtons.forEach(btn => btn.classList.remove('active'));
            stageButtons[currentStageIndex].classList.add('active');
            if (targetHeader) {
                targetHeader.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'start',
                });
            }
        }

        // Handle stage button clicks
        stageButtons.forEach((button, index) => {
            button.addEventListener('click', function() {
                stageButtons.forEach(btn => btn.classList.remove('active'));

                // Add active class to clicked button
                this.classList.add('active');

                // Scroll to the corresponding day
                const targetDay = stages[index].startDay;
                const targetHeader = document.getElementById(`day-${targetDay}`);
                if (targetHeader) {
                    targetHeader.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'start',
                    });
                }
            });
        });

        // Highlight the active stage dynamically based on scroll position
        window.addEventListener('scroll', function() {
            let currentStage = null;

            // Determine the active stage based on visible header
            for (let i = stages.length - 1; i >= 0; i--) {
                const stage = stages[i];
                const header = document.getElementById(`day-${stage.startDay}`);
                if (header && header.getBoundingClientRect().left < window.innerWidth / 2) {
                    currentStage = i;
                    break;
                }
            }

            // Update active button
            if (currentStage !== null) {
                stageButtons.forEach(btn => btn.classList.remove('active'));
                stageButtons[currentStage].classList.add('active');
            }
        });
    });
</script>
