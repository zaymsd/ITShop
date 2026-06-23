<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ITShop') }}</title>

        <!-- Fonts: Inter via bunny.net -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-body">
        <!-- Scrim Background covering the page content (Pinterest-like) -->
        <div class="min-h-screen flex flex-col items-center justify-center p-4 bg-black/50 relative overflow-hidden">

            <!-- Modal Card (rounded.lg, padding 32px, canvas background, 16px ambient shadow) -->
            <div class="relative z-10 w-full max-w-[480px]">
                <div class="bg-canvas rounded-[32px] p-8 shadow-[0_0_16px_rgba(0,0,0,0.1)] flex flex-col items-center">
                    
                    <!-- Logo / Branding -->
                    <a href="/" wire:navigate class="mb-4 flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-on-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="12" rx="2" stroke-width="2"/>
                                <line x1="2" y1="20" x2="22" y2="20" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h1 class="text-heading-lg text-ink text-center mb-1">
                            Welcome to ITShop
                        </h1>
                        <p class="text-body-md text-body text-center">
                            Manage your POS with ease
                        </p>
                    </a>

                    <!-- Livewire Form Injection -->
                    <div class="w-full mt-4">
                        {{ $slot }}
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>
