$(".invite-link-copy").on('click', function () {
    let item = $(this);
    navigator.clipboard.writeText($(item).attr("data-link"))
        .then(() => {
            $(item).find("span").text("Copied link to clipboard");
            setTimeout(function () {
                $(item).find("span").text("Copy link");
            }, 2000);
        })
        .catch(err => {
            console.log('Encountered an error with the clipboard: ', err);
        });
});
