<x-app-layout>
    <x-slot name="header">
        <h2 class="text-heading-lg text-[var(--color-ink)]">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Content Sections -->
    <div class="flex flex-col gap-[var(--spacing-section)]">
        
        <!-- Welcome Hero Feature -->
        <div class="bg-[var(--color-canvas)] rounded-[var(--radius-lg)] p-[var(--spacing-xxl)]">
            <h1 class="text-heading-xl text-[var(--color-ink)] mb-4">
                Welcome to ITShop POS
            </h1>
            <p class="text-[var(--color-body)] text-lg max-w-2xl">
                Manage your laptop and accessories inventory, process sales transactions,
                and generate reports — all in one place.
            </p>
        </div>

        <!-- Quick Stats Masonry (Using grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[var(--spacing-sm)]">
            
            <!-- Stat Card: Total Products -->
            <div class="bg-[var(--color-surface-card)] rounded-[var(--radius-md)] p-6">
                <div class="flex flex-col gap-2">
                    <div class="w-10 h-10 rounded-full bg-[var(--color-canvas)] flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-[var(--color-ink)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <p class="text-caption-md font-bold text-[var(--color-mute)] uppercase tracking-wide">Total Products</p>
                    <p class="text-heading-xl text-[var(--color-ink)]">—</p>
                </div>
            </div>

            <!-- Stat Card: Today's Sales -->
            <div class="bg-[var(--color-surface-card)] rounded-[var(--radius-md)] p-6">
                <div class="flex flex-col gap-2">
                    <div class="w-10 h-10 rounded-full bg-[var(--color-canvas)] flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-[var(--color-ink)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <p class="text-caption-md font-bold text-[var(--color-mute)] uppercase tracking-wide">Today's Sales</p>
                    <p class="text-heading-xl text-[var(--color-ink)]">—</p>
                </div>
            </div>

            <!-- Stat Card: Low Stock -->
            <div class="bg-[var(--color-surface-card)] rounded-[var(--radius-md)] p-6">
                <div class="flex flex-col gap-2">
                    <div class="w-10 h-10 rounded-full bg-[var(--color-canvas)] flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-[var(--color-ink)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <p class="text-caption-md font-bold text-[var(--color-mute)] uppercase tracking-wide">Low Stock</p>
                    <p class="text-heading-xl text-[var(--color-ink)]">—</p>
                </div>
            </div>

            <!-- Stat Card: Categories -->
            <div class="bg-[var(--color-surface-card)] rounded-[var(--radius-md)] p-6">
                <div class="flex flex-col gap-2">
                    <div class="w-10 h-10 rounded-full bg-[var(--color-canvas)] flex items-center justify-center mb-2">
                        <svg class="w-5 h-5 text-[var(--color-ink)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <p class="text-caption-md font-bold text-[var(--color-mute)] uppercase tracking-wide">Categories</p>
                    <p class="text-heading-xl text-[var(--color-ink)]">—</p>
                </div>
            </div>

        </div>

        <!-- Info Card -->
        <div class="bg-[var(--color-canvas)] rounded-[var(--radius-lg)] p-[var(--spacing-xl)]">
            <h3 class="text-heading-md text-[var(--color-ink)] mb-4">Getting Started</h3>
            <p class="text-[var(--color-body)] leading-relaxed">
                This dashboard will be populated with real-time widgets including sales charts,
                recent transactions, inventory alerts, and more. Use the sidebar navigation to
                explore different sections of the POS system.
            </p>
        </div>

    </div>
</x-app-layout>
