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
                if (data[0] === 'SUCCESS') {
                    window.location.href = ((location.protocol + '//' + location.host + location.pathname) + '#' + data[1]);
                }
            }
        });
    });
