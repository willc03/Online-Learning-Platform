<x-structure.wrapper title="{{ $course->title }}">
    {{-- Toggle owner admin view --}}
    @if($user_is_owner)
        <a href="{{ url()->current() }}?editing={{ !request()->has('editing') || request()->input('editing') !== 'true' ? 'true' : 'false' }}">{{ !request()->has('editing') || request()->input('editing') !== 'true' ? 'Enable admin mode' : 'Disable admin mode' }}</a>
    @endif
    {{-- Course details --}}
    <h1>{{ $course->title }}</h1>
    <p id="course-owner" class="mini-text">By <span class="italicise">{{ $owner->name }}</span></p>
    @if ($course->description !== null)
        <p id="course-description">{{ $course->description }}</p>
    @endif
    {{-- Display all the course content in a downwards fashion --}}
    <h2>Course content</h2>
    @if ($user_is_owner && request()->has('editing') && request()->input('editing') === 'true')
        <x-components.3d_button id="reorder-sections-button" fg-color="#a491d3" bg-color="#7a5fbf" data-active="false">Re-order sections</x-components.3d_button>
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

    <script>
        $(".section .collapse-button").on("click", function() {
            var contentElement = $(this).next();
            var isCollapsed = contentElement.hasClass("collapsed");

            if ($(contentElement).hasClass("collapsing")) {
                return;
            }
            $(contentElement).addClass("collapsing");

            $(this).toggleClass("collapsed");
            contentElement.toggleClass("collapsed").animate({
                height: isCollapsed ? contentElement.prop('scrollHeight') + "px" : "0px",
                paddingTop: isCollapsed ? "10px" : "0",
                paddingBottom: isCollapsed ? "10px" : "0"
            }, 1000, function() {
                $(contentElement).removeClass("collapsing");
            });
        });
    </script>

    @if ($user_is_owner && request()->has('editing') && request()->input('editing') === 'true')
        <script>
            ajaxRoute = '{{ url(route('course.edit', ['id' => $course->id])) }}';
            courseId = '{{ $course->id }}';
        </script>
        <script src="{{ asset("assets/scripts/courses/admin/section_reorder.js") }}"></script>
    @endif
</x-structure.wrapper>
