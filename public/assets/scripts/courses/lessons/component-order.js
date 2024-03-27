const reorderButton = $("#re-order");
const lessonItems = $(".lesson-items");
const lessonComponents = $(".lesson-config");
// Set defaults for lesson component sortable
$(lessonItems).sortable({
    cursor: "move", disabled: true, start: function (_, ui) {
        let item = $(ui.item);
        $(item).find("button").css('opacity', '0')
    }, stop: function (_, ui) {
        resetBFButtons();
        let item = $(ui.item);
        $(item).find("button").css('opacity', '')
    }, change: function () {
        resetBFButtons();
    }
});
// Set defaults for lesson components
$(lessonComponents).each(function () {
    let container = $(this).find(".container");
    $(container)
        .data({ height: $(container).height(), paddingTop: $(container).css('paddingTop'), paddingBottom: $(container).css('paddingBottom') })
        .css('overflow', 'hidden')
});
// Button click behaviour
$(reorderButton)
    .data('active', false)
    .on('click', function () {
        if ( $(this).data('active') ) {
            $(this).animate({ backgroundColor: "#245B4A" }, 500);
            $(this).find('.foreground').animate({ backgroundColor: "#43AA8B" }, 500).text("Re-order components");
            // Disable the sorter
            $(lessonItems).sortable("option", "disabled", true);
            $(lessonComponents).each(function () {
                let container = $(this).find(".container");
                // Make the fill blank buttons invisible
                if ( $(this).hasClass("fill-blanks") ) {
                    $(this).find("button").css('opacity', '0');
                }
                // Re-open the section
                $(container).animate({ height: $(container).data("height"), paddingTop: $(container).data("paddingTop"), paddingBottom: $(container).data("paddingBottom") }, 500, function () {
                    // Make the height variable once more
                    $(container).css('height', '');
                    resetBFButtons();
                    // Make all buttons visible
                    $(lessonItems).find("button").css('opacity', '100');
                });
            });
            // Submit the AJAX request containing the new order
            // First, get the new order
            setTimeout(function () {
                let data = [];
                let index = 1;
                $(".lesson-config").each(function () {
                    data.push({ id: $(this).attr('id'), position: index++ });
                })
                $.ajax({
                    url: lessonEditRoute, method: 'POST', headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }, data: {
                        'edit-type': 'order', 'data': data
                    }, success: function () {
                        console.log("Successful edit.")
                    }
                });
            }, 100);
        } else {
            $(this).animate({ backgroundColor: "#88A236" }, 500);
            $(this).find('.foreground').animate({ backgroundColor: "#B1CA65" }, 500).text("Save new order");
            // Enable the sorter and close the sections
            $(lessonComponents).each(function () {
                let container = $(this).find(".container");
                if ( $(this).hasClass("fill-blanks") ) {
                    $(this).find("button").css('opacity', '0');
                }
                $(container).animate({ height: 0, paddingTop: 0, paddingBottom: 0 }, 500, function () {
                    resetBFButtons();
                    $(lessonItems).find("button").css('opacity', '');
                });
            });
            $(lessonItems).sortable("option", "disabled", false);
        }
        // Flip the active switch
        $(this).data('active', !$(this).data('active'));
    })
