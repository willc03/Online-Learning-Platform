let toggleButtons = $(".toggle-invite-activity");
$(toggleButtons.on({
    mouseenter: function() {
        // Get components
        let button = $(this);
        let foreground = $(button).find("span");
        // Hover behaviour
        $(button).animate({ backgroundColor: $(button).attr('data-active') === "true" ? "#A23636" : "#88A236" }, 500).data('hovering', true);
        $(foreground).animate({ backgroundColor: $(button).attr('data-active') === "true" ? "#CA6565" : "#B1CA65" }, 500).text($(button).attr('data-active') === "true" ? "Deactivate" : "Activate");
    },
    mouseleave: function() {
        // Get components
        let button = $(this);
        let foreground = $(button).find("span");
        // Hover behaviour
        $(button).animate({ backgroundColor: $(button).attr('data-active') === "false" ? "#A23636" : "#88A236" }, 500).data('hovering', false);
        $(foreground).animate({ backgroundColor: $(button).attr('data-active') === "false" ? "#CA6565" : "#B1CA65" }, 500).text($(button).attr('data-active') === "true" ? "Active" : "Inactive");
    },
    click: function() {
        // Get components
        let button = $(this);
        let foreground = $(button).find("span");
        let inviteHolder = $(button).closest(".table-row");
        let inviteId = $(inviteHolder).attr('id');
        // Click behaviour
        $.ajax({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            method: "POST",
            url: inviteModifyRoute,
            data: {
                inviteId: inviteId,
                modificationType: 'activeState'
            },
            success: function(newState) {
                if ($(button).data('hovering')) {
                    $(button).animate({ backgroundColor:  !newState ? "#88A236" : "#A23636"}, 500).attr("data-active", newState ? "true" : "false");
                    $(foreground).animate({backgroundColor:  !newState ? "#B1CA65" : "#CA6565"}, 500).text(newState ? "Deactivate" : "Activate");
                } else {
                    $(button).animate({ backgroundColor:  newState ? "#88A236" : "#A23636"}, 500).attr("data-active", newState ? "true" : "false");
                    $(foreground).animate({backgroundColor:  newState ? "#B1CA65" : "#CA6565"}, 500).text(newState ? "Active" : "Inactive");
                }
            }
        });
    }
}));
