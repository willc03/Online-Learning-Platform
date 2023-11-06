<button class="three-d {{ $attributes->get('class') }}" {{ $attributes->except(['class']) }}>
    <span class="foreground">{{ $slot }}</span>
</button>
