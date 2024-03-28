// Define constants to be used throughout the script
const collapseButtons = $(".section .collapse-button");
const collapseSections = $(collapseButtons).next();
// Create the sorter
const sectionSorter = $("#course-sections").sortable({
    revert: 0, placeholder: "course_section_placeholder", opacity: 0.5, cancel: false, axis: 'y'
});
// Disable the sorter by default
$(sectionSorter).sortable("disable");
// Handle button clicks
$("#reorder-sections-button").on("click", function () {
    if ( $(this).attr("data-active") === "false" ) {
        $("#reorder-sections-button span").text('Save new order').animate({ backgroundColor: '#B1CA65' }, 500);
        $("#reorder-sections-button").animate({ backgroundColor: '#88A236' }, 500);
        // Collapse all the sections
        $(collapseSections).animate({
            height: "0px", paddingTop: "0px", paddingBottom: "0px"
        }, 1000, function () {
            $(collapseSections).addClass("collapsing");
            $(collapseButtons).addClass("collapsed").animate({
                borderRadius: "8px"
            }, 1000);
            $(collapseButtons).css('cursor', 'move');
            // Enable the sorter
            $(sectionSorter).sortable("enable");
            $("#reorder-sections-button").attr("data-active", "true")
        });
    } else {
        // Disable the sorter
        $("#reorder-sections-button span").text('Re-order sections').animate({ backgroundColor: $(this).attr("fg-color") || $(this).attr("fg_color") || "#ffffff" }, 500);
        $("#reorder-sections-button").animate({ backgroundColor: $(this).attr("bg-color") || $(this).attr("bg_color") || "#ffffff" }, 500);
        $(sectionSorter).sortable("disable");
        // Un-collapse the sections
        $(collapseSections).each(function () {
            $(this).animate({
                height: $(this).prop("scrollHeight") + "px", paddingTop: "10px", paddingBottom: "10px"
            }, 1000);
        }).removeClass("collapsing");
        $(collapseButtons).removeClass("collapsed").css("borderRadius", "");
        $(collapseButtons).css('cursor', 'move');
        $("#reorder-sections-button").attr("data-active", "false");
        // AJAX request for setting the new order
        let order = [];
        $("div .section").each(function (index) {
            order.push([ index + 1, $(this).attr("id") ])
        });
        let orderJson = JSON.stringify(order);

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, method: "POST", url: ajaxRoute, data: {
                'course_id': courseId, 'edit_type': 'section_order', 'data': orderJson
            }
        });
    }
})
