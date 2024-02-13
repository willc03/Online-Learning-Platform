// Get items
let items = $("#course-details input, #course-details textarea, #course-details select");
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
    $("#details-submit").prop("disabled", !enableButton);
});
