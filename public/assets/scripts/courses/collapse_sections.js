$(".section .collapse-button").on("click", function() {
    var contentElement = $(this).next();
    var isCollapsed = contentElement.hasClass("collapsed");

    if ($(contentElement).hasClass("collapsing")) {
        return;
    }
    $(contentElement).addClass("collapsing");

    $(this).toggleClass("collapsed");
    contentElement.toggleClass("collapsed").animate({
        height: isCollapsed ? contentElement.prop('scrollHeight') + "px" : "0px",
        paddingTop: isCollapsed ? "10px" : "0",
        paddingBottom: isCollapsed ? "10px" : "0"
    }, 1000, function() {
        $(contentElement).removeClass("collapsing");
    });
});
