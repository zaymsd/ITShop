<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        {{-- Welcome Card --}}
        <div class="rounded-xl bg-gradient-to-r from-primary-600 to-accent-600
                    dark:from-primary-700 dark:to-accent-700
                    p-6 sm:p-8 text-white shadow-lg shadow-primary-500/20
                    animate-fade-in">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold">
                        Welcome to ITShop POS 👋
                    </h1>
                    <p class="mt-2 text-white/80 max-w-xl">
                        Manage your laptop and accessories inventory, process sales transactions,
                        and generate reports — all in one place.
                    </p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-16 h-16 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Quick Stats Placeholder --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            {{-- Stat Card: Total Products --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700
                        p-5 hover:shadow-lg hover:border-primary-300 dark:hover:border-primary-600
                        transition-all duration-200 animate-slide-in-up">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/50
                                flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Products</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">—</p>
                    </div>
                </div>
            </div>

            {{-- Stat Card: Today's Sales --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700
                        p-5 hover:shadow-lg hover:border-emerald-300 dark:hover:border-emerald-600
                        transition-all duration-200 animate-slide-in-up" style="animation-delay: 50ms;">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/50
                                flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Today's Sales</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">—</p>
                    </div>
                </div>
            </div>

            {{-- Stat Card: Low Stock --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700
                        p-5 hover:shadow-lg hover:border-amber-300 dark:hover:border-amber-600
                        transition-all duration-200 animate-slide-in-up" style="animation-delay: 100ms;">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/50
                                flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Low Stock</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">—</p>
                    </div>
                </div>
            </div>

            {{-- Stat Card: Categories --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700
                        p-5 hover:shadow-lg hover:border-accent-300 dark:hover:border-accent-600
                        transition-all duration-200 animate-slide-in-up" style="animation-delay: 150ms;">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-accent-100 dark:bg-accent-900/50
                                flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent-600 dark:text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Categories</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">—</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700
                    p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/50
                            flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white">Getting Started</h3>
            </div>
            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                This dashboard will be populated with real-time widgets including sales charts,
                recent transactions, inventory alerts, and more. Use the sidebar navigation to
                explore different sections of the POS system.
            </p>
        </div>
    </div>
</x-app-layout>
