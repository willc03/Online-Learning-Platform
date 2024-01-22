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
        <script>
            // Define constants to be used throughout the script
            const collapseButtons = $(".section .collapse-button");
            const collapseSections = $(collapseButtons).next();
            // Create the sorter
            const sectionSorter = $("#course-sections").sortable({
                revert: true,
                placeholder: "course_section_placeholder",
                opacity: 0.5,
                cancel: false,
            });
            // Disable the sorter by default
            $(sectionSorter).sortable("disable");
            // Handle button clicks
            $("#reorder-sections-button").on("click", function() {
                if ($(this).attr("data-active") === "false") {
                    $("#reorder-sections-button span").text('Save new order').animate({backgroundColor: '#B1CA65'}, 500);
                    $("#reorder-sections-button").animate({backgroundColor: '#88A236'}, 500);
                    // Collapse all the sections
                    $(collapseSections).animate({
                        height: "0px",
                        paddingTop: "0px",
                        paddingBottom: "0px"
                    }, 1000, function() {
                        $(collapseSections).addClass("collapsing");
                        $(collapseButtons).addClass("collapsed").animate({
                            borderRadius: "8px"
                        }, 1000);
                        // Enable the sorter
                        $(sectionSorter).sortable("enable");
                        $("#reorder-sections-button").attr("data-active", "true")
                    });
                } else {
                    // Disable the sorter
                    $("#reorder-sections-button span").text('Re-order sections').animate({backgroundColor: '#a491d3'}, 500);
                    $("#reorder-sections-button").animate({backgroundColor: '#7a5fbf'}, 500);
                    $(sectionSorter).sortable("disable");
                    // Un-collapse the sections
                    $(collapseSections).each(function() {
                        $(this).animate({
                            height: $(this).prop("scrollHeight") + "px",
                            paddingTop: "10px",
                            paddingBottom: "10px"
                        }, 1000);
                    }).removeClass("collapsing");
                    $(collapseButtons).removeClass("collapsed").css("borderRadius", "");
                    $("#reorder-sections-button").attr("data-active", "false");
                    // AJAX request for setting the new order
                    let order = [];
                    $("div .section.draggable-choice").each(function(index) {
                        order.push([index + 1, $(this).attr("id")])
                    });
                    let orderJson = JSON.stringify(order);

                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        method: "POST",
                        url: ajaxRoute,
                        data: {
                            'course_id': courseId,
                            'edit_type': 'section_order',
                            'data': orderJson
                        }
                    });
                }
            })
        </script>
    @endif
</x-structure.wrapper>
