$(".lesson-config h2 button.three-d#delete-button")
    .each(function () {
        $(this).data({
            active: false, id: $(this).parent().parent().attr('id')
        });
    })
    .on("click", function () {
        let isActive = $(this).data("active");
        if ( isActive ) {
            let button = $(this);
            // If the confirmation state is active, send the deletion request
            $.ajax({
                url: lessonItemDeleteRoute, method: 'DELETE', headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, data: {
                    'edit-type': 'component-delete', 'data': $(button).data('id')
                }, success: function () {
                    let mainContainer = $(button).closest('.lesson-config');
                    $(mainContainer).css('overflow', 'hidden').animate({
                        height: 0, padding: 0
                    }, 500, function() {
                        $(mainContainer).remove();
                        if ($('.lesson-config').length === 0) {
                            location.href = location.href;
                        }
                    });
                }
            });
        } else {
            let button = $(this);
            // Set the active state
            $(button).data("active", true);
            // Keep a log of the current inside of the foreground
            let currentInterior = $(button).find("span.foreground").html();
            // Animate the button to its new state
            $(button).animate({ backgroundColor: "#88A236" }, 500);
            $(button).find('.foreground').animate({ backgroundColor: "#B1CA65" }, 500).text("Confirm");
            // Timeout function to revert to non-delete state
            setTimeout(function () {
                $(button).data("active", false);
                $(button).animate({ backgroundColor: "#A23636" }, 500);
                $(button).find('.foreground').animate({ backgroundColor: "#CA6565" }, 500).html(currentInterior);
            }, 7000);
        }
    })
