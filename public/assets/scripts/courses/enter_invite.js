$(".join-btn").on('click', function() {
    // Create a form element
    var form = $('<form>', {
        method: 'post',
        action: $(this).data('url') // Get the URL from data attribute of the button
    });

    // Append CSRF token input field
    form.append($('<input>', {
        type: 'hidden',
        name: '_token',
        value: $('meta[name="csrf-token"]').attr('content')
    }));

    // Append form to the document body
    $('body').append(form);

    // Submit the form
    form.submit();

    // Clean up: Remove the form from the document
    form.remove();
});
