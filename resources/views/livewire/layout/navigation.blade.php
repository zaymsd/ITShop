<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

{{-- ============================================================
     SIDEBAR NAVIGATION - Desktop fixed, Mobile slide-over
     Uses Alpine.js for collapse/expand and section toggles
     ============================================================ --}}
<aside
    x-data="{
        masterDataOpen: {{ request()->routeIs('categories.*', 'brands.*', 'products.*', 'suppliers.*', 'users.*') ? 'true' : 'false' }},
        transaksiOpen: {{ request()->routeIs('pos.*', 'purchases.*') ? 'true' : 'false' }},
        laporanOpen: {{ request()->routeIs('reports.*') ? 'true' : 'false' }},
    }"
    class="fixed inset-y-0 left-0 z-50 flex flex-col
           bg-slate-900 dark:bg-slate-950
           border-r border-slate-800 dark:border-slate-800
           transition-all duration-300 ease-in-out
           shadow-xl shadow-slate-900/50"
    :class="{
        'w-64': sidebarOpen,
        'w-16': !sidebarOpen,
        'translate-x-0': mobileMenuOpen || true,
        '-translate-x-full lg:translate-x-0': !mobileMenuOpen,
    }">

    {{-- ============================================================
         SIDEBAR HEADER / LOGO
         ============================================================ --}}
    <div class="h-16 flex items-center px-4 border-b border-slate-800 shrink-0">
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center gap-3 group overflow-hidden">
            {{-- Logo Icon --}}
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500
                        flex items-center justify-center shrink-0
                        group-hover:shadow-lg group-hover:shadow-primary-500/30
                        transition-all duration-300">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="12" rx="2" stroke-width="1.5"/>
                    <line x1="2" y1="20" x2="22" y2="20" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            {{-- Logo Text (hidden when collapsed) --}}
            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200 delay-100"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 class="flex flex-col overflow-hidden">
                <span class="text-lg font-extrabold text-white tracking-tight leading-none">
                    IT<span class="text-accent-400">Shop</span>
                </span>
                <span class="text-[10px] font-medium text-slate-500 tracking-widest uppercase">POS System</span>
            </div>
        </a>

        {{-- Close button (mobile only) --}}
        <button @click="mobileMenuOpen = false"
                class="lg:hidden ml-auto p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800
                       transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ============================================================
         NAVIGATION ITEMS (scrollable)
         ============================================================ --}}
    <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4 space-y-1">

        {{-- ---- Dashboard ---- --}}
        <a href="{{ route('dashboard') }}" wire:navigate
           class="sidebar-nav-item sidebar-nav-item-hover {{ request()->routeIs('dashboard') ? 'sidebar-nav-item-active' : '' }}">
            {{-- Icon: chart-bar --}}
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
                  x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                  class="truncate">Dashboard</span>
        </a>

        {{-- ---- Divider ---- --}}
        <div class="pt-3 pb-1" x-show="sidebarOpen">
            <p class="px-3 text-[10px] font-semibold text-slate-500 uppercase tracking-widest">Menu</p>
        </div>
        <div class="pt-3 pb-1" x-show="!sidebarOpen">
            <hr class="border-slate-700/50">
        </div>

        {{-- ============================================================
             MASTER DATA (Collapsible Group)
             ============================================================ --}}
        <div>
            <button @click="masterDataOpen = !masterDataOpen"
                    class="sidebar-nav-item sidebar-nav-item-hover w-full justify-between
                           {{ request()->routeIs('categories.*', 'brands.*', 'products.*', 'suppliers.*', 'users.*') ? 'text-white' : '' }}">
                <div class="flex items-center gap-3">
                    {{-- Icon: cube --}}
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                          class="truncate">Master Data</span>
                </div>
                {{-- Chevron --}}
                <svg x-show="sidebarOpen" class="w-4 h-4 shrink-0 transition-transform duration-200"
                     :class="masterDataOpen ? 'rotate-90' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            {{-- Submenu Items --}}
            <div x-show="masterDataOpen && sidebarOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mt-1 ml-4 pl-3 border-l border-slate-700/50 space-y-1">

                {{-- Categories --}}
                {{-- TODO: Connect to categories.index route when created --}}
                <a href="#"
                   class="sidebar-nav-item sidebar-nav-item-hover text-sm {{ request()->routeIs('categories.*') ? 'sidebar-nav-item-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                    </svg>
                    <span class="truncate">Categories</span>
                </a>

                {{-- Brands --}}
                {{-- TODO: Connect to brands.index route when created --}}
                <a href="#"
                   class="sidebar-nav-item sidebar-nav-item-hover text-sm {{ request()->routeIs('brands.*') ? 'sidebar-nav-item-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    <span class="truncate">Brands</span>
                </a>

                {{-- Products --}}
                {{-- TODO: Connect to products.index route when created --}}
                <a href="#"
                   class="sidebar-nav-item sidebar-nav-item-hover text-sm {{ request()->routeIs('products.*') ? 'sidebar-nav-item-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="truncate">Products</span>
                </a>

                {{-- Suppliers --}}
                {{-- TODO: Connect to suppliers.index route when created --}}
                <a href="#"
                   class="sidebar-nav-item sidebar-nav-item-hover text-sm {{ request()->routeIs('suppliers.*') ? 'sidebar-nav-item-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                    </svg>
                    <span class="truncate">Suppliers</span>
                </a>

                {{-- Users (admin only) --}}
                {{-- TODO: Connect to users.index route when created, add admin gate --}}
                <a href="#"
                   class="sidebar-nav-item sidebar-nav-item-hover text-sm {{ request()->routeIs('users.*') ? 'sidebar-nav-item-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="truncate">Users</span>
                </a>
            </div>
        </div>

        {{-- ============================================================
             TRANSAKSI (Collapsible Group)
             ============================================================ --}}
        <div>
            <button @click="transaksiOpen = !transaksiOpen"
                    class="sidebar-nav-item sidebar-nav-item-hover w-full justify-between
                           {{ request()->routeIs('pos.*', 'purchases.*') ? 'text-white' : '' }}">
                <div class="flex items-center gap-3">
                    {{-- Icon: shopping-cart --}}
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                          class="truncate">Transaksi</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 shrink-0 transition-transform duration-200"
                     :class="transaksiOpen ? 'rotate-90' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div x-show="transaksiOpen && sidebarOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mt-1 ml-4 pl-3 border-l border-slate-700/50 space-y-1">

                {{-- Kasir / POS --}}
                {{-- TODO: Connect to pos.index route when created --}}
                <a href="#"
                   class="sidebar-nav-item sidebar-nav-item-hover text-sm {{ request()->routeIs('pos.*') ? 'sidebar-nav-item-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="truncate">Kasir / POS</span>
                </a>

                {{-- Pembelian --}}
                {{-- TODO: Connect to purchases.index route when created --}}
                <a href="#"
                   class="sidebar-nav-item sidebar-nav-item-hover text-sm {{ request()->routeIs('purchases.*') ? 'sidebar-nav-item-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="truncate">Pembelian</span>
                </a>
            </div>
        </div>

        {{-- ============================================================
             LAPORAN (Collapsible Group)
             ============================================================ --}}
        <div>
            <button @click="laporanOpen = !laporanOpen"
                    class="sidebar-nav-item sidebar-nav-item-hover w-full justify-between
                           {{ request()->routeIs('reports.*') ? 'text-white' : '' }}">
                <div class="flex items-center gap-3">
                    {{-- Icon: document-report --}}
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                          class="truncate">Laporan</span>
                </div>
                <svg x-show="sidebarOpen" class="w-4 h-4 shrink-0 transition-transform duration-200"
                     :class="laporanOpen ? 'rotate-90' : ''"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div x-show="laporanOpen && sidebarOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="mt-1 ml-4 pl-3 border-l border-slate-700/50 space-y-1">

                {{-- Laporan Penjualan --}}
                {{-- TODO: Connect to reports.sales route when created --}}
                <a href="#"
                   class="sidebar-nav-item sidebar-nav-item-hover text-sm {{ request()->routeIs('reports.sales') ? 'sidebar-nav-item-active' : '' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="truncate">Laporan Penjualan</span>
                </a>
            </div>
        </div>
    </nav>

    {{-- ============================================================
         USER INFO & LOGOUT (bottom of sidebar)
         ============================================================ --}}
    <div class="shrink-0 border-t border-slate-800 p-3">
        <div class="flex items-center gap-3" x-show="sidebarOpen">
            {{-- User Avatar --}}
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-500 to-accent-500
                        flex items-center justify-center text-white text-sm font-bold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            {{-- User Info --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-white truncate"
                   x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                   x-text="name"
                   x-on:profile-updated.window="name = $event.detail.name">
                </p>
                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
            </div>
            {{-- Logout Button --}}
            <button wire:click="logout"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800
                           transition-colors duration-200"
                    title="Log Out">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </div>

        {{-- Collapsed state: just show logout icon --}}
        <div x-show="!sidebarOpen" class="flex justify-center">
            <button wire:click="logout"
                    class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-800
                           transition-colors duration-200"
                    title="Log Out">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </div>
    </div>
</aside>
