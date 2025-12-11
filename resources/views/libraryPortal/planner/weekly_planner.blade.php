<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="nurseryTab">
        <div>
            <div class="dailypHeader d-md-flex">
                <div class="d-flex flex-column">
                    <span>Select Week</span>
                </div>
                <ul class="filterButtonUl">
                    @foreach ($weeks as $weekNumber => $week)
                        <li>
                            <button type="button"
                                class="filterbutton {{ now()->format('W') == $weekNumber ? 'active' : '' }}">Week
                                {{ $weekNumber }}</button>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="table-responsive tbleDiv ">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-start" style="vertical-align: middle;">Subjects</th>
                            @foreach ($weeks as $weekNumber => $week)
                                <th class="{{ now()->format('W') == $weekNumber ? 'currentBg' : '' }}"
                                    data-week-number="{{ $weekNumber }}">
                                    <span>Week {{ $weekNumber }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjects as $subject)
                            <tr>
                                <td class="text-start fw-semibold">{{ $subject->subject->name }}</td>
                                @foreach ($weeks as $weekNumber => $week)
                                    <td class="{{ now()->format('W') == $weekNumber ? 'currentBg' : '' }}"
                                        data-week-number="{{ $weekNumber }}">
                                        @if (isset($weekWiseData[$weekNumber][$subject->subject->id]))
                                            @foreach ($weekWiseData[$weekNumber][$subject->subject->id] as $chapter)
                                                <a href="javascript:void(0)" class="open-weekly-planner-modal"
                                                    data-week-number="{{ $weekNumber }}"
                                                    data-week-name="Week {{ $weekNumber }}"
                                                    data-chapters="{{ htmlspecialchars(
                                                        json_encode([
                                                            'ids' => $chapter['chapter_id'],
                                                            'titles' => array_values($chapter['titles']), // Convert associative array to indexed array
                                                        ]),
                                                        ENT_QUOTES,
                                                        'UTF-8',
                                                    ) }}">
                                                    <div class="{{ $chapter['class'] }}">
                                                        <strong>{{ Str::limit(implode(', ', $chapter['titles']), 20, '...') }}
                                                            &nbsp;&nbsp;+</strong>
                                                    </div>
                                                </a>
                                            @endforeach
                                        @else
                                            <div class="shiftBox lightred">
                                                <strong>No Task</strong>
                                            </div>
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
