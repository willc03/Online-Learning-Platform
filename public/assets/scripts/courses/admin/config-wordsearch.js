$(function () {
    // jQuery groups
    const wordsearch = $(".wordsearch");
    const letters = $(".letter");

    // Variables
    let isMouseDown = false;
    let highlightedLetters = [];
    let startLetter = null;
    let initialDirection = null;

    // Functions
    function resizeWordsearch() {
        letters.each(function() {
            $(this).height($(this).width());
        });
    }


    function getDirection (currentLetter)
    { // This function will find out whether the selection is a row or column
        const currentRow = currentLetter.parent().index();
        const currentCol = currentLetter.index();

        if ( startLetter ) {
            const [ startRow, startCol ] = [ startLetter.parent().index(), startLetter.index() ];

            if ( currentRow === startRow ) {
                return "row";
            } else if ( currentCol === startCol ) {
                return "column";
            }

            return null;
        }

        return null;
    }

    function highlightLetter ()
    {
        const isHighlighted = highlightedLetters.some(element => element.is($(this)));
        if ( isMouseDown && !isHighlighted ) { // Only highlight letters while the mouse is down and its not already highlighted
            if ( !startLetter ) { // Special logic must be executed if only the starting letter is selected, as the direction is unknown
                startLetter = $(this);
                highlightedLetters.push($(this));
                $(this).addClass("wordsearch-selected");
                initialDirection = null; // Resetting the direction
            } else {
                const direction = getDirection($(this));

                if ( !initialDirection ) { // Set the direction of the selection if it is not set
                    initialDirection = direction;
                }

                // Check if the selected letter is in the same row or column and in the initial direction
                if ( direction === initialDirection ) {
                    highlightedLetters.push($(this));
                    $(this).addClass("wordsearch-selected");
                }
            }
        }
    }

    // Events
    $(window).on("resize", resizeWordsearch);
    $(letters)
        .on("mousedown", function () {
            isMouseDown = true;
            startLetter = null;
            initialDirection = null;
            highlightLetter.call($(this)); // This is used to set the value of $(this)
        })
        .on("mouseup", function () {
            isMouseDown = false;
            startLetter = null;
            initialDirection = null;
            $(letters).removeClass("wordsearch-selected");
        });
    $(letters).on("mouseover", highlightLetter);
    $(letters).on("mousedown", highlightLetter);

    // General scripting
    $(".wordsearch").each(function() {
        $(this).find(".letter").width((100 / $(this).find(".row").length) + "%");
    })

    // Initial function calls
    resizeWordsearch();
});
