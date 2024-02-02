<x-structure.wrapper title="{{ $course->title }}">
    {{-- Toggle owner admin view --}}
    @if($user_is_owner)
        <div class="flex-row" id="admin-row">
            <form action="{{ url()->current() }}" method="get">
                <x-components.3d_button class="course-button-mini max-content" name="editing" value="{{ $is_editing ? 'false' : 'true' }}" id="admin-button" fg-color="#9EC5AB" bg-color="#5e9c73">{{ $is_editing ? 'Disable admin mode' : 'Enable admin mode' }}</x-components.3d_button>
            </form>
            <form action="{{ route('course.settings', ['id' => $course->id]) }}" method="get">
                <x-components.3d_button class="course-button-mini max-content" id="settings-button" fg-color="#9EC5AB" bg-color="#5E9C73">Course settings</x-components.3d_button>
            </form>
        </div>
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
    <div id="course-sections" class="flex-col">
        @foreach($course->sections as $course_section)
            <div class="section" id="{{ $course_section->id }}">
                <button class="collapse-button" id="{{ $course_section->id }}">{{ $course_section->title }}</button>
                <div class="collapse-section" id="{{ $course_section->id }}">
                    {{-- Add deletion buttons if the user is a course admin and is editing --}}
                    @if ($user_is_owner && $is_editing)
                        <div class="section-admin-panel">
                            <h4>Admin Controls</h4>
                            <x-components.3d_button class="course-button-mini" id="delete-button" fg_color="#CA6565" bg_color="#A23636">Delete section</x-components.3d_button>
                            <x-components.3d_button class="max-content course-button-mini" fg-color="#9EC5AB" bg-color="#5e9c73">Add new component</x-components.3d_button>
                            @if ($course_section->items->count() > 1)
                                <x-components.3d_button class="course-button-mini" id="reorder-section-button" fg-color="#9EC5AB" bg-color="#5e9c73" data-active="false">Re-order components</x-components.3d_button>
                            @endif
                        </div>
                        {{-- Display the form to add new sections --}}
                        <div id="section-add-component">
                            <x-courses.add_component course_id="{{ $course->id }}" section_id="{{ $course_section->id }}" />
                        </div>
                    @endif
                    {{-- Add description --}}
                    @if($course_section->description)
                        <p>{{ $course_section->description }}</p>
                    @endif
                    {{-- Display all the items in the section --}}
                    @if($is_editing) <div class="section-content"> @endif
                        @foreach($course_section->items as $section_item)
                            {{-- Add the item accordingly with a switch-case --}}
                            @switch($section_item->item_type)
                                @case("LESSON")
                                    <div class="lesson flex-col" id="{{ $section_item->id }}">
                                        <h5>{{ $section_item->title }}</h5>
                                        @if($section_item->description != null)
                                            <p>{{ $section_item->description }}</p>
                                        @endif
                                        <x-components.3d_button fg-color="#9EC5AB" bg-color="#5e9c73" onclick="location.href = '{{ url()->current() }}'">BEGIN LESSON</x-components.3d_button>
                                    </div>
                                    @break
                                @case("TEXT")
                                    <div class="text" id="{{ $section_item->id }}">
                                        <p>{{ $section_item->title }}</p>
                                    </div>
                                    @break
                            @endswitch
                        @endforeach
                    @if($is_editing) </div> @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- If the user is an admin, allow them to add additional sections --}}
    @if($user_is_owner && $is_editing)
        <x-courses.add_section />
    @endif

    <script src="{{ asset("assets/scripts/courses/collapse_sections.js") }}"></script>

    @if ($is_editing) {{-- Add the scripts --}}
        <script>
            ajaxRoute = '{{ url(route('course.edit', ['id' => $course->id])) }}';
            courseId = '{{ $course->id }}';
        </script>
        <script src="{{ asset("assets/scripts/courses/admin/section_reorder.js") }}"></script>
        <script src="{{ asset('assets/scripts/courses/admin/add_section.js') }}"></script>
        <script src="{{ asset('assets/scripts/courses/admin/delete_section.js') }}"></script>
        <script src="{{ asset('assets/scripts/courses/admin/section_interior_reorder.js') }}"></script>
    @endif
</x-structure.wrapper>
