@php
    session()->flash('course', '9ac2e12e-2bda-4314-b639-e249841f4f7c');
@endphp



<form method="post" action="{{ route('temp.upload') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <label for="file">Upload a file:</label>
    <input type="file" name="file">

    <input type="submit">
</form>
