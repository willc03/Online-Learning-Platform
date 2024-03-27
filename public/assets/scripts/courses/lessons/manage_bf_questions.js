function getElementDetails(element) {
    const $element = $(element);
    return {
        width: $element.width(),
        height: $element.height(),
        x: $element.offset().left,
        y: $element.offset().top,
    };
}

$("div.lesson-config.fill-blanks").each(function() {
    let container = $(this);
    let blanks = $(container).find("span.blank");
    let options = $(container).find("button.three-d");

    $(options).each(function() {
        if ($(this).attr('id')) {
            let index = $(this).attr('id');
            let blank = $(blanks).not('.filled')[0];
            let fieldDetails = getElementDetails(blank);
            $(this).css({
                width: fieldDetails.width,
                height: fieldDetails.height,
                left: fieldDetails.x + 1,
                top: fieldDetails.y - 6,
                position: 'absolute'
            });
            $(blank).css('borderWidth', 0).addClass("filled").attr("option", index);
        }
    })
});

// Window logic, to move the absolute form buttons when the window size is changed.
$(window).on("resize", resetBFButtons);
