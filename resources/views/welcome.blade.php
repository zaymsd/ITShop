<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>ITShop POS</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased font-sans bg-[var(--color-surface-soft)] text-[var(--color-body)]">
        <!-- Primary Nav (Logged Out) -->
        <header class="h-16 flex items-center justify-between px-6 bg-[var(--color-canvas)]">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/ITShop-logo.png') }}" alt="ITShop Logo" class="w-[68px] h-[68px] object-contain">
                <span class="font-bold text-[var(--color-primary)] text-lg">ITShop</span>
            </div>
            
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-bold text-[var(--color-ink)] hover:underline">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-bold text-[var(--color-ink)] hover:underline">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Sign up</a>
                        @endif
                    @endauth
                @endif
            </div>
        </header>

        <main class="flex flex-col gap-[var(--spacing-section)] py-16 px-6 max-w-7xl mx-auto">
            
            <!-- Hero Section -->
            <section class="flex flex-col items-center text-center">
                <h1 class="text-display-xl text-[var(--color-ink)] mb-6 max-w-4xl">Manage your shop inventory effortlessly.</h1>
                <p class="text-heading-md text-[var(--color-body)] max-w-2xl mb-8">ITShop POS is designed to stay out of your way so you can focus on what matters most — running your business.</p>
                <div class="flex gap-4">
                    <a href="{{ route('register') }}" class="btn-primary px-6 py-3 text-lg">Get started</a>
                    <a href="#features" class="btn-secondary px-6 py-3 text-lg">Learn more</a>
                </div>
            </section>

            <!-- Feature Rows (Alternating Layout) -->
            <section id="features" class="flex flex-col gap-[var(--spacing-section)]">
                
                <!-- Feature 1: Text Left, Image Right -->
                <div class="flex flex-col md:flex-row items-center gap-[var(--spacing-xxl)] bg-[var(--color-canvas)] p-[var(--spacing-xxl)] rounded-[var(--radius-md)]">
                    <div class="flex-1 flex flex-col gap-4">
                        <h2 class="text-heading-xl text-[var(--color-ink)]">Everything in one place.</h2>
                        <p class="text-[var(--color-body)] text-lg leading-relaxed">
                            Track products, brands, and categories within our unified Master Data system.
                        </p>
                        <a href="{{ route('register') }}" class="btn-primary self-start mt-2">Explore Master Data</a>
                    </div>
                    <div class="flex-1 w-full bg-[var(--color-surface-card)] rounded-[var(--radius-md)] aspect-[4/3] flex items-center justify-center overflow-hidden">
                        <!-- Placeholder Image / Illustration -->
                        <div class="w-full h-full bg-slate-200"></div>
                    </div>
                </div>

                <!-- Feature 2: Image Left, Text Right -->
                <div class="flex flex-col md:flex-row-reverse items-center gap-[var(--spacing-xxl)] bg-[var(--color-canvas)] p-[var(--spacing-xxl)] rounded-[var(--radius-md)]">
                    <div class="flex-1 flex flex-col gap-4">
                        <h2 class="text-heading-xl text-[var(--color-ink)]">Fast & Fluid Transactions.</h2>
                        <p class="text-[var(--color-body)] text-lg leading-relaxed">
                            A point-of-sale interface built for speed. Process sales securely and instantly.
                        </p>
                        <a href="{{ route('register') }}" class="btn-primary self-start mt-2">See POS in action</a>
                    </div>
                    <div class="flex-1 w-full bg-[var(--color-surface-card)] rounded-[var(--radius-md)] aspect-[4/3] flex items-center justify-center overflow-hidden">
                        <!-- Placeholder Image / Illustration -->
                        <div class="w-full h-full bg-slate-200"></div>
                    </div>
                </div>

            </section>

        </main>

        <footer class="border-t border-[var(--color-hairline)] bg-[var(--color-canvas)] py-[var(--spacing-xl)] px-6">
            <div class="max-w-7xl mx-auto flex flex-col items-center">
                <p class="text-caption-sm text-[var(--color-mute)] mb-2">© {{ date('Y') }} ITShop. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-caption-sm text-[var(--color-mute)] hover:underline">Privacy</a>
                    <a href="#" class="text-caption-sm text-[var(--color-mute)] hover:underline">Terms</a>
                    <a href="#" class="text-caption-sm text-[var(--color-mute)] hover:underline">Help Center</a>
                </div>
            </div>
        </footer>
    </body>
</html>
