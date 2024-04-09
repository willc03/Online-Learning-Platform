<x-structure.wrapper title="{{ $course->title }}">


    {{-- Toggle owner admin view --}}
    @if($user_is_owner)
        <div class="flex-row" id="admin-row">
            <form action="{{ url()->current() }}" method="get">
                <x-ui.interactive-button class="course-button-mini max-content" name="editing" value="{{ $is_editing ? 'false' : 'true' }}" id="admin-button" fg-color="#43AA8B" bg-color="#245B4A">{{ $is_editing ? 'Disable admin mode' : 'Enable admin mode' }}</x-ui.interactive-button>
            </form>
            <form action="{{ route('course.settings.get', ['id' => $course->id]) }}" method="get">
                <x-ui.interactive-button class="course-button-mini max-content" id="settings-button" fg-color="#43AA8B" bg-color="#245B4A">Course settings</x-ui.interactive-button>
            </form>
        </div>
    @endif

    {{-- Let non-owner users leave the course --}}
    @unless($user_is_owner)
        <form method="post" action="{{ route('course.leave', [ 'id' => $course->id ]) }}">
            @csrf
            @method('DELETE')
            <x-ui.interactive-button class="course-button-mini max-content" fg_color="#D10023" bg_color="#840016">Leave course</x-ui.interactive-button>
        </form>
    @endunless

    {{-- Course details --}}
    <h1>{{ $course->title }}</h1>
    <p id="course-owner" class="mini-text">By <span class="italicise">{{ $owner->name }}</span></p>
    @if ($course->description !== null)
        <p id="course-description">{{ $course->description }}</p>
    @endif


    {{-- Display all the course content in a downwards fashion --}}
    <h2>Course content</h2>
    @if ($is_editing && $course->sections->count() > 1)
        <x-ui.interactive-button class="course-button-mini" id="reorder-sections-button" fg-color="#43AA8B" bg-color="#245B4A" data-active="false">Re-order sections</x-ui.interactive-button>
    @endif

    {{-- Display lesson completion success message if in the session --}}
    @if (session()->get('COMPLETED_LESSON', null))
        <x-message.success title="Congratulations!" description="You completed the lesson '{{ session()->get('LESSON_TITLE', 'N/A') }}' with a score of {{ session()->get('COMPLETED_LESSON', 'N/A') }}" />
    @endif

    {{-- Display the course sections --}}
    <div id="course-sections" class="flex-col">

        @if($course->sections->count() == 0)
            <p>There is no content in this course! Check back soon, the course owner may add something here!</p>
        @endif

        @foreach($course->sections as $course_section)
            <div class="section" id="{{ $course_section->id }}">
                <button class="collapse-button" id="{{ $course_section->id }}">{{ $course_section->title }}</button>
                <div class="collapse-section" id="{{ $course_section->id }}">


                    {{-- Add deletion buttons if the user is a course admin and is editing --}}
                    @if ($user_is_owner && $is_editing)
                        <div class="section-admin-panel">
                            <h4>Admin Controls</h4>
                            <x-ui.interactive-button class="course-button-mini" id="delete-button" fg_color="#CA6565" bg_color="#A23636">Delete section</x-ui.interactive-button>
                            <x-ui.interactive-button class="course-button-mini" id="edit-button" fg-color="#43AA8B" bg-color="#245B4A">Edit section details</x-ui.interactive-button>
                            <x-ui.interactive-button class="max-content course-button-mini" id="add-component-button" fg-color="#43AA8B" bg-color="#245B4A">Add new component</x-ui.interactive-button>
                            @if ($course_section->items->count() > 1)
                                <x-ui.interactive-button class="course-button-mini" id="reorder-section-button" fg-color="#43AA8B" bg-color="#245B4A" data-active="false">Re-order components</x-ui.interactive-button>
                            @endif
                        </div>
                        {{-- Display the form to edit the form details --}}
                        <div class="flex-col section-edit-component">
                            <form id="edit-section-form" method="post" action="{{ route('course.edit', ['id' => $course->id]) }}" class="flex-col">
                                @csrf
                                <legend>Edit section details</legend>
                                <input type="hidden" name="edit_type" value="section_edit" />
                                <input type="hidden" name="course_id" value="{{ $course->id }}" />
                                <input type="hidden" name="section_id" value="{{ $course_section->id }}" />
                                <label class="form-flex">
                                    <span>Section Title:</span>
                                    <input name="title" type="text" value="{{ $course_section->title }}" required />
                                </label>
                                <label class="form-flex">
                                    <span>Section Description:</span>
                                    <textarea name="description" style="resize: none">{{ $course_section->description ?? "" }}</textarea>
                                </label>
                                <x-ui.interactive-button id="section-details-submit" class="course-button-mini max-content" fg-color="#43AA8B" bg-color="#245B4A" disabled>Set new details</x-ui.interactive-button>
                            </form>

                        </div>
                        {{-- Display the form to add new componenets --}}
                        <div class="flex-col section-add-component">
                            <label>
                                Which type of component would you like to add?
                                <select name="type" class="type">
                                    <option value="" disabled selected>Please select a component</option>
                                    <option value="text">Text</option>
                                    <option value="lesson">Lesson</option>
                                    <option value="image">Image</option>
                                    <option value="file">File</option>
                                </select>
                            </label>

                            <div id="form_container" style="display: none"></div>

                            <div id="submission" class="flex-row" style="display: none">
                                <x-ui.interactive-button id="cancel" class="max-content course-button-mini" fg_color="#CA6565" bg_color="#A23636">Cancel</x-ui.interactive-button>
                                <x-ui.interactive-button id="submit" class="max-content course-button-mini" fg_color="#B1CA65" bg_color="#88A236">Submit</x-ui.interactive-button>
                            </div>

                        </div>
                    @endif


                    {{-- Add the description for the description (if available) --}}
                    @if($course_section->description)
                        <p>{{ $course_section->description }}</p>
                    @endif


                    {{-- Display all the items in the section --}}
                    @if($is_editing)
                        <div class="section-content"> @endif
                            @foreach($course_section->items as $section_item)
                                {{-- Add the item accordingly with a switch-case --}}
                                @switch($section_item->item_type)

                                    @case("LESSON") {{-- Add the content for lessons --}}
                                    @if ($section_item->lessonExists)
                                        {{-- Change what is displayed depending on whether the lesson exists --}}
                                        <div class="section-item lesson flex-col" id="{{ $section_item->id }}">
                                            @if ($is_editing)
                                                <x-course.component-settings :num-sections="$course->sections->count()" :current-pos="$course_section->position" :max-pos="$course->sections->max('position')" :min-pos="$course->sections->min('position')" />
                                            @endif
                                            <h5>{{ $section_item->title }}</h5>
                                            @if($section_item->description != null)
                                                <p>{{ $section_item->description }}</p>
                                            @endif
                                            @php
                                                $highScore = 0;
                                                foreach ($lesson_scores as $lesson_score) {
                                                    if ($lesson_score['lessonId'] == $section_item->item_value['lesson_id']) {
                                                        if ($lesson_score['score'] > $highScore) {
															$highScore = $lesson_score['score'];
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <div class="flex-row middle">
                                                <x-ui.interactive-button class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A" onclick="location.href = '{{ route('course.lesson.start', [ 'id' => $course->id, 'lessonId' => $section_item->item_value['lesson_id']] ) }}'">BEGIN LESSON</x-ui.interactive-button>
                                                @if($user_is_owner)
                                                    <x-ui.interactive-button class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A" onclick="location.href = '{{ route('course.lesson.attempts', [ 'id' => $course->id, 'lessonId' => $section_item->item_value['lesson_id']] ) }}'">VIEW ATTEMPTS</x-ui.interactive-button>
                                                    @if ($is_editing)
                                                        <x-ui.interactive-button class="course-button-mini" fg-color="#43AA8B" bg-color="#245B4A" onclick="location.href = '{{ route('course.lesson.configure.get', [ 'id' => $course->id, 'lessonId' => $section_item->item_value['lesson_id']] ) }}'">CONFIGURE LESSON</x-ui.interactive-button>
                                                    @endif
                                                @endif
                                            </div>
                                            @if( $highScore != 0 )
                                                <div class="high-score flex-row">
                                                    <span class="text">HIGH SCORE:   </span>
                                                    <span class="number">{{ $highScore }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        {{-- Display the soft error message if the lesson can't be found. --}}
                                        <div class="section-item lesson flex-col" id="{{ $section_item->id }}">
                                            @if ($is_editing)
                                                <x-course.component-settings :num-sections="$course->sections->count()" :current-pos="$course_section->position" :max-pos="$course->sections->max('position')" :min-pos="$course->sections->min('position')" />
                                            @endif
                                            <h5>Lesson Unavailable</h5>
                                            <p>The lesson cannot be displayed as the content cannot be found.</p>
                                        </div>
                                    @endif
                                    @break


                                    @case("TEXT") {{-- Display the content for text items --}}
                                    <div class="section-item text" id="{{ $section_item->id }}">
                                        @if ($is_editing)
                                            <x-course.component-settings :num-sections="$course->sections->count()" :current-pos="$course_section->position" :max-pos="$course->sections->max('position')" :min-pos="$course->sections->min('position')" />
                                        @endif
                                        <p style="margin-top: 2px; word-wrap: break-word">{!! str_replace("\r\n", "<br>", $section_item->item_value['text']) !!}</p>
                                    </div>
                                    @break


                                    @case("FILE") {{-- Display the content for downloadable files. --}}
                                    <div class="section-item file" id="{{ $section_item->id }}">
                                        @if ($is_editing)
                                            <x-course.component-settings :num-sections="$course->sections->count()" :current-pos="$course_section->position" :max-pos="$course->sections->max('position')" :min-pos="$course->sections->min('position')" />
                                        @endif
                                        <div class="flex-row">
                                            <p style="margin-right: 10px;">{{ $section_item->title }}</p>
                                            <x-ui.interactive-button class="download-button course-button-mini max-content" fg-color="#43AA8B" bg-color="#245B4A" onclick="window.location.href = '{{ route('course.file.download', ['id' => $course->id, 'fileId' => $section_item->item_value['fileId']]) }}'">Download</x-ui.interactive-button>
                                        </div>
                                    </div>
                                    @break


                                    @case("IMAGE") {{-- Display the content for images. --}}
                                    <div class="section-item image" id="{{ $section_item->id }}">
                                        @if ($is_editing)
                                            <x-course.component-settings :num-sections="$course->sections->count()" :current-pos="$course_section->position" :max-pos="$course->sections->max('position')" :min-pos="$course->sections->min('position')" />
                                        @endif
                                        <img style="width: 100%;" src="{{ route('course.file.serve', ['id' => $course->id, 'fileId' => $section_item->item_value['fileId']]) }}" alt="{{ $section_item->item_value['alt'] == 0 ? "No alt text provided" : $section_item->item_value['alt'] }}">
                                    </div>
                                    @break

                                @endswitch
                            @endforeach
                            @if($is_editing) </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>


    {{-- If the user is an admin, allow them to add additional sections --}}
    @if($user_is_owner && $is_editing)
        <br>
        <x-ui.interactive-button class="course-button-mini" id="add-section-button" fg-color="#43AA8B" bg-color="#245B4A" onclick="$(addSectionForm).animate({height: $(addSectionForm).data('size')}, 500, function() { $(addSectionForm).height('') })">Add new section</x-ui.interactive-button>
        <form id="course-section-add-form" class="middle">
            <legend>New Section</legend>

            <label for="title" class="form-flex">
                <span class="required">Section title:</span>
                <input type="text" name="title" required />
            </label>

            <label for="description" class="form-flex">
                <span>Section description:</span>
                <textarea name="description" id="new-section-description"></textarea>
            </label>

            <div class="submit-buttons" style="margin-bottom: 15px">
                <x-ui.interactive-button value="false" fg_color="#CA6565" bg_color="#A23636" onclick="$(addSectionForm).animate({height: '0px'}, 500)">Cancel</x-ui.interactive-button>
                <x-ui.interactive-button value="true" fg_color="#B1CA65" bg_color="#88A236">Add section</x-ui.interactive-button>
            </div>
        </form>

    @endif


    {{-- Manage client-side JavaScript files --}}
    <script src="{{ asset("assets/scripts/courses/collapse_sections.js") }}"></script>
    <script src="{{ asset("assets/scripts/courses/high_scores.js") }}"></script>
    @if ($is_editing)
        {{-- These scripts are used only for when editing course content --}}
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
