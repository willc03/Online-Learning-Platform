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
    let maxHeight = 0;
    $(".three-d").each(function() {
        maxHeight = $(this).height() > maxHeight ? $(this).height() : maxHeight;
    }).height(maxHeight + 10);
    {{-- Manage the matching of items --}}
    function onButtonClick(buttonGroup, currentButton) {
        let selectedButtons = $(buttonGroup).filter(".selected").removeClass("selected");
        if (selectedButtons[0] !== currentButton) {
            $(currentButton).addClass("selected");
        }

        if ($(".left-box .three-d.selected").length && $(".right-box .three-d.selected").length) {
            let leftElement = $(".left-box .three-d.selected").first().text();
            let rightElement = $(".right-box .three-d.selected").first().text();
            setTimeout(() => $(leftBoxButtons).add(rightBoxButtons).removeClass("selected"), 175);
        }
    }
    
    $(leftBoxButtons).on("click", function() {
        onButtonClick(leftBoxButtons, this);
    });
    $(rightBoxButtons).on("click", function() {
        onButtonClick(rightBoxButtons, this);
    });
</script>
