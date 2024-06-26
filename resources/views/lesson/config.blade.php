<x-structure.wrapper title="Configure Lesson">
    <div class="flex-row button-row">
        <x-ui.interactive-button class="course-button-mini right-spacer" fg_color="#43AA8B" bg_color="#245B4A" onclick="location.href = '{{ route('course.home', [ 'id' => $course->id, 'editing' => 'true' ]) }}'">Return to course</x-ui.interactive-button>
        <x-ui.interactive-button class="course-button-mini" fg_color="#43AA8B" bg_color="#245B4A" onclick="location.href = '{{ route('course.home', [ 'id' => $course->id ]) }}'">Return to course (without admin mode)</x-ui.interactive-button>
    </div>
    <x-ui.interactive-button id="re-order" class="course-button-mini" fg_color="#43AA8B" bg_color="#245B4A">Re-order components</x-ui.interactive-button>
    {{-- Display all the items on the lesson --}}
    <div class="lesson-items">
        @if($lesson->items->count() === 0)
            <p>This lesson does not have any components, use the form below to add your first component.</p>
        @else
            @foreach($lesson->items as $item)
                {{-- Display different content depending on the type of item --}}
                @switch($item->item_type)
                    {{-- Display lesson questions --}}
                    @case('QUESTION')

                        @switch($item->item_value['question_type'])

                            @case('single_choice')
                                <div class="lesson-config question single-choice flex-col" id="{{ $item->id }}">
                                    <h2 class="flex-row"><span class="title var-width">{{ $item->item_title }}</span>
                                        <x-ui.interactive-button class="course-button-mini remove-bottom-spacer" id="delete-button" fg_color="#CA6565" bg_color="#A23636"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-ui.interactive-button>
                                    </h2>
                                    <div class="container">
                                        <h3>Single Choice Question</h3>
                                        @if($item->description)
                                            <p>{{ $item->description }}</p>
                                        @endif
                                        <div class="answer-field single-choice-field">
                                            @foreach($item->item_value['question_choices'] as $option)
                                                {{-- $value is passed in from the question page --}}
                                                @if ($option == $item->item_value['correct_answer'])
                                                    <x-ui.interactive-button class="option-button selected" value="{{ $option }}" fg_color="#43AA8B" bg_color="#245B4A">{{ $option }}</x-ui.interactive-button>
                                                @else
                                                    <x-ui.interactive-button class="option-button" value="{{ $option }}" fg_color="#D10023" bg_color="#840016">{{ $option }}</x-ui.interactive-button>
                                                @endif
                                            @endforeach
                                        </div>
                                        @if(!$item->item_value['one_time_answer'])
                                            <p><span class="italicise">This question allows users to change their answer before submitting.</span></p>
                                        @endif
                                    </div>
                                </div>
                                @break

                            @case('multiple_choice')
                                <div class="lesson-config question multiple-choice flex-col" id="{{ $item->id }}">
                                    <h2 class="flex-row"><span class="title var-width">{{ $item->item_title }}</span>
                                        <x-ui.interactive-button class="course-button-mini remove-bottom-spacer" id="delete-button" fg_color="#CA6565" bg_color="#A23636"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-ui.interactive-button>
                                    </h2>
                                    <div class="container">
                                        <h3>Multiple Choice Question</h3>
                                        @if($item->description)
                                            <p>{{ $item->description }}</p>
                                        @endif
                                        <div class="answer-field multi-choice-field">
                                            @foreach($item->item_value['question_choices'] as $option)
                                                {{-- $value is passed in from the question page --}}
                                                @if (in_array($option, $item->item_value['correct_answers']))
                                                    <x-ui.interactive-button class="option-button selected" value="{{ $option }}" fg_color="#43AA8B" bg_color="#245B4A">{{ $option }}</x-ui.interactive-button>
                                                @else
                                                    <x-ui.interactive-button class="option-button" value="{{ $option }}" fg_color="#D10023" bg_color="#840016">{{ $option }}</x-ui.interactive-button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @break

                            @case('true_or_false')
                                <div class="lesson-config question true-false flex-col" id="{{ $item->id }}">
                                    <h2 class="flex-row"><span class="title var-width">{{ $item->item_title }}</span>
                                        <x-ui.interactive-button class="course-button-mini remove-bottom-spacer" id="delete-button" fg_color="#CA6565" bg_color="#A23636"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-ui.interactive-button>
                                    </h2>
                                    <div class="container">
                                        <h3>True or False Question</h3>
                                        @if($item->description)
                                            <p>{{ $item->description }}</p>
                                        @endif
                                        <div class="answer-field true-false-field">
                                            {{-- Display the true button --}}
                                            @if($item->item_value['correct_answer'])
                                                <x-ui.interactive-button class="option-button selected" fg_color="#43AA8B" bg_color="#245B4A">True</x-ui.interactive-button>
                                            @else
                                                <x-ui.interactive-button class="option-button" fg_color="#43AA8B" bg_color="#245B4A" disabled>True</x-ui.interactive-button>
                                            @endif
                                            {{-- Display the false button --}}
                                            @if($item->item_value['correct_answer'])
                                                <x-ui.interactive-button class="option-button" fg_color="#43AA8B" bg_color="#245B4A" disabled>False</x-ui.interactive-button>
                                            @else
                                                <x-ui.interactive-button class="option-button selected" fg_color="#43AA8B" bg_color="#245B4A">False</x-ui.interactive-button>
                                            @endif
                                        </div>
                                        @if(!$item->item_value['one_time_answer'])
                                            <p><span class="italicise">This question allows users to change their answer before submitting.</span></p>
                                        @endif
                                    </div>
                                </div>
                                @break
                            @case('match')
                                <div class="lesson-config question match flex-col" id="{{ $item->id }}">
                                    <h2 class="flex-row"><span class="title var-width">{{ $item->item_title }}</span>
                                        <x-ui.interactive-button class="course-button-mini remove-bottom-spacer" id="delete-button" fg_color="#CA6565" bg_color="#A23636"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-ui.interactive-button>
                                    </h2>
                                    <div class="container">
                                        <h3>Match Question</h3>
                                        @if($item->description)
                                            <p>{{ $item->description }}</p>
                                        @endif
                                        <div class="answer-field match-field flex-col">
                                            @foreach($item->item_value['items_to_match'] as $answer)
                                                <div class="flex-row match-row">
                                                    <x-ui.interactive-button class="answer-button" fg_color="#81d4fa" bg_color="#5a94af">{{ $answer[0] }}</x-ui.interactive-button>
                                                    <x-ui.interactive-button class="answer-button" fg_color="#81d4fa" bg_color="#5a94af">{{ $answer[1] }}</x-ui.interactive-button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @break
                            @case('wordsearch')
                                <div class="lesson-config question wordsearch flex-col" id="{{ $item->id }}">
                                    <h2 class="flex-row"><span class="title var-width">{{ $item->item_title }}</span>
                                        <x-ui.interactive-button class="course-button-mini remove-bottom-spacer" id="delete-button" fg_color="#CA6565" bg_color="#A23636"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-ui.interactive-button>
                                    </h2>
                                    <div class="container">
                                        <h3>Word Search Question</h3>
                                        @if($item->description)
                                            <p>{{ $item->description }}</p>
                                        @endif
                                        <div class="answer-field wordsearch-field flex-col">
                                            @php
                                                $max = 0;
												$words = [];
												foreach($item->item_value['words'] as $wordSet) {
													$words[] = $wordSet[0];
													if (strlen($wordSet[0]) > $max) {
														$max = strlen($wordSet[0]);
													}
												}
                                                $puzzle = \WordSearch\Factory::create($words, $max + 5);
                                            @endphp
                                            <p>Example word search:</p>
                                            <div class="wordsearch flex-col middle">
                                                @foreach($puzzle->toArray() as $row)
                                                    <div class="row flex-row">
                                                        @foreach($row as $letter)
                                                            <div class="letter">
                                                                <p>{{ $letter }}</p>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endforeach
                                            </div>
                                            <p><span class="italicise">Word searches are randomly generated upon loading.</span></p>
                                        </div>
                                    </div>
                                </div>
                                @break
                            @case('order')
                                <div class="lesson-config question order flex-col" id="{{ $item->id }}">
                                    <h2 class="flex-row"><span class="title var-width">{{ $item->item_title }}</span>
                                        <x-ui.interactive-button class="course-button-mini remove-bottom-spacer" id="delete-button" fg_color="#CA6565" bg_color="#A23636"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-ui.interactive-button>
                                    </h2>
                                    <div class="container">
                                        <h3>Order Question</h3>
                                        @if($item->description)
                                            <p>{{ $item->description }}</p>
                                        @endif
                                        <div class="answer-field order-field @if ($item->item_value['direction'] == 'vertical') flex-col @else flex-row @endif">
                                            @foreach($item->item_value['correct_answer'] as $answer)
                                                @if ($item->item_value['direction'] == 'horizontal')
                                                    <x-ui.interactive-button class="answer-button max-content" fg_color="#81d4fa" bg_color="#5a94af">{{ $answer }}</x-ui.interactive-button>
                                                @else
                                                    <x-ui.interactive-button class="answer-button" fg_color="#81d4fa" bg_color="#5a94af">{{ $answer }}</x-ui.interactive-button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @break
                            @case('fill_in_blanks')
                                <div class="lesson-config question fill-blanks flex-col" id="{{ $item->id }}">
                                    <h2 class="title flex-row"><span class="title var-width">{!! str_replace("\\n", "<br>", (str_replace("%", '<span class=\'blank\'> </span>', $item->item_title))) !!}</span>
                                        <x-ui.interactive-button class="course-button-mini remove-bottom-spacer" id="delete-button" fg_color="#CA6565" bg_color="#A23636"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-ui.interactive-button>
                                    </h2>
                                    <div class="container">
                                        <h3>Fill in Blanks Question</h3>
                                        @if($item->description)
                                            <p>{{ $item->description }}</p>
                                        @endif
                                        <div class="answer-field fill-blanks-field">
                                            @foreach($item->item_value['question_choices'] as $answer)
                                                @if(in_array($answer, $item->item_value['correct_answers']))
                                                    <x-ui.interactive-button id="{{ $item->id }}-{{ array_search($answer, $item->item_value['correct_answers']) }}" class="option-button selected correct" fg_color="#43AA8B" bg_color="#245B4A">{{ $answer }}</x-ui.interactive-button>
                                                @else
                                                    <x-ui.interactive-button class="option-button" fg_color="#D10023" bg_color="#840016">{{ $answer  }}</x-ui.interactive-button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @break
                        @endswitch
                        @break

                    @case("TEXT")
                        <div class="lesson-config text flex-col" id="{{ $item->id }}">
                            <h2 class="flex-row"><span class="title var-width">Text component</span>
                                <x-ui.interactive-button class="course-button-mini remove-bottom-spacer" id="delete-button" fg_color="#CA6565" bg_color="#A23636"><img width="20px" height="20px" src="https://learn.test/assets/images/trash-can.svg"></x-ui.interactive-button>
                            </h2>
                            <div class="container display-case">
                                <h3>{{ $item->item_title }}</h3>
                                @if($item->description)
                                    <p>{{ $item->description }}</p>
                                @else
                                    <h3>This text item has no subtext.</h3>
                                @endif
                            </div>
                        </div>
                        @break
                @endswitch

            @endforeach
        @endif
    </div>
    {{-- Allow for the addition of new components to the lesson --}}
    <div class="new-item">
        <x-ui.interactive-button id="add-btn" class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A">Add new item</x-ui.interactive-button>
        <form id="new-lesson-item" method="post" action="{{ route('course.lesson.configure.add', [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}" class="flex-col">
            @csrf
            <legend>New lesson item</legend>
            <label class="form-flex">
                <span class="required">Item type:</span>
                <select id="select-item-type" name="item-type" required>
                    <option disabled selected value="null">Select an item type</option>
                    <option value="question">Question</option>
                    <option value="text">Text</option>
                </select>
            </label>
            <div class="detail-container"></div>
        </form>
    </div>
