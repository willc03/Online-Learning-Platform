$("div.high-score")
    .each(function() {
        let span = $(this).find("span.text");
        $(span)
            .data({
                width: $(span).width()
            })
            .css({
                width: 0,
                overflow: "hidden",
                whiteSpace: "nowrap"
            })
    })
    .on({
        mouseenter: function() {
            let span = $(this).find("span.text");
            $(span).stop().animate({ width: $(span).data("width") }, 300);
        },
        mouseleave: function() {
            let span = $(this).find("span.text");
            $(span).stop().animate({ width: 0 }, 300);
        }
    })
