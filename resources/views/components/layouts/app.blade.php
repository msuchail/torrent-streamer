<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <title>{{ $title ?? 'Page Title' }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.png') }}">
    </head>
    <body>

    <style>
        .bg-dots{background-image:url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(200,200,255,0.15)'/%3E%3C/svg%3E");
    </style>

    <div class="relative min-h-screen bg-center bg-dots bg-gray-900 selection:bg-indigo-500 text-white">
        <header>
            <div class="justify-end pt-3 container mx-auto flex flex-col gap-5 pb-5">
                <nav class="grid grid-cols-3 items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-5" wire:navigate>
                        <img src="{{ asset('images/logo.png') }}" alt="" class="h-12">
                        <span class="hidden md:block text-3xl">TorrentStream</span>
                    </a>

                    @auth
                        <ul class="flex gap-5 justify-center">
                            <li><a wire:navigate href="{{ route('movie.index') }}">Films</a></li>
                            <li><a wire:navigate href="{{ route('serie.index') }}">Séries</a></li>
                        </ul>
                    @endauth

                    <ul class="flex gap-5 justify-end">
                        @auth
                            <li><a href="{{ route('logout') }}">Se déconnecter</a></li>
                        @else
                            <li><a href="{{ route('login') }}">Se connecter</a></li>
                            <li><a href="{{ route('register') }}">S'inscrire</a></li>
                        @endauth
                    </ul>
                </nav>
                {{ $headerExtras ?? '' }}
            </div>

        </header>
        <main class="container mx-auto pb-20">
            {{ $slot }}
        </main>
        <footer>

        </footer>
    </div>
    </body>
</html>


