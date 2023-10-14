{{-- A file which provides to views the basic structure of an HTML page. --}}
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> {{-- This viewport tag allows for mobile optimisation. --}}
        <title>{{ $title }}</title> {{-- A title can be provided when the component is called --}}
        <link rel="stylesheet" type="text/css" href="{{ asset('style.css') }}" {{-- Use a global stylesheet --}}
    </head>
    <body>
        {{ $slot }} {{-- The slot allows the component to be used as a HTML-like tag in Blade PHP files. --}}
    </body>
</html>
