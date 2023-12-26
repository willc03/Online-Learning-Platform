<x-structure.wrapper title="test">
    <form method="post" enctype="multipart/form-data" id="basic-file-upload">
        {{-- A basic file upload form will be used to allow for unique styling on different pages --}}
        @csrf
        @method('POST')

        <input type="hidden" name="id" id="course-id" value="{{ explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2] }}">

        <fieldset>
            <div class="message" id="file-upload-message-box" style="display: none">
                <p id="file-upload-message"></p>
            </div>
            <legend>File upload:</legend>

            <label for="name">File name:</label>
            <input type="text" name="name" id="file-upload-name" required>

            <label for="file">Upload a file:</label>
            <input type="file" name="file" id="file-upload-slot" required>

            <input type="submit">
        </fieldset>

        <script>
            upload_url = "{{ url('course/'.explode('/', preg_replace( "#^[^:/.]*[:/]+#i", "", url()->current()) )[2]) }}/upload"; {{-- https://speedysense.com/php-remove-http-https-www-and-slashes-from-url/ --}}
        </script>
        <script src="{{ asset("assets/scripts/forms/file_upload.js") }}"></script>
    </form>
</x-structure.wrapper>
