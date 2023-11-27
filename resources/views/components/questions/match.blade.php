<input type="hidden" name="answer" id="answer"> {{-- Add a hidden attribute for the final submitted value --}}
<fieldset class="match-field"> {{-- Display the options to the user --}}
    <legend>Order these items correctly:</legend>

    <style>
        .three-d.selected {
            background-color: #0276aa!important;
        }
        .three-d.selected .foreground {
            background-color: #48b1e1 !important;
        }

        .three-d.incorrect {
            background-color: #CA6565!important;
        }
        .three-d.incorrect .foreground {
            background-color: #A23636!important;
        }

        .three-d.correct {
            filter: saturate(100%)!important;
            background-color: #88A236!important;
        }
        .three-d.correct .foreground {
            filter: saturate(100%)!important;
            background-color: #B1CA65!important;
            transform: translateY(-2px)!important;
        }
    </style>

    @shuffle($choices)
    <div class="left-box">
        @foreach($choices as $choice)
            <x-components.3d_button type="button" fg_color="#81d4fa" bg_color="#5a94af">{{ $choice[0] }}</x-components.3d_button>
        @endforeach
    </div>

    @shuffle($choices)
    <div class="right-box">
        @foreach($choices as $choice)
            <x-components.3d_button type="button" fg_color="#81d4fa" bg_color="#5a94af">{{ $choice[1] }}</x-components.3d_button>
        @endforeach
    </div>
</fieldset>

<script>
    const leftBoxButtons = $(".left-box .three-d");
    const rightBoxButtons = $(".right-box .three-d");
    {{-- Set all the boxes to be the same size --}}
    function resizeButtons() {
        let maxHeight = 0;
        $(".three-d").css("height", "").each(function() {
            maxHeight = $(this).height() > maxHeight ? $(this).height() : maxHeight;
        }).height(maxHeight + 10).data("extended", true);
    }
    resizeButtons();

    $(window).on("resize", resizeButtons);
    {{-- Manage the matching of items --}}
    function onButtonClick(buttonGroup, currentButton) {
        let selectedButtons = $(buttonGroup).filter(".selected").removeClass("selected");
        if (selectedButtons[0] !== currentButton) {
            $(currentButton).addClass("selected");
        }

        if ($(".left-box .three-d.selected").length && $(".right-box .three-d.selected").length) {
            let leftElement = $(".left-box .three-d.selected").first();
            let rightElement = $(".right-box .three-d.selected").first();
            setTimeout(() => $(leftBoxButtons).add(rightBoxButtons).removeClass("selected"), 175);

            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                method: "POST",
                url: "{{ route('question.partial') }}",
                data: {
                    'question_id': $("#question_id").val(),
                    'answer': [$(leftElement).text(), $(rightElement).text()]
                }
            }).done(function(data) {
                var elements = $(leftElement).add(rightElement);

                if (data === "true") {
                    elements.addClass("correct").prop('disabled', true);
                } else {
                    elements.addClass("incorrect");
                    setTimeout(function() {
                        elements.removeClass("incorrect");
                    }, 175);
                }
            });
        }
    }

    $(leftBoxButtons).on("click", function() {
        onButtonClick(leftBoxButtons, this);
    });
    $(rightBoxButtons).on("click", function() {
        onButtonClick(rightBoxButtons, this);
    });
</script>
