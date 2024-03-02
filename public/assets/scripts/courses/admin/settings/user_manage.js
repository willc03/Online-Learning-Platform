$("#user-manager #user-delete").on("click", function() {
    const button = $(this);
    const foreground = $(button).children('span')[0];
    let row = $(this).closest('.table-row');

    if ($(button).data("shouldDelete") === true) {
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "DELETE",
            url: userRemoveRoute,
            data: {
                userId: $(row).attr('id')
            },
            success: function() {
                // Hide the row
                $(row).css({
                    height: $(row).height(),
                    overflow: 'hidden'
                }).animate({
                    height: 0
                }, 500, function() {
                    $(row).remove();
                });
            }
        });
    } else {
        $(button).animate({backgroundColor: "#88A236"}, 500);
        $(foreground).animate({backgroundColor: "#B1CA65"}, 500).text("Confirm removal");
        $(button).data("shouldDelete", true);

        setTimeout(function() {
            $(button).data("shouldDelete", false);
            $(button).animate({backgroundColor: $(button).attr("bg-color") || $(button).attr("bg_color") || "#ffffff"}, 500);
            $(foreground).animate({backgroundColor: $(button).attr("fg-color") || $(button).attr("fg_color") || "#ffffff"}, 500).text("Delete");
        }, 7500);
    }
});

let blockBtns = $("#user-manager #user-block");
$(blockBtns.on({
    mouseenter: function() {
        // Get components
        let button = $(this);
        let foreground = $(button).find("span");
        // Hover behaviour
        $(button).animate({ backgroundColor: "#88A236" }, 500).data('hovering', true);
        $(foreground).animate({ backgroundColor: "#B1CA65" }, 500).text($(button).attr('data-active') === "1" ? "Unblock from course" : "Block from course");
    },
    mouseleave: function() {
        // Get components
        let button = $(this);
        let foreground = $(button).find("span");
        // Hover behaviour
        $(button).animate({ backgroundColor: "#A23636" }, 500).data('hovering', false);
        $(foreground).animate({ backgroundColor: "#CA6565"}, 500).text($(button).attr('data-active') !== "1" ? "Block from course" : "Unblock from course");
    },
    click: function() {
        // Get components
        let button = $(this);
        let foreground = $(button).find("span");
        let nearestCol = $(button).closest(".table-row");
        let userId = $(nearestCol).attr('id');
        // Click behaviour
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            url: userBlockRoute,
            data: {
                userId: userId,
                modificationType: 'activeState'
            },
            success: function(newState) {
                if ($(button).data('hovering')) {
                    $(button).animate({ backgroundColor: "#88A236"}, 500).attr("data-active", newState ? "1" : "0");
                    $(foreground).animate({backgroundColor: "#B1CA65"}, 500).text(newState ? "Unblock from course" : "Block from course");
                } else {
                    $(button).animate({ backgroundColor: "#A23636"}, 500).attr("data-active", newState ? "1" : "0");
                    $(foreground).animate({backgroundColor: "#CA6565"}, 500).text(newState ? "Block from course" : "Unblock from course");
                }
            }
        });
    }
}));
