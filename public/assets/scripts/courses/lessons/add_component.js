const formButton = $("#add-btn");
const formBox = $("#new-lesson-item");
const subFormBox = $(".detail-container");
$(formButton).on('click', function() {
    if ($(formBox).data('open')) { return; }
    $(formBox)
        .animate({ height: $(formBox).data('height') }, 500, function() { $(formBox).css('height', ''); })
        .data('open', true)
});
$(formBox)
    .data({
        height: $(formBox).height(),
        open: false
    })
    .css({ height: 0, overflow: 'hidden' });
$("#select-item-type").on('change', function () {
    $(subFormBox).empty();
    $.ajax({
        url: formRequestRoute,
        data: {
            'form-name': $("#select-item-type").val()
        },
        success: function(response) {
            $(subFormBox).html(response);
        }
    });
});
