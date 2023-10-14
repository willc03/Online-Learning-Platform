<x-structure.base :$title> {{-- Use the base structure before the header is made --}}
    <div class="wrapper">
        <header>
            <div id="menu-toggle" class="menu-toggle">
                <a href="#" onclick="document.getElementById('menu-toggle').classList.toggle('active');">
                    <svg width="30" height="30" viewBox="0 0 30 30">
                        @for ($i = 0; $i < 3; $i++)
                            <rect class="child-{{ $i }}" y="{{ $i * 13 }}" width="30" height="2" rx="1.5" fill="#1f1f1f" />
                        @endfor
                        <line class="child-3" x1="4" x2="26" y1="4" y2="26" stroke="#1f1f1f" stroke-width="2" stroke-linecap="round" />
                        <line class="child-4" x1="4" x2="26" y1="26" y2="4" stroke="#1f1f1f" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </a>
            </div>
            <div class="title">
                <h2>Online Learning Platform</h2>
            </div>
            <div class="user-account">
                <a href="{{ url("/account") }}">
                    <img width="50" height="50" src="{{ asset('assets/images/user-icon.svg') }}" alt="A button with an outline of a person for account access">
                </a>
            </div>
        </header>
        <nav>
            <a href="{{ url('/') }}">Home</a>
        </nav>
        <main>
            {{ $slot }}
        </main>
    </div>
    <footer>
        <h1>Footer placeholder content</h1>
    </footer>
</x-structure.base>
