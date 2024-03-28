// Form requests
$(".section select[name='type']").change(function () {
    // Get the necessary data for the section
    let section = $(this).closest('.section');
    let selectedFormType = $(this).val();
    let formContainer = $(section).find('#form_container');
    let submissionContainer = $(section).find('#submission');
    // Empty the form container
    $(formContainer).empty();
    // Make the AJAX request to re-populate the form
    $.ajax({
        url: formRoute, data: {
            form_type: selectedFormType, course_id: courseId, section_id: $(section).attr('id')
        }, success: function (data) {
            $(formContainer).html(data).css('display', '');
            $(submissionContainer).css('display', '');
        }
    });
});

// Default container setup
$('.section .section-add-component').each(function () {
    // Define necessary components
    let additionContainer = $(this);
    // Default behaviour
    $(additionContainer)
        .data('size', $(additionContainer).height())
        .data('is_open', false)
        .css({
            height: 0, borderWidth: 0, paddingTop: 0, paddingBottom: 0
        });
});

// Addition button click
$('.section .section-admin-panel #add-component-button').each(function () {
    // Define necessary components
    let button = $(this);
    let section = $(this).closest('.section');
    let additionContainer = $(section).find('.section-add-component');
    let sectionEditContainer = $(section).find('.section-edit-component');
    // Button click behaviour
    $(button).on('click', function () {
        if ( $(additionContainer).data('is_open') === true ) {
            return;
        }
        $(sectionEditContainer) // Close the edit container
            .data('is_open', false)
            .animate({
                height: 0, paddingTop: 0, paddingBottom: 0
            });
        $(additionContainer)
            .data('is_open', true)
            .animate({
                height: $(additionContainer).data('size') + 'px', borderWidth: '1.5px', paddingTop: '10px', paddingBottom: '10px'
            }, 500, function () {
                $(additionContainer).css('height', '');
            });
    });
});

// Addition cancellation
$('.section #submission #cancel').each(function () {
    // Define necessary components
    let button = $(this);
    let section = $(this).closest('.section');
    let additionContainer = $(section).find('.section-add-component');
    let formContainer = $(section).find('#form_container');
    let submissionContainer = $(section).find('#submission');
    // Button click behaviour
    $(button).on('click', function () {
        if ( $(additionContainer).data('is_open') === false ) {
            return;
        }
        $(additionContainer)
            .data('is_open', false)
            .animate({
                height: 0, paddingTop: 0, paddingBottom: 0, borderWidth: 0
            }, 500, function () {
                $(formContainer).empty().css('display', 'none');
                $(submissionContainer).css('display', 'none');
            });
    });
});

// Addition submissions
$('.section #submission #submit').on('click', function () {
    // Define necessary components
    let section = $(this).closest('.section');
    let additionForm = $(section).find('#add-component-form');
    // Submission behaviour
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: ajaxRoute, method: "POST", data: {
            edit_type: 'section_item_add', course_id: courseId, data: JSON.stringify($(additionForm).serializeArray()), success: function () {
                setTimeout(function () {
                    location.reload();
                }, 100);
            }
        }
    })
})
