{{-- A file which provides to views the basic structure of an HTML page. --}}
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> {{-- This viewport tag allows for mobile optimisation. --}}
        <title>{{ $title }} - Online Learning Platform</title> {{-- A title can be provided when the component is called --}}
        <link rel="stylesheet" type="text/css" href="{{ asset('style.css') }}"> {{-- Use a global stylesheet --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    </head>

    <body class="block-transitions">
        {{ $slot }} {{-- The slot allows the component to be used as a HTML-like tag in Blade PHP files. --}}
    </body>

    {{-- A function will be called to remove the block-transitions element from the body, this will stop odd transitions from appearing when the page loads --}}
    <script>
        (function() {
            setTimeout(() => {
                document.body.classList.remove("block-transitions");
            }, 1);
        })()
    </script>

</html>
