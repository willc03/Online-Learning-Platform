<x-structure.base :$title> {{-- Use the base structure before the header is made --}}
    <div class="wrapper">
        <header>
            <div id="menu-toggle" class="menu-toggle"> {{-- A custom SVG is implemented for the hamburger menu, this will make it easily animatable --}}
                <a href="#" onclick="document.getElementById('menu-toggle').classList.toggle('active'); document.getElementById('navigation').classList.toggle('active');">
                    <svg width="30" height="30" viewBox="0 0 30 30">
                        @for ($i = 0; $i < 3; $i++)
                            <rect class="child-{{ $i }}" y="{{ $i * 13 }}" width="30" height="2" rx="1.5" fill="#1f1f1f" />
                        @endfor
                        <line class="child-3" x1="4" x2="26" y1="4" y2="26" stroke="#1f1f1f" stroke-width="2" stroke-linecap="round" />
                        <line class="child-4" x1="4" x2="26" y1="26" y2="4" stroke="#1f1f1f" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </a>
            </div>
            <div class="title"> {{-- Add a clickable hyperlink as the header title to take the user to the home page --}}
                <h2><a href="{{ route('home') }}">{{ env('APP_NAME') }}</a></h2>
            </div>
            <div class="user-account"> {{-- Add a button to take the user to their account --}}
                <a href="{{ url("/account") }}"> {{-- An image button is used for interactivity --}}
                    <img width="50" height="50" src="{{ asset('assets/images/user-icon.svg') }}" alt="A button with an outline of a person for account access">
                </a>
            </div>
        </header>
        <nav id="navigation">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ url('/about') }}">About</a>
            <a href="{{ route('courses') }}">Courses</a>
            @auth
                <form method="post" action="{{ route('logout') }}" id="logout-form"> {{-- A form is required to send a post request --}}
                    @csrf
                    <a class="clickable" onclick="document.getElementById('logout-form').submit();">Logout</a>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
            @endauth
        </nav>
        <main>
            @if($errors->any())
                <x-messages.error title="Unexpected Error" description="" :passed_errors="$errors->all()" />
            @endif
            {{ $slot }} {{-- The calling component can be used as an HTML tag with inner content by using slots --}}
        </main>
    </div>
    <footer>
        @guest {{-- Display a guest-only box to encourage the user to sign up --}}
            <div class="main-box">
                <div class="description">
                    <p>Ready to start learning?</p>
                    <p>Sign up today</p>
                </div>
                <div class="sign-up">
                    <a href="{{ url('register') }}">Sign Up</a>
                </div>
            </div>
            <div class="links-box">

            </div>
        @endguest
    </footer>
</x-structure.base>
