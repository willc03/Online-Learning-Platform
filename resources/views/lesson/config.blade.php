<x-structure.wrapper title="Configure Lesson">
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
                                    <h2 class="title">{{ $item->item_title }}</h2>
                                    <div class="container">
                                        <h3>Single Choice Question</h3>
                                        @if($item->description)
                                            <p>{{ $item->description }}</p>
                                        @endif
                                        <div class="answer-field single-choice-field">
                                            @foreach($item->item_value['question_choices'] as $option) {{-- $value is passed in from the question page --}}
                                            @if ($option == $item->item_value['correct_answer'])
                                                <x-components.3d_button class="option-button selected" value="{{ $option }}" fg_color="#43AA8B" bg_color="#245B4A">{{ $option }}</x-components.3d_button>
                                            @else
                                                <x-components.3d_button class="option-button" value="{{ $option }}" fg_color="#D10023" bg_color="#840016">{{ $option }}</x-components.3d_button>
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
                            <div class="display-case">
                                <h2>{{ $item->item_title }}</h2>
                                @if($item->description)
                                    <h3>{{ $item->description }}</h3>
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
        <x-components.3d_button id="add-btn" class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A">Add new item</x-components.3d_button>
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
    const formButton = $("#add-btn");
    const formBox = $("#new-lesson-item");
    const subFormBox = $(".detail-container");
    $(formButton).on('click', function() {
        if ($(formBox).data('open')) { return; }
        $(formBox)
            .animate({ height: $(formBox).data('height') }, 500, function() { $(formBox).css('height', ''); })
            .data('open', true)
    });
    $(formBox)
        .data({
            height: $(formBox).height(),
            open: false
        })
        .css({ height: 0, overflow: 'hidden' });
    $("#select-item-type").on('change', function () {
        $(subFormBox).empty();
        $.ajax({
            url: "{{ route("course.lesson.configure.form-request", [ 'id' => $course->id, 'lessonId' => $lesson->id ]) }}",
            data: {
                'form-name': $("#select-item-type").val()
            },
            success: function(response) {
                $(subFormBox).html(response);
            }
        });
    });
</script>