</x-structure.wrapper>

<script>
    function resetBFButtons (draggedPosition)
    {
        $("span.blank").filter(".filled").each(function (_, field) {
            const selectedOption = $(field).attr("option");
            if ( selectedOption ) {
                const optionElement = $("#" + selectedOption);
                const fieldDetails = getElementDetails(field);
                // Check if draggedPosition is provided, otherwise use default position
                optionElement.css({
                    width: fieldDetails.width, height: fieldDetails.height, left: fieldDetails.x + 1, top: fieldDetails.y - 6,
                });
            }
        });
    }

    formRequestRoute = "{{ route("course.lesson.configure.form-request", [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}";
    lessonEditRoute = "{{ route('course.lesson.configure.modify', [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}";
    lessonItemDeleteRoute = "{{ route('course.lesson.configure.modify.delete', [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}";
</script>
<script src="{{ asset("assets/scripts/courses/admin/config-wordsearch.js") }}"></script>
<script src="{{ asset("assets/scripts/courses/lessons/add_component.js") }}"></script>
<script src="{{ asset("assets/scripts/courses/lessons/manage_bf_questions.js") }}"></script>
<script src="{{ asset("assets/scripts/courses/lessons/component-order.js") }}"></script>
<script src="{{ asset("assets/scripts/courses/lessons/delete-component.js") }}"></script>
