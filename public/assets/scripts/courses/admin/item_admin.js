$(".item-settings").closest(".section-item").on('mouseenter', function () {
    $(this).find(".item-settings").css('opacity', 100);
}).on('mouseleave', function () {
    $(this).find(".item-settings").css('opacity', 0);
});
$(".trash-button").on('click', function () {
    // Define components
    let button = $(this);
    let foreground = $(button).find("span");
    let item = $(button).closest(".section-item");
    // Define behaviour
    if ( $(button).data('is_active') === true ) {
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, method: "POST", url: ajaxRoute, data: {
                'course_id': courseId, 'edit_type': 'section_item_delete', 'data': JSON.stringify({ 'item_id': $(item).attr('id') })
            }, success: function (data) {
                $(item).css("overflow", "hidden").animate({ height: 0, padding: 0 }, 500, function () {
                    $(item).remove();
                });
            }
        });
    } else {
        $(button).data('is_active', true);
        $(button).animate({ backgroundColor: "#88A236" }, 500);
        $(foreground).animate({ backgroundColor: "#B1CA65" }, 500).text("Confirm");

        setTimeout(function () {
            $(button).data("is_active", false);
            $(button).animate({ backgroundColor: $(button).attr("bg-color") || $(button).attr("bg_color") || "#ffffff" }, 500);
            $(foreground).animate({ backgroundColor: $(button).attr("fg-color") || $(button).attr("fg_color") || "#ffffff" }, 500).html('<img width="20px" height="20px" src="{{ asset("assets/images/trash-can.svg") }}">');
        }, 7500);
    }
});

$(".down-button, .up-button").on('click', function () {
    // Define components
    let button = $(this);
    let foreground = $(button).find("span");
    let item = $(button).closest(".section-item");
    // Define behaviour
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, method: "POST", url: ajaxRoute, data: {
            'course_id': courseId, 'edit_type': 'section_item_move', 'data': JSON.stringify({ item_id: $(item).attr('id'), direction: $(button).hasClass("down-button") ? "down" : "up" })
        }, success: function () {
            location.reload();
        }
    });
});
