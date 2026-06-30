<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ITShop') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/ITShop-logo.png') }}">

        <!-- Fonts: Inter via bunny.net -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Dark Mode Initialization Script (Prevents flash) -->
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-[var(--color-surface-soft)] text-[var(--color-body)] transition-colors duration-300"
          x-data="{
              sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
              mobileMenuOpen: false,
              theme: localStorage.theme || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
              toggleTheme() {
                  this.theme = this.theme === 'light' ? 'dark' : 'light';
                  localStorage.theme = this.theme;
                  if (this.theme === 'dark') {
                      document.documentElement.classList.add('dark');
                  } else {
                      document.documentElement.classList.remove('dark');
                  }
              },
              init() {
                  this.$watch('sidebarOpen', val => {
                      localStorage.setItem('sidebarOpen', val);
                  });
                  if (this.theme === 'dark') {
                      document.documentElement.classList.add('dark');
                  } else {
                      document.documentElement.classList.remove('dark');
                  }
              }
          }">
        <div class="min-h-screen flex">

            <!-- ============================================================
                 SIDEBAR NAVIGATION
                 ============================================================ -->
            <livewire:layout.navigation />

            <!-- ============================================================
                 MAIN CONTENT AREA
                 ============================================================ -->
            <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
                 :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-16'">

                <!-- Top Bar -->
                <header class="sticky top-0 z-30 h-16 flex items-center justify-between gap-4 px-4 sm:px-6 lg:px-8
                               bg-[var(--color-canvas)] border-b border-[var(--color-hairline)]">

                    <!-- Left: Mobile hamburger + Breadcrumb -->
                    <div class="flex items-center gap-3">
                        <!-- Mobile Menu Button -->
                        <button @click="mobileMenuOpen = true"
                                class="lg:hidden p-2 rounded-full text-mute hover:bg-surface-card transition-colors duration-200">
                            <svg class="w-6 h-6 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Desktop Sidebar Toggle -->
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="hidden lg:flex p-2 rounded-full text-mute hover:bg-surface-card transition-colors duration-200"
                                title="Toggle Sidebar">
                            <svg class="w-5 h-5 text-ink transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </button>

                        <!-- Page Heading / Breadcrumb -->
                        @if (isset($header))
                            <div class="text-heading-md text-ink animate-fade-in">
                                {{ $header }}
                            </div>
                        @endif
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center gap-4">
                        <!-- Theme Toggle Button -->
                        <button @click="toggleTheme()" class="p-2 rounded-full text-mute hover:text-primary hover:bg-surface-card transition-colors duration-200" title="Toggle Dark Mode">
                            <!-- Sun icon -->
                            <svg x-show="theme === 'dark'" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <!-- Moon icon -->
                            <svg x-show="theme === 'light'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        </button>

                        <!-- Search Bar placeholder -->
                        <div class="hidden sm:flex relative">
                            <input type="text" placeholder="Search..." class="bg-[var(--color-surface-card)] border-none text-[var(--color-ink)] text-sm rounded-full py-2 pl-10 pr-4 focus:ring-2 focus:ring-[var(--color-focus-outer)] outline-none w-64 h-10">
                            <svg class="w-4 h-4 text-[var(--color-ink)] absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>

                        <!-- User Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 p-1 rounded-full text-sm font-medium transition-colors duration-200 hover:bg-surface-card">
                                <!-- User Avatar -->
                                <div class="w-8 h-8 rounded-full bg-[var(--color-surface-card)] flex items-center justify-center text-[var(--color-ink)] font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <svg class="w-4 h-4 text-ink transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 py-2 rounded-[16px] bg-[var(--color-canvas)] border border-[var(--color-hairline)] shadow-[0_0_16px_rgba(0,0,0,0.1)]"
                                 style="display: none;">
                                <div class="px-4 py-2 mb-2 border-b border-[var(--color-hairline)]">
                                    <p class="text-sm font-bold text-ink truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-mute truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile') }}" wire:navigate
                                   class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-ink hover:bg-[var(--color-surface-card)] transition-colors duration-150">
                                    {{ __('Profile') }}
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-2 w-full px-4 py-2 text-sm font-semibold text-ink hover:bg-[var(--color-surface-card)] transition-colors duration-150 text-left">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8 animate-fade-in">
                    <!-- Standard 64px gap / 8px gutter spacing -->
                    <div class="flex flex-col gap-[64px]">
                        {{ $slot }}
                    </div>
                </main>

            </div>
        </div>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false"
             class="fixed inset-0 z-40 bg-black/50 lg:hidden"
             style="display: none;"></div>
    </body>
</html>
