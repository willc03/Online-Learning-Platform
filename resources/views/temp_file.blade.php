@php
    session()->flash('course', '9ac2e12e-2bda-4314-b639-e249841f4f7c');
@endphp

@if(session()->has('success'))
    <h4>FILE UPLOAD SUCCESSFUL</h4>
@endif


<form method="post" action="{{ route('temp.upload') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <label for="name">File name:</label>
    <input type="text" name="name" required>

    <br>

    <label for="file">Upload a file:</label>
    <input type="file" name="file" required>

    <br>

    <input type="submit">
</form>
