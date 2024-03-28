$('#create-course')
    .data({
        height: $('#create-course').height(), boxShadow: $('#create-course').css('boxShadow')
    })
    .css({
        height: 0, boxShadow: 0, overflow: 'hidden'
    });
$('#course-create-btn').on('click', function () {
    $('#create-course').animate({
        height: $('#create-course').data('height'), boxShadow: $('#create-course').data('boxShadow')
    }, 500, function () {
        $('#create-course').css('height', '');
    })
});

// Also add a small section to make all the items the same size
$(function () {
    function calculateMaxHeight ()
    {
        // Reset the heights
        $('.course-item').height('');
        // Get the tallest
        var maxHeight = 0;
        $('.course-item').each(function () {
            var height = $(this).height();
            if ( height > maxHeight ) {
                maxHeight = height;
            }
        });
        // Set them all to the tallest
        $('.course-item').height(maxHeight);
    }

    calculateMaxHeight();
    $(window).on('resize', calculateMaxHeight);
});
