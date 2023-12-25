<x-structure.wrapper title="test">
    <form method="post" enctype="multipart/form-data" id="basic-file-upload">
        {{-- A basic file upload form will be used to allow for unique styling on different pages --}}
        @csrf
        @method('POST')

        <input type="hidden" name="id" id="course-id" value="{{ explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2] }}">

        <fieldset>
            <legend>File upload:</legend>

            <label for="name">File name:</label>
            <input type="text" name="name" id="file-upload-name" required>

            <label for="file">Upload a file:</label>
            <input type="file" name="file" id="file-upload-slot" required>

            <input type="submit">
        </fieldset>

        <script>
            $(function() {
                {{-- Constants and variables --}}
                const form = document.getElementById("basic-file-upload");
                const formFile = document.getElementById("file-upload-slot");
                const fileName = document.getElementById("file-upload-name");
                const courseId = document.getElementById("course-id");
                {{-- Functions --}}
                function uploadFile() {
                    let fileContainer = new FormData();
                    fileContainer.append('id', $(courseId).val());
                    fileContainer.append('name', $(fileName).val())
                    fileContainer.append('file', formFile.files[0]);

                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        method: "post",
                        url: "{{ url('course/'.explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2]) }}/upload", {{-- https://speedysense.com/php-remove-http-https-www-and-slashes-from-url/ --}}
                        data: fileContainer,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                        }
                    });
                }
                {{-- General scripting --}}
                $(form).on("submit", function( event ) {
                    event.preventDefault();

                    if ($(form).valid()) { {{-- jQuery validation plugin used for form validation check --}}
                        uploadFile();
                    }
                })
            });
        </script>
    </form>
</x-structure.wrapper>
