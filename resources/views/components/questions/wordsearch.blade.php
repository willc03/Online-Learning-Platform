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
    <div class="word-list">
        @foreach($puzzle->getWordList() as $word)
            <p>{{ $word->word }}</p>
        @endforeach
    </div>
</fieldset>

<script>
    $(function() {
        {{-- jQuery groups --}}
        const field = $(".wordsearch-field");
        const wordsearch = $(".wordsearch");
        const letters = $(".letter");

        {{-- Variables --}}
        let isMouseDown = false;
        let highlightedLetters = [];

        {{-- Functions --}}
        function resizeWordsearch() {
            $(wordsearch).height($(wordsearch).width());
            $(letters).height($(letters).width());
        }
        function highlightLetter() {
            if (isMouseDown && !highlightedLetters.includes($(this))) {
                highlightedLetters.push($(this));
                $(this).addClass("wordsearch-selected");
            }
        }

        {{-- Events --}}
        $(window).on("resize", resizeWordsearch);
        $(letters)
            .on("mousedown", function() {
                isMouseDown = true;
                highlightedLetters.push($(this));
                $(this).addClass("wordsearch-selected");
            })
            .on("mouseup", function() {
                isMouseDown = false;
                $(letters).removeClass("wordsearch-selected");
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
