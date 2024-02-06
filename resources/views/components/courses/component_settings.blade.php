<div class="item-settings flex-row" style="opacity: 0; transition-duration: 500ms; transition-property: opacity;">
    <x-components.3d_button class="trash-button max-content course-button-mini" fg_color="#CA6565" bg_color="#A23636"><img width="20px" height="20px" src="{{ asset("assets/images/trash-can.svg") }}"></x-components.3d_button>
    @if ($numSections > 1)
        @if ($currentPos < $maxPos)
            <x-components.3d_button class="down-button max-content course-button-mini" fg-color="#9EC5AB" bg-color="#5e9c73"><img width="20" height="20" src="https://img.icons8.com/ios-glyphs/30/thick-arrow-pointing-down.png" alt="A thick arrow pointing down"/></x-components.3d_button>
        @endif
        @if ($currentPos > $minPos)
            <x-components.3d_button class="up-button max-content course-button-mini" fg-color="#9EC5AB" bg-color="#5e9c73"><img style="transform: rotate(180deg);" width="20" height="20" src="https://img.icons8.com/ios-glyphs/30/thick-arrow-pointing-down.png" alt="A thick arrow pointing up"/></x-components.3d_button>
        @endif
    @endif
</div>
