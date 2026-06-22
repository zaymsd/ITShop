<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          darkMode: localStorage.getItem('darkMode') === 'true',
          init() {
              document.documentElement.classList.toggle('dark', this.darkMode);
              this.$watch('darkMode', val => {
                  localStorage.setItem('darkMode', val);
                  document.documentElement.classList.toggle('dark', val);
              });
          }
      }"
      :class="{ 'dark': darkMode }">
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
    <body class="font-sans antialiased text-slate-800 dark:text-slate-200">
        <!-- Gradient Background -->
        <div class="min-h-screen flex flex-col items-center justify-center p-4
                    bg-gradient-to-br from-primary-600 via-primary-800 to-slate-900
                    dark:from-slate-900 dark:via-primary-950 dark:to-slate-950
                    relative overflow-hidden">

            <!-- Decorative Background Elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none" aria-hidden="true">
                <!-- Gradient Orbs -->
                <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full
                            bg-accent-400/20 dark:bg-accent-600/10 blur-3xl"></div>
                <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full
                            bg-primary-400/20 dark:bg-primary-700/10 blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full
                            bg-accent-500/5 dark:bg-accent-500/5 blur-3xl"></div>

                <!-- Grid Pattern Overlay -->
                <div class="absolute inset-0 opacity-5"
                     style="background-image: radial-gradient(circle, rgba(255,255,255,0.3) 1px, transparent 1px);
                            background-size: 30px 30px;"></div>
            </div>

            <!-- Dark Mode Toggle (top right) -->
            <button @click="darkMode = !darkMode"
                    class="absolute top-6 right-6 p-2.5 rounded-xl
                           bg-white/10 hover:bg-white/20 dark:bg-slate-800/50 dark:hover:bg-slate-700/50
                           text-white/80 hover:text-white
                           backdrop-blur-sm transition-all duration-300 z-10"
                    :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                </svg>
            </button>

            <!-- Logo / Branding -->
            <div class="relative z-10 mb-8 animate-fade-in">
                <a href="/" wire:navigate class="flex flex-col items-center gap-3 group">
                    <!-- Laptop Icon -->
                    <div class="w-16 h-16 rounded-2xl bg-white/10 dark:bg-white/5 backdrop-blur-sm
                                flex items-center justify-center
                                group-hover:bg-white/20 dark:group-hover:bg-white/10
                                transition-all duration-300 group-hover:scale-105">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="12" rx="2" stroke-width="1.5"/>
                            <line x1="2" y1="20" x2="22" y2="20" stroke-width="1.5" stroke-linecap="round"/>
                            <rect x="7" y="7" width="6" height="4" rx="1" fill="currentColor" opacity="0.3"/>
                        </svg>
                    </div>
                    <!-- Brand Name -->
                    <div>
                        <h1 class="text-3xl font-extrabold text-white tracking-tight">
                            IT<span class="text-accent-400">Shop</span>
                        </h1>
                        <p class="text-sm text-white/50 text-center font-medium tracking-wider uppercase">Point of Sale</p>
                    </div>
                </a>
            </div>

            <!-- Glass Card -->
            <div class="relative z-10 w-full sm:max-w-md animate-scale-in">
                <div class="rounded-2xl px-8 py-8 sm:px-10 sm:py-10 shadow-2xl
                            bg-white/80 dark:bg-slate-800/80
                            backdrop-blur-xl
                            border border-white/30 dark:border-slate-700/50">
                    {{ $slot }}
                </div>
            </div>

            <!-- Footer -->
            <p class="relative z-10 mt-8 text-xs text-white/30">
                &copy; {{ date('Y') }} ITShop POS System
            </p>
        </div>
    </body>
</html>
