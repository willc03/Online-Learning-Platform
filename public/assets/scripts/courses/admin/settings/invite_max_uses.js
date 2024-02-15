let maxUseToggleButtons = $('#max-use-toggle');
let toggleSections = $('#max-use-form');
// Initial settings
$(toggleSections).each(function() {
    $(this)
        .data('height', $(this).height())
        .css('height', 0);
})
// Click behaviour
$(maxUseToggleButtons).on('click', function() {
    let button = $(this);
    let box = $(button).closest('.table-col');
    let form = $(box).find("#max-use-form");
    //
    $(button).animate({ height: 0 }, 500, function() { $(this).css('overflow', 'hidden'); });
    $(form).animate({ height: $(form).data('height') }, 500);
});
// Form editing
$(toggleSections).each(function() {
    let input = $(this).find("input[type='number']");
    let button = $(this).find("#submit-invite-max-use");
    let row = $(this).closest(".table-row");
    let inviteId = $(row).attr('id');
    console.log($(input).val())
    $(input).on("input", function() {
        $(button).prop("disabled", $(this).val() === $(this).attr("data-initial"))
    });
    $(button).on("click", function() {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            url: inviteModifyRoute,
            data: {
                inviteId: inviteId,
                modificationType: 'maxUses',
                newMax: $(input).val()
            },
            success: function() {
                location.reload();
            }
        });
    })
});
