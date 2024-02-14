// Default container setup
$('.section .section-edit-component').each(function () {
    // Define necessary components
    let editContainer = $(this);
    // Default behaviour
    $(editContainer)
        .data('size', $(editContainer).height())
        .data('is_open', false)
        .css({
            height: 0,
            borderWidth: 0,
            paddingTop: 0,
            paddingBottom: 0
        });
    // Set up for form submission
    {
        // Get items
        let items = $(editContainer).find("input:not([type='hidden']), textarea, select");
        console.log(items);
        // Set initial values for button enable/disable
        $(items).each(function () {
            $(this).data("initial-value", $(this).val());
        });
        // Detect changes and enable/disable button
        $(items).on('input', function () {
            let enableButton = false;
            $(items).each(function () {
                if ( !($(this).val() === $(this).data("initial-value")) ) {
                    enableButton = true;
                }
            });
            $("#section-details-submit").prop("disabled", !enableButton);
        });
    }
});

// Edit button click
$('.section .section-admin-panel #edit-button').each(function () {
    // Define necessary components
    let button = $(this);
    let section = $(this).closest('.section');
    let additionContainer = $(section).find('.section-add-component');
    let sectionEditContainer = $(section).find('.section-edit-component');
    // Button click behaviour
    $(button).on('click', function () {
        if ($(sectionEditContainer).data('is_open') === true) {
            return;
        }
        $(sectionEditContainer) // Close the addition container
            .data('is_open', true)
            .animate({
                height: $(sectionEditContainer).data('size') + 'px',
                paddingTop: '10px',
                paddingBottom: '10px'
            });
        $(additionContainer)
            .data('is_open', false)
            .animate({
                height: 0,
                paddingTop: 0,
                paddingBottom: 0,
                borderWidth: 0
            }, 500);
    });
});
