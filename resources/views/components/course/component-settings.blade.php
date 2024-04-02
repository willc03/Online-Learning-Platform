<div class="item-settings flex-row" style="opacity: 0; transition-duration: 500ms; transition-property: opacity;">
    <x-ui.interactive-button class="trash-button max-content course-button-mini" fg_color="#CA6565" bg_color="#A23636"><img width="20px" height="20px" src="{{ asset("assets/images/trash-can.svg") }}"></x-ui.interactive-button>
    @if ($numSections > 1)
        @if ($currentPos < $maxPos)
            <x-ui.interactive-button class="down-button max-content course-button-mini" fg-color="#43AA8B" bg-color="#245B4A"><img width="20" height="20" src="https://img.icons8.com/ios-glyphs/30/thick-arrow-pointing-down.png" alt="A thick arrow pointing down"/></x-ui.interactive-button>
        @endif
        @if ($currentPos > $minPos)
            <x-ui.interactive-button class="up-button max-content course-button-mini" fg-color="#43AA8B" bg-color="#245B4A"><img style="transform: rotate(180deg);" width="20" height="20" src="https://img.icons8.com/ios-glyphs/30/thick-arrow-pointing-down.png" alt="A thick arrow pointing up"/></x-ui.interactive-button>
        @endif
    @endif
</div>
