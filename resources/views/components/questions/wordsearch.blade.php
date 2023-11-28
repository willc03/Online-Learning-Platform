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
        const wordsearch = $(".wordsearch");
        const letters = $(".letter");

        {{-- Functions --}}
        function resizeWordsearch() {
            $(wordsearch).height($(wordsearch).width());
            $(letters).height($(letters).width());
        }

        {{-- Events --}}
        $(window).on("resize", resizeWordsearch);

        {{-- General scripting --}}
        $(letters).width( ( 100 / $(".row").length ) + "%");

        {{-- Initial function calls --}}
        resizeWordsearch();
    });
</script>
