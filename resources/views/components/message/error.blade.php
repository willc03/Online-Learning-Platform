<div class="message error-message">
    <h3>{{ $title }}</h3>
    <p>{{ $description }}</p>

    @if (isset($passedErrors))
        @foreach ($passedErrors as $err)
            <p>{{ $err }}</p>
        @endforeach
    @endif
</div>
