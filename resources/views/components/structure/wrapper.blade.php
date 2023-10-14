<x-structure.base :$title> {{-- Use the base structure before the header is made --}}
    <div class="wrapper">
        <header>
            <div class="title">
                <h1>Header placeholder content</h1>
            </div>
            <nav>
                <a href="{{ url("/") }}">Home</a>
            </nav>
        </header>
        <main>
            {{ $slot }}
        </main>
    </div>
    <footer>
        <h1>Footer placeholder content</h1>
    </footer>
</x-structure.base>
