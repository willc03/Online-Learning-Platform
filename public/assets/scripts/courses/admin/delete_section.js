$(".three-d.course-button-mini#delete-button").on("click", function() {
    const button = $(this);
    const foreground = $(button).children('span')[0];

    if ($(button).data("shouldDelete") === true) {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            url: ajaxRoute,
            data: {
                'course_id': courseId,
                'edit_type': 'delete_section',
                'data': JSON.stringify({'section_id': $(button).parent().parent().attr('id')})
            },
            success: function(data) {
                if (data === 'SUCCESS') {
                    let section = $(button).parent().parent();
                    let section_button = $(section).prev();
                    let section_container = $(section).parent()

                    $(section_container).css('background', 'none');
                    $(section).animate({height: 0, padding: 0}, 500, function() {
                        $(section_button).css('overflow', 'hidden').animate({height: 0, padding: 0}, 500, function() {
                            $(section_container).animate({padding: 0, margin: 0}, 500, function() {
                                $(section_container).remove();
                            });
                        })
                    })
                }
            }
        });
    } else {
        $(button).animate({backgroundColor: "#88A236"}, 500);
        $(foreground).animate({backgroundColor: "#B1CA65"}, 500).text("Confirm");
        $(button).data("shouldDelete", true);

        setTimeout(function() {
            $(button).data("shouldDelete", false);
            $(button).animate({backgroundColor: $(button).attr("bg-color") || $(button).attr("bg_color") || "#ffffff"}, 500);
            $(foreground).animate({backgroundColor: $(button).attr("fg-color") || $(button).attr("fg_color") || "#ffffff"}, 500).text("Delete section");
        }, 7500);
    }
});
