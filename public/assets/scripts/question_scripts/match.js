const leftBoxButtons = $(".left-box .three-d");
const rightBoxButtons = $(".right-box .three-d");

// Set all the boxes to be the same size
function resizeButtons ()
{
    let maxHeight = 0;
    $(".three-d").css("height", "").each(function () {
        maxHeight = $(this).height() > maxHeight ? $(this).height() : maxHeight;
    }).height(maxHeight + 10).data("extended", true);
}

resizeButtons();

$(window).on("resize", resizeButtons);

// Manage the matching of items
function onButtonClick (buttonGroup, currentButton)
{
    let selectedButtons = $(buttonGroup).filter(".selected").removeClass("selected");
    if ( selectedButtons[0] !== currentButton ) {
        $(currentButton).addClass("selected");
    }

    if ( $(".left-box .three-d.selected").length && $(".right-box .three-d.selected").length ) {
        let leftElement = $(".left-box .three-d.selected").first();
        let rightElement = $(".right-box .three-d.selected").first();
        setTimeout(() => $(leftBoxButtons).add(rightBoxButtons).removeClass("selected"), 175);

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, method: "POST", url: ajaxRoute, data: {
                'question_id': $("#question_id").val(), 'answer': [ $(leftElement).text(), $(rightElement).text() ]
            }
        }).done(function (data) {
            var elements = $(leftElement).add(rightElement);

            if ( data === "true" ) {
                elements.addClass("correct").prop('disabled', true);

                let areAllButtonsDisabled = $('.match-field .three-d:disabled').length === $('.match-field .three-d').length;
                if ( areAllButtonsDisabled ) {
                    $('#answer').val(true);
                    $(".question-form").submit();
                }
            } else {
                elements.addClass("incorrect");
                setTimeout(function () {
                    elements.removeClass("incorrect");
                }, 175);
            }
        });
    }
}

$(leftBoxButtons).on("click", function () {
    onButtonClick(leftBoxButtons, this);
});
$(rightBoxButtons).on("click", function () {
    onButtonClick(rightBoxButtons, this);
});
