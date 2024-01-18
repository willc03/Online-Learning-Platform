<x-structure.wrapper title="{{ $course->title }}">
    {{-- Toggle owner admin view --}}
    @if($user_is_owner)
        <a href="{{ url()->current() }}?editing={{ !request()->has('editing') || request()->input('editing') !== 'true' ? 'true' : 'false' }}">{{ !request()->has('editing') || request()->input('editing') !== 'true' ? 'Enable admin mode' : 'Disable admin mode' }}</a>
    @endif
    {{-- Course details --}}
    <h1>{{ $course->title }}</h1>
    <p id="course-owner" class="mini-text">By <span class="italicise">{{ $owner->name }}</span></p>
    @if ($course->description !== null)
        <br>
        <p id="course-description">{{ $course->description }}</p>
        <br>
    @endif
    {{-- Display all the course content in a downwards fashion --}}
    <h2>Course content</h2>
    <div id="course-sections">
        @foreach($course->sections as $course_section)
            <div class="section" id="{{ $course_section->id }}">
                <button class="collapse-button" id="{{ $course_section->id }}">{{ $course_section->title }}</button>
                <div class="collapse-section" id="{{ $course_section->id }}">
                    <h3>{{ $course_section->title }}</h3>
                    @foreach($course_section->items as $section_item)
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

            $(this).toggleClass("collapsed");
            contentElement.toggleClass("collapsed").animate({
                height: isCollapsed ? contentElement.prop('scrollHeight') + "px" : "0px",
                paddingTop: isCollapsed ? "10px" : "0",
                paddingBottom: isCollapsed ? "10px" : "0"
            }, 1000);
        });
    </script>
</x-structure.wrapper>
