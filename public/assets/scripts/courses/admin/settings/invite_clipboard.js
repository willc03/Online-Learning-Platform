$("#invite-link-copy").on('click', function () {
    console.log($(this).attr("data-link"));
    navigator.clipboard.writeText($(this).attr("data-link"))
        .then(() => {
            let preText = $(this).find("span").text();
            $(this).find("span").text("Copied link to clipboard")
            setTimeout(function () {
                $(this).find("span").text(preText);
            }, 2000);
        })
        .catch(err => {
            console.log('Encountered an error with the clipboard: ', err);
        });
});
