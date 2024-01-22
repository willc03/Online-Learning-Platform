<x-structure.wrapper title="{{ $course->title }}">
    {{-- Toggle owner admin view --}}
    @if($user_is_owner)
        <form action="{{ url()->current() }}">
            <x-components.3d_button class="course-button-mini" name="editing" value="{{ $is_editing ? 'false' : 'true' }}" id="admin-button" fg-color="#9EC5AB" bg-color="#5e9c73">{{ $is_editing ? 'Disable admin mode' : 'Enable admin mode' }}</x-components.3d_button>
        </form>
    @endif
    {{-- Course details --}}
    <h1>{{ $course->title }}</h1>
    <p id="course-owner" class="mini-text">By <span class="italicise">{{ $owner->name }}</span></p>
    @if ($course->description !== null)
        <p id="course-description">{{ $course->description }}</p>
    @endif
    {{-- Display all the course content in a downwards fashion --}}
    <h2>Course content</h2>
    @if ($is_editing)
        <x-components.3d_button class="course-button-mini" id="reorder-sections-button" fg-color="#9EC5AB" bg-color="#5e9c73" data-active="false">Re-order sections</x-components.3d_button>
    @endif
    {{-- Display the course sections --}}
    <div id="course-sections">
        @foreach($course->sections as $course_section)
            <div class="section draggable-choice" id="{{ $course_section->id }}">
                <button class="collapse-button" id="{{ $course_section->id }}">{{ $course_section->title }}</button>
                <div class="collapse-section" id="{{ $course_section->id }}">
                    @if($course_section->description)
                        <p>{{ $course_section->description }}</p>
                    @endif
                    {{-- Display all the items in the section --}}
                    @foreach($course_section->items as $section_item)
                        {{-- Add the item accordingly with a switch-case --}}
                        @switch($section_item->item_type)
                            @case("LESSON")
                                <h4>LESSON</h4>
                                <p>{{ $section_item }}</p>
                                @break
                        @endswitch
                    @endforeach
                </div>
            </div>
        @endforeach
        {{-- If the user is an admin, allow them to add additional sections --}}
        @if($user_is_owner && $is_editing)
            <x-components.3d_button class="course-button-mini" id="add-section-button" fg-color="#9EC5AB" bg-color="#5e9c73" onclick="$(addSectionForm).animate({height: $(addSectionForm).data('size')})">Add new section</x-components.3d_button>
            <form id="course-section-add-form">
                <fieldset>
                    <legend>New Section</legend>
                    <label class="required" for="title">Section title:</label>
                    <input type="text" name="title" required>

                    <label for="description">Section description:</label>
                    <textarea name="description" id="new-section-description"></textarea>

                    <div class="submit-buttons">
                        <x-components.3d_button value="false" fg_color="#CA6565" bg_color="#A23636" onclick="$(addSectionForm).animate({height: '0px'}, 500)">Cancel</x-components.3d_button>
                        <x-components.3d_button value="true" fg_color="#B1CA65" bg_color="#88A236">Add section</x-components.3d_button>
                    </div>
                </fieldset>
            </form>
            <script>
                const addSectionForm = $('#course-section-add-form');
                $(addSectionForm)
                    .data('size', $(addSectionForm).height())
                    .css('height', '0')
                    .submit(function( event ) {
                        event.preventDefault();
                        $.ajax({
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            method: "POST",
                            url: ajaxRoute,
                            data: {
                                'course_id': courseId,
                                'edit_type': 'new_section',
                                'data': JSON.stringify($(this).serializeArray())
                            },
                            success: function(data) {
                                if (data === 'SUCCESS') {
                                    location.href = location.href;
                                }
                            }
                        });
                    });
            </script>
        @endif
    </div>

    <script src="{{ asset("assets/scripts/courses/collapse_sections.js") }}"></script>

    @if ($is_editing)
        <script>
            ajaxRoute = '{{ url(route('course.edit', ['id' => $course->id])) }}';
            courseId = '{{ $course->id }}';
        </script>
        <script src="{{ asset("assets/scripts/courses/admin/section_reorder.js") }}"></script>
    @endif
</x-structure.wrapper>
