$("#file-manager .three-d").on("click", function () {
    const button = $(this);
    const foreground = $(button).children('span')[0];
    let row = $(this).closest('.table-row');

    if ( $(button).data("shouldDelete") === true ) {
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, method: "DELETE", url: fileRemoveRoute, data: {
                fileId: $(row).attr('id')
            }, success: function () {
                // Hide the row
                $(row).css({
                    height: $(row).height(), overflow: 'hidden'
                }).animate({
                    height: 0
                }, 500, function () {
                    $(row).remove();
                });
            }
        });
    } else {
        $(button).animate({ backgroundColor: "#88A236" }, 500);
        $(foreground).animate({ backgroundColor: "#B1CA65" }, 500).text("Confirm Deletion");
        $(button).data("shouldDelete", true);

        setTimeout(function () {
            $(button).data("shouldDelete", false);
            $(button).animate({ backgroundColor: $(button).attr("bg-color") || $(button).attr("bg_color") || "#ffffff" }, 500);
            $(foreground).animate({ backgroundColor: $(button).attr("fg-color") || $(button).attr("fg_color") || "#ffffff" }, 500).text("Delete");
        }, 7500);
    }
});
