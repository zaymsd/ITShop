<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          darkMode: localStorage.getItem('darkMode') === 'true',
          sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
          mobileMenuOpen: false,
          init() {
              this.$watch('darkMode', val => {
                  localStorage.setItem('darkMode', val);
                  document.documentElement.classList.toggle('dark', val);
              });
              this.$watch('sidebarOpen', val => {
                  localStorage.setItem('sidebarOpen', val);
              });
              document.documentElement.classList.toggle('dark', this.darkMode);
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
    <body class="font-sans antialiased bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-200">
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
                               bg-white/80 dark:bg-slate-800/80 backdrop-blur-lg
                               border-b border-slate-200 dark:border-slate-700">

                    <!-- Left: Mobile hamburger + Breadcrumb -->
                    <div class="flex items-center gap-3">
                        <!-- Mobile Menu Button -->
                        <button @click="mobileMenuOpen = true"
                                class="lg:hidden p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100
                                       dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-700
                                       transition-colors duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Desktop Sidebar Toggle -->
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="hidden lg:flex p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100
                                       dark:text-slate-400 dark:hover:text-slate-200 dark:hover:bg-slate-700
                                       transition-colors duration-200"
                                title="Toggle Sidebar">
                            <svg class="w-5 h-5 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                        </button>

                        <!-- Page Heading / Breadcrumb -->
                        @if (isset($header))
                            <div class="text-lg font-semibold text-slate-800 dark:text-slate-200 animate-fade-in">
                                {{ $header }}
                            </div>
                        @endif
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode"
                                class="p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100
                                       dark:text-slate-400 dark:hover:text-amber-400 dark:hover:bg-slate-700
                                       transition-all duration-300"
                                :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                            <!-- Sun Icon (shown in dark mode) -->
                            <svg x-show="darkMode" x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 rotate-90 scale-0"
                                 x-transition:enter-end="opacity-100 rotate-0 scale-100"
                                 class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <!-- Moon Icon (shown in light mode) -->
                            <svg x-show="!darkMode" x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 -rotate-90 scale-0"
                                 x-transition:enter-end="opacity-100 rotate-0 scale-100"
                                 class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                        </button>

                        <!-- User Dropdown -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium
                                           text-slate-600 hover:text-slate-800 hover:bg-slate-100
                                           dark:text-slate-300 dark:hover:text-slate-100 dark:hover:bg-slate-700
                                           transition-colors duration-200">
                                <!-- User Avatar -->
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-accent-500
                                            flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden sm:inline"
                                      x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                                      x-text="name"
                                      x-on:profile-updated.window="name = $event.detail.name"></span>
                                <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 py-1 rounded-xl
                                        bg-white dark:bg-slate-800
                                        border border-slate-200 dark:border-slate-700
                                        shadow-lg shadow-slate-200/50 dark:shadow-slate-900/50"
                                 style="display: none;">
                                <a href="{{ route('profile') }}" wire:navigate
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300
                                          hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-150">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    {{ __('Profile') }}
                                </a>
                                <hr class="my-1 border-slate-200 dark:border-slate-700">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-2 w-full px-4 py-2 text-sm text-rose-600 dark:text-rose-400
                                                   hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors duration-150">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8 animate-fade-in">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-slate-400 dark:text-slate-600
                               border-t border-slate-200 dark:border-slate-700/50">
                    &copy; {{ date('Y') }} ITShop POS System. All rights reserved.
                </footer>
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
             class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden"
             style="display: none;"></div>
    </body>
</html>
