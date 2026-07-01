<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'ITShop') }}</title>
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/ITShop-logo.png') }}">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Swiper CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    </head>
    <body class="antialiased font-sans bg-[var(--color-surface-soft)] text-[var(--color-body)] overflow-x-hidden">
        <!-- Primary Nav (Logged Out) -->
        <header class="h-16 flex items-center justify-between px-6 bg-[var(--color-canvas)] border-b border-[var(--color-hairline)] sticky top-0 z-50">
            <div class="flex items-center gap-2">
                <img src="{{ asset('images/ITShop-logo.png') }}" alt="ITShop Logo" class="w-[48px] h-[48px] object-contain">
                <span class="font-bold text-[var(--color-primary)] text-xl">ITShop</span>
            </div>
            
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-bold text-[var(--color-ink)] hover:text-[var(--color-primary)] transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-bold text-[var(--color-ink)] hover:text-[var(--color-primary)] transition-colors">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Sign up</a>
                        @endif
                    @endauth
                @endif
            </div>
        </header>

        <!-- Hero Banner (Full Width, Compact/Uncropped) -->
        <section class="w-full animate-fade-in relative bg-[var(--color-canvas)]">
            <img src="{{ asset('images/content/banner.png') }}" alt="ITShop Banner" class="w-full h-auto block" onerror="this.src='https://placehold.co/1920x600/E60023/FFF?text=Banner+Placeholder'; this.onerror=null;">
        </section>

        <!-- Main Content Area -->
        <main class="flex flex-col gap-[var(--spacing-section)] py-12 px-6 max-w-7xl mx-auto w-full">
            
            <!-- Tagline Section -->
            <section class="flex flex-col items-center text-center gap-6 py-12 max-w-4xl mx-auto">
                <h1 class="text-display-xl text-[var(--color-ink)] font-extrabold leading-tight">
                    Manage your shop inventory effortlessly.
                </h1>
                <p class="text-heading-md text-[var(--color-mute)] max-w-2xl">
                    ITShop POS is designed to stay out of your way so you can focus on what matters most — running your business.
                </p>
                <div class="mt-6 flex justify-center gap-4">
                    <a href="{{ route('register') }}" class="btn-primary px-10 py-4 text-lg shadow-[0_8px_24px_rgba(230,0,35,0.25)] hover:scale-105 transition-all">Get started</a>
                    <a href="#features" class="btn-secondary px-10 py-4 text-lg bg-[var(--color-surface-card)]">Learn more</a>
                </div>
            </section>

            <!-- Feature Rows (Alternating Layout using slide1 & slide2) -->
            <section id="features" class="flex flex-col gap-[var(--spacing-section)]">
                
                <!-- Feature 1: Text Left, Slide 1 Right -->
                <div class="flex flex-col md:flex-row items-center gap-[var(--spacing-xxl)] bg-[var(--color-canvas)] p-[var(--spacing-xl)] rounded-[32px] shadow-[0_0_24px_rgba(0,0,0,0.03)] hover:shadow-[0_0_32px_rgba(0,0,0,0.06)] transition-shadow">
                    <div class="flex-1 flex flex-col gap-4">
                        <h2 class="text-heading-xl text-[var(--color-ink)]">Everything in one place.</h2>
                        <p class="text-[var(--color-body)] text-lg leading-relaxed">
                            Track products, brands, and categories within our unified Master Data system. Powerful filtering and instant search.
                        </p>
                        <a href="{{ route('register') }}" class="btn-primary self-start mt-2">Explore Master Data</a>
                    </div>
                    <div class="flex-1 w-full flex items-center justify-center">
                        <img src="{{ asset('images/content/slide1.png') }}" alt="Slide 1" class="w-full rounded-[24px] object-cover shadow-sm" onerror="this.src='https://placehold.co/600x400/E6F4EA/111?text=Slide+1'; this.onerror=null;">
                    </div>
                </div>

                <!-- Feature 2: Slide 2 Left, Text Right -->
                <div class="flex flex-col md:flex-row-reverse items-center gap-[var(--spacing-xxl)] bg-[var(--color-canvas)] p-[var(--spacing-xl)] rounded-[32px] shadow-[0_0_24px_rgba(0,0,0,0.03)] hover:shadow-[0_0_32px_rgba(0,0,0,0.06)] transition-shadow">
                    <div class="flex-1 flex flex-col gap-4">
                        <h2 class="text-heading-xl text-[var(--color-ink)]">Fast & Fluid POS.</h2>
                        <p class="text-[var(--color-body)] text-lg leading-relaxed">
                            A point-of-sale interface built for speed. Process sales securely and instantly with barcode support.
                        </p>
                        <a href="{{ route('register') }}" class="btn-primary self-start mt-2">See POS in action</a>
                    </div>
                    <div class="flex-1 w-full flex items-center justify-center">
                        <img src="{{ asset('images/content/slide2.png') }}" alt="Slide 2" class="w-full rounded-[24px] object-cover shadow-sm" onerror="this.src='https://placehold.co/600x400/ffeaea/111?text=Slide+2'; this.onerror=null;">
                    </div>
                </div>

            </section>

            <!-- Dynamic Product Catalog Slider -->
            <section class="py-12 flex flex-col gap-8 w-full overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-heading-xl text-[var(--color-ink)]">Latest Products</h2>
                        <p class="text-[var(--color-mute)] mt-1">Discover our newest additions to the inventory.</p>
                    </div>
                    <div class="flex gap-2">
                        <!-- Custom Navigation Buttons for Slider -->
                        <button class="swiper-prev w-10 h-10 rounded-full bg-[var(--color-surface-card)] text-[var(--color-ink)] flex items-center justify-center hover:bg-[var(--color-surface-soft)] shadow-sm transition-colors border border-[var(--color-hairline)]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button class="swiper-next w-10 h-10 rounded-full bg-[var(--color-surface-card)] text-[var(--color-ink)] flex items-center justify-center hover:bg-[var(--color-surface-soft)] shadow-sm transition-colors border border-[var(--color-hairline)]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
                
                <div class="swiper product-slider w-full pb-10">
                    <div class="swiper-wrapper">
                        @forelse($products as $product)
                            <div class="swiper-slide h-auto">
                                <div class="pin-card group cursor-pointer animate-fade-in flex flex-col h-full shadow-[0_4px_16px_rgba(0,0,0,0.04)] hover:shadow-[0_8px_24px_rgba(0,0,0,0.08)]">
                                    <!-- Image -->
                                    <div class="aspect-square bg-white rounded-t-[16px] overflow-hidden flex items-center justify-center relative">
                                        @if($product->primaryImage)
                                            <img src="{{ Storage::url($product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <svg class="w-16 h-16 text-[var(--color-mute)] opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        @endif
                                        
                                        @if($product->stock <= 0)
                                            <div class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full shadow-sm">Out of Stock</div>
                                        @endif
                                    </div>
                                    
                                    <!-- Info -->
                                    <div class="p-4 flex flex-col gap-1 flex-1 justify-between">
                                        <div>
                                            <h3 class="text-sm font-bold text-[var(--color-ink)] line-clamp-2 leading-tight group-hover:text-[var(--color-primary)] transition-colors">{{ $product->name }}</h3>
                                            <p class="text-xs text-[var(--color-mute)] font-medium mt-1">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                        </div>
                                        <div class="mt-3 text-md font-extrabold text-[var(--color-ink)]">
                                            Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="swiper-slide w-full text-center text-[var(--color-mute)] bg-[var(--color-canvas)] rounded-[32px] border border-dashed border-[var(--color-hairline)] py-12">
                                <svg class="w-12 h-12 mx-auto text-[var(--color-mute)] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                <p class="text-heading-sm font-semibold">No products available yet.</p>
                                <p class="text-sm">Products will appear here once they are added to the inventory.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="swiper-pagination mt-4"></div>
                </div>
            </section>

        </main>

        <footer class="border-t border-[var(--color-hairline)] bg-[var(--color-canvas)] py-[var(--spacing-xl)] px-6 mt-12">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/ITShop-logo.png') }}" alt="ITShop Logo" class="w-[32px] h-[32px] object-contain">
                    <span class="font-bold text-[var(--color-ink)]">ITShop POS</span>
                </div>
                <p class="text-caption-sm text-[var(--color-mute)]">© {{ date('Y') }} ITShop. All rights reserved.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-caption-sm text-[var(--color-mute)] hover:text-[var(--color-primary)] transition-colors">Privacy</a>
                    <a href="#" class="text-caption-sm text-[var(--color-mute)] hover:text-[var(--color-primary)] transition-colors">Terms</a>
                    <a href="#" class="text-caption-sm text-[var(--color-mute)] hover:text-[var(--color-primary)] transition-colors">Help</a>
                </div>
            </div>
        </footer>

        <!-- Swiper JS -->
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var swiper = new Swiper(".product-slider", {
                    slidesPerView: 1,
                    spaceBetween: 16,
                    loop: false,
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-next',
                        prevEl: '.swiper-prev',
                    },
                    breakpoints: {
                        640: { slidesPerView: 2, spaceBetween: 16 },
                        768: { slidesPerView: 3, spaceBetween: 24 },
                        1024: { slidesPerView: 4, spaceBetween: 24 }, // 4 tampilan awal di desktop
                    },
                });
            });
        </script>
    </body>
</html>
