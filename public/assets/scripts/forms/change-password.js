let form = $("form#change-password");
$(form)
    .data('height', $(form).height())
    .css({
        height: 0,
        overflow: 'hidden'
    });
$("#password-change-button").on('click', function() {
    if ($(form).data('open')) { return; }
    $(form).data('open', true);
    $(form).animate({ height: $(form).data('height') + 10 }, 750, function() {
        $(form).css('height', '');
    });
});
