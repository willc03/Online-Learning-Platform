let expiryToggleButtons = $('.expiry-date-toggle');
let expirySectionToggles = $('.expiry-date-form');
// Initial settings
$(expirySectionToggles).each(function () {
    $(this)
        .data('height', $(this).height())
        .css('height', 0);
})
// Click behaviour
$(expiryToggleButtons).on('click', function () {
    let button = $(this);
    let box = $(button).closest('.table-col');
    let form = $(box).find(".expiry-date-form");
    //
    $(button).animate({ height: 0 }, 500, function () {
        $(this).css('overflow', 'hidden');
    });
    $(form).animate({ height: $(form).data('height'), padding: '10px' }, 500);
});
// Form editing
$(expirySectionToggles).each(function () {
    let input = $(this).find("input");
    let button = $(this).find("#submit-expiry-date");
    let remDateBtn = $(this).find("#date-remove");
    let row = $(this).closest(".table-row");
    let inviteId = $(row).attr('id');
    $(button).on("click", function () {
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, method: "POST", url: inviteModifyRoute, data: {
                inviteId: inviteId, modificationType: 'expiryDate', newDate: $(input).val()
            }, success: function () {
                location.reload();
            }
        });
    });

    $(remDateBtn).on("click", function () {
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, method: "POST", url: inviteModifyRoute, data: {
                inviteId: inviteId, modificationType: 'expiryDate', newDate: "01/01/1970 23:59", remove: 1,
            }, success: function () {
                location.reload();
            }
        });
    });

    let currentDate = new Date();
    $(input).datetimepicker({
        minDate: 0, defaultTime: "23:59", formatDate: "d/m/Y", formatTime: "H:i", format: "d/m/Y H:i", onChangeDateTime: function (currTime) {
            if ( stringToDate($(input).val()).getTime() < currentDate.getTime() ) {
                return $(button).prop("disabled", true);
            }
            $(button).prop("disabled", ($(input).val() === $(input).attr("data-initial")))
        }
    });
});

function stringToDate (dateString)
{
    var parts = dateString.split(/[\/\s:]/); // Split the string by "/", " ", and ":"
    var day = parseInt(parts[0], 10);
    var month = parseInt(parts[1], 10) - 1; // Months are 0-indexed in JavaScript
    var year = parseInt(parts[2], 10);
    var hours = parseInt(parts[3], 10);
    var minutes = parseInt(parts[4], 10);

    // Create a new Date object
    var date = new Date(year, month, day, hours, minutes);

    return date;
}

