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
        <x-components.3d_button id="reorder-sections-button" fg-color="#9EC5AB" bg-color="#5e9c73" data-active="false">Re-order sections</x-components.3d_button>
    @if ($is_editing)
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
    </div>



    <script src="{{ asset("assets/scripts/courses/admin/section_reorder.js") }}"></script>

    @if ($is_editing)
        <script>
            ajaxRoute = '{{ url(route('course.edit', ['id' => $course->id])) }}';
            courseId = '{{ $course->id }}';
        </script>
        <script src="{{ asset("assets/scripts/courses/admin/section_reorder.js") }}"></script>
    @endif
</x-structure.wrapper>
