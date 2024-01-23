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
    @if ($is_editing && $course->sections->count() > 1)
        <x-components.3d_button class="course-button-mini" id="reorder-sections-button" fg-color="#9EC5AB" bg-color="#5e9c73" data-active="false">Re-order sections</x-components.3d_button>
    @endif
    {{-- Display the course sections --}}
    <div id="course-sections">
        @foreach($course->sections as $course_section)
            <div class="section draggable-choice" id="{{ $course_section->id }}">
                <button class="collapse-button" id="{{ $course_section->id }}">{{ $course_section->title }}</button>
                <div class="collapse-section" id="{{ $course_section->id }}">
                    {{-- Add deletion buttons if the user is a course admin and is editing --}}
                    @if ($user_is_owner && $is_editing)
                        <x-components.3d_button class="course-button-mini" id="delete-button" fg_color="#CA6565" bg_color="#A23636">Delete section</x-components.3d_button>
                    @endif
                    {{-- Add description --}}
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

    {{-- If the user is an admin, allow them to add additional sections --}}
    @if($user_is_owner && $is_editing)
        <x-courses.add_section />
        <script src="{{ asset('assets/scripts/courses/admin/add_section.js') }}"></script>

        <script>
            $(".three-d.course-button-mini#delete-button").on("click", function() {
                const button = $(this);
                const foreground = $(button).children('span')[0];

                if ($(button).data("shouldDelete") === true) {
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        method: "POST",
                        url: ajaxRoute,
                        data: {
                            'course_id': courseId,
                            'edit_type': 'delete_section',
                            'data': JSON.stringify({'section_id': $(button).parent().attr('id')})
                        },
                        success: function(data) {
                            if (data === 'SUCCESS') {

                            }
                        }
                    });
                } else {
                    $(button).animate({backgroundColor: "#88A236"}, 500);
                    $(foreground).animate({backgroundColor: "#B1CA65"}, 500).text("Confirm");
                    $(button).data("shouldDelete", true);

                    setTimeout(function() {
                        $(button).data("shouldDelete", false);
                        $(button).animate({backgroundColor: $(button).attr("bg-color") || $(button).attr("bg_color") || "#ffffff"}, 500);
                        $(foreground).animate({backgroundColor: $(button).attr("fg-color") || $(button).attr("fg_color") || "#ffffff"}, 500).text("Delete section");
                    }, 7500);
                }
            })
        </script>
    @endif

    <script src="{{ asset("assets/scripts/courses/collapse_sections.js") }}"></script>

    @if ($is_editing)
        <script>
            ajaxRoute = '{{ url(route('course.edit', ['id' => $course->id])) }}';
            courseId = '{{ $course->id }}';
        </script>
        <script src="{{ asset("assets/scripts/courses/admin/section_reorder.js") }}"></script>
    @endif
</x-structure.wrapper>
