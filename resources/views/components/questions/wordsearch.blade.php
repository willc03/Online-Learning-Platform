<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="wordsearch-field"> {{-- Display the options to the user --}}
    <div class="wordsearch">
        @foreach($puzzle->toArray() as $row)
            <div class="row">
                @foreach($row as $letter)
                    <div class="letter">
                        <p>{{ $letter }}</p>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</fieldset>
<fieldset class="word-list wordsearch-field" style="width: 50%; margin-top: 10px; align-self: center">
    <legend>Word list:</legend>
    <div class="word-list">
        @foreach($puzzle->getWordList() as $word)
            <p id="ws-word-{{ strtolower($word->word) }}">{{ $word->word }}</p>
        @endforeach
    </div>
</fieldset>

<div id="cover-box" style="display: none">
    <div class="content">
        <h3 id="title"></h3>
        <p id="info"></p>
        <x-components.3d_button type="button" fg_color="#B1CA65" bg_color="#88A236" onclick="$('#cover-box').css('display', 'none')">Done</x-components.3d_button>
    </div>
</div>

<script>
    $(function() {
        {{-- jQuery groups --}}
        const wordsearch = $(".wordsearch");
        const letters = $(".letter");

        {{-- Variables --}}
        let isMouseDown = false;
        let highlightedLetters = [];
        let startLetter = null;
        let initialDirection = null;

        {{-- Functions --}}
        function resizeWordsearch() {
            $(wordsearch).height($(wordsearch).width());
            $(letters).height($(letters).width());
        }

        function getDirection(currentLetter) { {{-- This function will find out whether the selection is a row or column --}}
            const currentRow = currentLetter.parent().index();
            const currentCol = currentLetter.index();

            if (startLetter) {
                const [startRow, startCol] = [startLetter.parent().index(), startLetter.index()];

                if (currentRow === startRow) {
                    return "row";
                } else if (currentCol === startCol) {
                    return "column";
                }

                return null;
            }

            return null;
        }

        function highlightLetter() {
            if (isMouseDown && !highlightedLetters.includes($(this))) { {{-- Only highlight letters while the mouse is down and its not already highlighted --}}
                if (!startLetter) { {{-- Special logic must be executed if only the starting letter is selected, as the direction is unknown --}}
                    startLetter = $(this);
                    highlightedLetters.push($(this));
                    $(this).addClass("wordsearch-selected");
                    initialDirection = null; {{-- Resetting the direction  --}}
                } else {
                    const direction = getDirection($(this));

                    if (!initialDirection) { {{-- Set the direction of the selection if it is not set --}}
                        initialDirection = direction;
                    }

                    {{-- Check if the selected letter is in the same row or column and in the initial direction --}}
                    if (direction === initialDirection) {
                        highlightedLetters.push($(this));
                        $(this).addClass("wordsearch-selected");
                    }
                }
            }
        }

        {{-- Events --}}
        $(window).on("resize", resizeWordsearch);
        $(letters)
            .on("mousedown", function() {
                isMouseDown = true;
                startLetter = null;
                initialDirection = null;
                highlightLetter.call($(this)); {{-- This is used to set the value of $(this) --}}
            })
            .on("mouseup", function() {
                isMouseDown = false;
                startLetter = null;
                initialDirection = null;
                $(letters).removeClass("wordsearch-selected");

                let selectedLetters = highlightedLetters;

                {{-- Letter-handling logic --}}
                let answer = [];
                $.each($(selectedLetters), function(_, val) {
                    answer.push($(val).text().trim());
                })

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    method: "POST",
                    url: "{{ route('question.partial') }}",
                    data: {
                        'question_id': $("#question_id").val(),
                        'answer': answer
                    }
                }).done(function(data) {
                });

                highlightedLetters = [];
            });
        $(letters).on("mouseover", highlightLetter);
        $(letters).on("mousedown", highlightLetter);

        {{-- General scripting --}}
        $(letters).width( ( 100 / $(".row").length ) + "%");

        {{-- Initial function calls --}}
        resizeWordsearch();
    });

</script>
