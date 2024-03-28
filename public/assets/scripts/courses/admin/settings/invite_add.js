// Initial setup
$("#new-invite-form")
    .data("height", $("#new-invite-form").height())
    .css({
        height: 0, overflow: 'hidden'
    });
$("#new-invite").on('click', function () {
    $("#new-invite-form").animate({
        height: $("#new-invite-form").data("height")
    }, 500);
});

// Get the button
let button = $("#add-invite-btn");
// Get the components
let activeOnCreation = $("#new-invite-form input[name='active']");
let unlimitedUses = $("#new-invite-form input[name='unlimitedUses']");
let allowedUses = $("#new-invite-form input[name='allowedUses']");
let neverExpire = $("#new-invite-form input[name='neverExpire']");
let expiryDate = $("#new-invite-form input[name='expiryDate']");
// Get on component input
$("#new-invite-form input").on('input', function () {
    if ( $(activeOnCreation).val() && ($(unlimitedUses).val() || $(allowedUses).val()) && ($(neverExpire).val() || $(expiryDate).val()) ) {
        $(button).prop('disabled', false);
    } else {
        $(button).prop('disabled', true);
    }
});
