<x-structure.wrapper title="welcome">
    @if($errors->any())
        <x-messages.error title="Unexpected Error" description="" :passed_errors="$errors->all()" />
    @endif
</x-structure.wrapper>
