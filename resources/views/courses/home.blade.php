<x-structure.wrapper title="{{ $course->title }}">


    {{-- Toggle owner admin view --}}
    @if($user_is_owner)
        <div class="flex-row" id="admin-row">
            <form action="{{ url()->current() }}" method="get">
                <x-components.3d_button class="course-button-mini max-content" name="editing" value="{{ $is_editing ? 'false' : 'true' }}" id="admin-button" fg-color="#9EC5AB" bg-color="#5e9c73">{{ $is_editing ? 'Disable admin mode' : 'Enable admin mode' }}</x-components.3d_button>
            </form>
            <form action="{{ route('course.settings.get', ['id' => $course->id]) }}" method="get">
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
                            <x-components.3d_button class="course-button-mini" id="edit-button" fg-color="#9EC5AB" bg-color="#5e9c73">Edit section details</x-components.3d_button>
                            <x-components.3d_button class="max-content course-button-mini" id="add-component-button" fg-color="#9EC5AB" bg-color="#5e9c73">Add new component</x-components.3d_button>
                            @if ($course_section->items->count() > 1)
                                <x-components.3d_button class="course-button-mini" id="reorder-section-button" fg-color="#9EC5AB" bg-color="#5e9c73" data-active="false">Re-order components</x-components.3d_button>
                            @endif
                        </div>
                        {{-- Display the form to edit the form details --}}
                        <div class="flex-col section-edit-component">
                            <x-courses.edit_component :course="$course" :section="$course_section" />
                        </div>
                        {{-- Display the form to add new componenets --}}
                        <div class="flex-col section-add-component">
                            <x-courses.add_component course_id="{{ $course->id }}" section_id="{{ $course_section->id }}" />
                        </div>
                    @endif


                    {{-- Add the description for the description (if available) --}}
                    @if($course_section->description)
                        <p>{{ $course_section->description }}</p>
                    @endif


                    {{-- Display all the items in the section --}}
                    @if($is_editing) <div class="section-content"> @endif
                        @foreach($course_section->items as $section_item)
                            {{-- Add the item accordingly with a switch-case --}}
                            @switch($section_item->item_type)


                                @case("LESSON") {{-- Add the content for lessons --}}
                                    @if ($section_item->lessonExists) {{-- Change what is displayed depending on whether the lesson exists --}}
                                        <div class="section-item lesson flex-col" id="{{ $section_item->id }}">
                                            @if ($is_editing) <x-courses.component_settings :num-sections="$course->sections->count()" :current-pos="$course_section->position" :max-pos="$course->sections->max('position')" :min-pos="$course->sections->min('position')" /> @endif
                                            <h5>{{ $section_item->title }}</h5>
                                            @if($section_item->description != null)
                                                <p>{{ $section_item->description }}</p>
                                            @endif
                                            <x-components.3d_button fg-color="#9EC5AB" bg-color="#5e9c73" onclick="location.href = '{{ route('course.lesson.start', [ 'id' => $course->id, 'lessonId' => $section_item->item_value['lesson_id']] ) }}'">BEGIN LESSON</x-components.3d_button>
                                        </div>
                                    @else {{-- Display the soft error message if the lesson can't be found. --}}
                                        <div class="section-item lesson flex-col" id="{{ $section_item->id }}">
                                            @if ($is_editing) <x-courses.component_settings :num-sections="$course->sections->count()" :current-pos="$course_section->position" :max-pos="$course->sections->max('position')" :min-pos="$course->sections->min('position')" /> @endif
                                            <h5>Lesson Unavailable</h5>
                                            <p>The lesson cannot be displayed as the content cannot be found.</p>
                                        </div>
                                    @endif
                                    @break


                                @case("TEXT") {{-- Display the content for text items --}}
                                    <div class="section-item text" id="{{ $section_item->id }}">
                                        @if ($is_editing) <x-courses.component_settings :num-sections="$course->sections->count()" :current-pos="$course_section->position" :max-pos="$course->sections->max('position')" :min-pos="$course->sections->min('position')" /> @endif
                                        <p style="margin-top: 2px;">{{ $section_item->title }}</p>
                                    </div>
                                    @break


                                @case("FILE") {{-- Display the content for downloadable files. --}}
                                    <div class="section-item file" id="{{ $section_item->id }}">
                                        @if ($is_editing) <x-courses.component_settings :num-sections="$course->sections->count()" :current-pos="$course_section->position" :max-pos="$course->sections->max('position')" :min-pos="$course->sections->min('position')" /> @endif
                                        <div class="flex-row">
                                            <p style="margin-right: 10px;">{{ $section_item->title }}</p>
                                            <x-components.3d_button class="download-button course-button-mini max-content" fg-color="#9EC5AB" bg-color="#5e9c73" onclick="window.location.href = '{{ route('course.file.download', ['id' => $course->id, 'fileId' => $section_item->item_value['fileId']]) }}'">Download</x-components.3d_button></div>
                                        </div>
                                    @break


                                @case("IMAGE") {{-- Display the content for images. --}}
                                    <div class="section-item image" id="{{ $section_item->id }}">
                                        @if ($is_editing) <x-courses.component_settings :num-sections="$course->sections->count()" :current-pos="$course_section->position" :max-pos="$course->sections->max('position')" :min-pos="$course->sections->min('position')" /> @endif
                                        <img style="width: 100%;" src="{{ route('course.file.serve', ['id' => $course->id, 'fileId' => $section_item->item_value['fileId']]) }}" alt="{{ $section_item->item_value['alt'] == 0 ? "No alt text provided" : $section_item->item_value['alt'] }}">
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


    {{-- Manage client-side JavaScript files --}}
    <script src="{{ asset("assets/scripts/courses/collapse_sections.js") }}"></script>
    @if ($is_editing) {{-- These scripts are used only for when editing course content --}}
        <script>
            courseId = '{{ $course->id }}';
            ajaxRoute = '{{ url(route('course.edit', ['id' => $course->id])) }}';
            formRoute = '{{ url(route('course.getForm', ['id' => $course->id])) }}';
        </script>
        <script src="{{ asset("assets/scripts/courses/admin/edit_section.js") }}"></script>
        <script src="{{ asset('assets/scripts/courses/admin/item_admin.js') }}"></script>
        <script src="{{ asset("assets/scripts/courses/admin/section_reorder.js") }}"></script>
        <script src="{{ asset('assets/scripts/courses/admin/add_section.js') }}"></script>
        <script src="{{ asset('assets/scripts/courses/admin/delete_section.js') }}"></script>
        <script src="{{ asset('assets/scripts/courses/admin/section_interior_reorder.js') }}"></script>
        <script src="{{ asset('assets/scripts/courses/admin/add_section_item.js') }}"></script>
    @endif
</x-structure.wrapper>
