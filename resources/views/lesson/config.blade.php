<x-structure.wrapper title="Configure Lesson">

                    @case("TEXT")
                        <div class="lesson-config text flex-col" id="{{ $item->id }}">
                            <div class="display-case">
                                <h2>{{ $item->item_title }}</h2>
                                @if($item->description)
                                    <h3>{{ $item->description }}</h3>
                                @else
                                    <h3>This text item has no description.</h3>
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
        <form id="new-lesson-item" method="post" class="flex-col"> {{-- REMEMBER TO IMPLEMENT THE ACTION --}}
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
