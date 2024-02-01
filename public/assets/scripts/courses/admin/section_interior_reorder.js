$("#reorder-section-button").on("click", function () {
    const section = $(this).parents("div.collapse-section").children(".section-content");
    const section_id = $(this).parents("div.collapse-section").attr('id');

    // Create the sorter
    const sectionSorter = $(section).sortable({
        revert: 0,
        placeholder: "course_section_placeholder",
        opacity: 0.5,
        cancel: false,
        axis: 'y',
        tolerance: 'pointer',
        appendTo: $(section)
    });

    if ($(this).attr("data-active") === "false") {
        $($(this).children()[0])
            .text('Save new order')
            .animate({ backgroundColor: '#B1CA65' }, 500);
        $(this).animate({ backgroundColor: '#88A236' }, 500);
        $(sectionSorter).sortable("enable");
        $(section).children().each(function(_, element) {
            $(element).css('cursor', 'move');
        });
        $(this).attr("data-active", "true");
    } else {
        // Disable the sorter
        $($(this).children()[0])
            .text('Re-order components')
            .animate({ backgroundColor: $(this).attr("fg-color") || $(this).attr("fg_color") || "#ffffff" }, 500);
        $(this).animate({ backgroundColor: $(this).attr("bg-color") || $(this).attr("bg_color") || "#ffffff" }, 500);
        $(sectionSorter).sortable("disable");
        $(section).children().each(function(_, element) {
            $(element).css('cursor', '');
        });
        $(this).attr("data-active", "false");

        // AJAX request for setting the new order
        let order = [];
        $(section).children().each(function (index) {
            order.push([index + 1, $(this).attr("id")]);
        });

        let orderJson = JSON.stringify(order);

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            method: "POST",
            url: ajaxRoute,
            data: {
                'course_id': courseId,
                'edit_type': 'section_interior_order',
                'section_id': section_id,
                'data': orderJson
            }
        });
    }
});
