$(function () {
    // Constants and variables
    const form = document.getElementById("basic-file-upload");
    const formFile = document.getElementById("file-upload-slot");
    const fileName = document.getElementById("file-upload-name");
    const courseId = document.getElementById("course-id");
    const messageBox = document.getElementById("file-upload-message-box");
    const messageMsg = document.getElementById("file-upload-message")

    // Functions
    function uploadFile ()
    {
        let fileContainer = new FormData();
        fileContainer.append('id', $(courseId).val());
        fileContainer.append('name', $(fileName).val())
        fileContainer.append('file', formFile.files[0]);

        $(messageBox).css("display", "none");
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, method: "post", url: upload_url, data: fileContainer, dataType: 'json', processData: false, contentType: false, success: function (data) {
                $(messageMsg).text("File upload " + (data[0] !== true ? "un" : "") + "successful");
                $(messageBox).css("display", "block");
                $(messageBox).attr("message-type", (data[0] === true ? "success" : "error"));
            }
        });
    }

    // General scripting
    $(form).on("submit", function (event) {
        event.preventDefault();

        if ( $(form).valid() ) { // jQuery validation plugin used for form validation check
            uploadFile();
        }
    })
});
