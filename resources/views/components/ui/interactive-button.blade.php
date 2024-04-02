<button class="three-d {{ $attributes->get('class') }}" {{ $attributes->except(['class']) }} @if(isset($bgColor)) style="background-color: {{ $bgColor }}" @endif>
    <span class="foreground" @if(isset($bgColor)) style="background-color: {{ $fgColor }}" @endif>{{ $slot }}</span>
</button>
