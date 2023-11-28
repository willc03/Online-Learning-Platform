<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="wordsearch-field"> {{-- Display the options to the user --}}
    <div class="wordsearch">
        @foreach($puzzle->toArray() as $row)
            <div class="row">
                @foreach($row as $letter)
                    <p>{{ $letter }}</p>
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
        const wordsearch = $(".wordsearch")

        {{-- Functions --}}
        function resizeWordsearch() {
            $(wordsearch).height($(wordsearch).width());
        }
        resizeWordsearch();

        {{-- Events --}}
        $(window).on("resize", resizeWordsearch);

        {{-- General scripting --}}
        $(".wordsearch p").width( ( 100 / $(".row").length ) + "%");
    });
</script>
