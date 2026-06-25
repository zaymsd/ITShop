<?php

use App\Models\Product;
use App\Models\Sale;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public function with(): array
    {
        $today = today();
        
        $totalProducts = Product::count();
        
        $salesToday = Sale::with('saleItems.product')->whereDate('created_at', $today)->get();
        $transactionsToday = $salesToday->count();
        
        $revenueToday = $salesToday->sum('grand_total');
        
        $profitToday = $salesToday->sum(function($sale) {
            $cost = $sale->saleItems->sum(fn($item) => $item->qty * ($item->product->buy_price ?? 0));
            return ($sale->total - $sale->discount) - $cost;
        });

        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->take(5)->get();

        $chartDates = [];
        $chartSales = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $chartDates[] = $date->format('d M');
            $chartSales[] = Sale::whereDate('created_at', $date)->sum('grand_total');
        }

        return [
            'totalProducts' => $totalProducts,
            'transactionsToday' => $transactionsToday,
            'revenueToday' => $revenueToday,
            'profitToday' => $profitToday,
            'lowStockProducts' => $lowStockProducts,
            'chartDates' => json_encode($chartDates),
            'chartSales' => json_encode($chartSales),
        ];
    }
}; ?>

<div class="space-y-8">
    {{-- ── Welcome Hero ──────────────────────────────────────────────────────── --}}
    <div class="bg-primary rounded-[32px] p-8 md:p-10 relative overflow-hidden flex items-center justify-between">
        {{-- Decorative Flat Elements --}}
        <div class="absolute -right-8 -top-8 w-40 h-40 bg-white opacity-10 rounded-full"></div>
        <div class="absolute right-24 bottom-[-40px] w-24 h-24 bg-white opacity-10 rounded-full"></div>
        
        <div class="relative z-10 w-full max-w-3xl">
            <h1 class="text-[32px] md:text-[40px] font-black text-on-dark leading-tight mb-3">
                Halo, {{ auth()->user()->name }}! 👋
            </h1>
            <p class="text-[16px] md:text-[18px] text-on-dark/80 font-medium">
                Pantau performa hari ini dan jangan lewatkan pergerakan stok toko Anda.
            </p>
        </div>
        
        <div class="hidden lg:flex relative z-10 w-24 h-24 rounded-[24px] bg-white/10 items-center justify-center backdrop-blur-sm">
            <svg class="w-12 h-12 text-on-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        </div>
    </div>

    {{-- ── Quick Stats Grid ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Stat Card: Total Products -->
        <div class="bg-surface-card rounded-[32px] p-6 border border-hairline hover:shadow-[0_0_0_2px_#111111] transition-all duration-200 group">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-[16px] bg-surface-soft group-hover:bg-[var(--color-danger-bg)] transition-colors flex items-center justify-center">
                    <svg class="w-6 h-6 text-mute group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-mute uppercase tracking-wider">Produk Aktif</span>
            </div>
            <p class="text-[36px] font-black text-ink leading-none">{{ number_format($totalProducts) }}</p>
        </div>

        <!-- Stat Card: Today's Transactions -->
        <div class="bg-surface-card rounded-[32px] p-6 border border-hairline hover:shadow-[0_0_0_2px_#111111] transition-all duration-200 group">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-[16px] bg-surface-soft group-hover:bg-[var(--color-danger-bg)] transition-colors flex items-center justify-center">
                    <svg class="w-6 h-6 text-mute group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-mute uppercase tracking-wider">Transaksi Hari Ini</span>
            </div>
            <p class="text-[36px] font-black text-ink leading-none">{{ number_format($transactionsToday) }}</p>
        </div>

        <!-- Stat Card: Today's Revenue -->
        <div class="bg-surface-card rounded-[32px] p-6 border border-hairline hover:shadow-[0_0_0_2px_#111111] transition-all duration-200 group">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-[16px] bg-surface-soft group-hover:bg-[var(--color-danger-bg)] transition-colors flex items-center justify-center">
                    <svg class="w-6 h-6 text-mute group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-mute uppercase tracking-wider">Omzet Hari Ini</span>
            </div>
            <p class="text-[28px] font-black text-ink leading-none mt-2 truncate" title="Rp {{ number_format($revenueToday, 0, ',', '.') }}">
                Rp {{ number_format($revenueToday, 0, ',', '.') }}
            </p>
        </div>

        <!-- Stat Card: Today's Profit -->
        <div class="bg-surface-card rounded-[32px] p-6 border border-hairline hover:shadow-[0_0_0_2px_#111111] transition-all duration-200 group">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-[16px] bg-surface-soft group-hover:bg-[var(--color-danger-bg)] transition-colors flex items-center justify-center">
                    <svg class="w-6 h-6 text-mute group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-mute uppercase tracking-wider">Laba Kotor</span>
            </div>
            <p class="text-[28px] font-black text-ink leading-none mt-2 truncate" title="Rp {{ number_format($profitToday, 0, ',', '.') }}">
                Rp {{ number_format($profitToday, 0, ',', '.') }}
            </p>
        </div>

    </div>

    {{-- ── Main Content Grid ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Chart Section -->
        <div class="lg:col-span-2 bg-surface-card rounded-[32px] p-8 border border-hairline">
            <h3 class="text-heading-md text-ink mb-6">Grafik Penjualan (7 Hari)</h3>
            <div id="salesChart" class="w-full h-[320px]"></div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="bg-surface-card rounded-[32px] p-8 border border-hairline flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-heading-md text-ink">Stok Menipis</h3>
                <span class="px-3 py-1 rounded-[12px] bg-[var(--color-danger-bg)] text-primary text-[10px] font-black uppercase tracking-wider">
                    Peringatan
                </span>
            </div>
            
            <div class="flex-1 space-y-3">
                @forelse($lowStockProducts as $product)
                    <div class="flex items-center justify-between p-4 rounded-[16px] bg-surface-soft hover:bg-canvas transition-colors cursor-default border border-transparent hover:border-ash">
                        <div class="min-w-0 flex-1 pr-4">
                            <p class="text-[14px] font-bold text-ink truncate">{{ $product->name }}</p>
                            <p class="text-xs font-mono text-mute truncate mt-0.5">{{ $product->sku }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-[20px] font-black {{ $product->stock == 0 ? 'text-primary' : 'text-ink' }}">
                                {{ $product->stock }}
                            </p>
                            <p class="text-[10px] font-bold text-mute uppercase tracking-wider">Sisa</p>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center py-8">
                        <div class="w-16 h-16 rounded-full bg-canvas flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-mute opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-[16px] font-bold text-ink">Stok Produk Aman!</p>
                        <p class="text-sm text-mute mt-1">Belum ada barang yang hampir habis.</p>
                    </div>
                @endforelse
            </div>
            
            @if($lowStockProducts->count() > 0)
                <a href="{{ route('products.index') }}" wire:navigate class="mt-6 block w-full py-4 bg-canvas hover:bg-surface-soft border border-ash text-center text-[14px] font-bold text-ink rounded-[16px] transition-colors">
                    Kelola Produk
                </a>
            @endif
        </div>

    </div>

    <!-- ApexCharts Library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" data-navigate-once></script>
    <script>
        (function() {
            const chartEl = document.querySelector("#salesChart");
            if (!chartEl) return;
            
            // Cleanup existing chart to prevent duplicates on wire:navigate
            if (chartEl.__apexcharts_instance) {
                chartEl.__apexcharts_instance.destroy();
            }
            
            const renderChart = () => {
                if (typeof ApexCharts === 'undefined') {
                    setTimeout(renderChart, 50);
                    return;
                }
                
                const dates = {!! $chartDates !!};
                const sales = {!! $chartSales !!};

                const options = {
                    series: [{
                        name: 'Penjualan',
                        data: sales
                    }],
                    chart: {
                        type: 'line',
                        height: 320,
                        toolbar: { show: false },
                        fontFamily: 'Inter, sans-serif',
                        background: 'transparent',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                            animateGradually: { enabled: true, delay: 150 },
                            dynamicAnimation: { enabled: true, speed: 350 }
                        }
                    },
                    colors: ['#E60023'],
                    stroke: {
                        curve: 'smooth',
                        width: 4,
                        lineCap: 'round'
                    },
                    markers: {
                        size: 6,
                        colors: ['#ffffff'],
                        strokeColors: '#E60023',
                        strokeWidth: 3,
                        hover: { size: 8 }
                    },
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: dates,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: {
                            style: { 
                                colors: '#767676',
                                fontWeight: 600,
                                fontSize: '12px'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: { 
                                colors: '#767676',
                                fontWeight: 600,
                                fontSize: '12px'
                            },
                            formatter: (value) => {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                return 'Rp ' + value;
                            }
                        }
                    },
                    grid: {
                        borderColor: '#e2e8f0',
                        strokeDashArray: 0,
                        xaxis: { lines: { show: false } },
                        yaxis: { lines: { show: true } },
                        padding: { top: 0, right: 0, bottom: 0, left: 10 }
                    },
                    theme: { mode: 'light' },
                    tooltip: {
                        theme: 'light',
                        style: { fontSize: '14px', fontFamily: 'Inter, sans-serif' },
                        marker: { show: false },
                        y: {
                            formatter: function (val) {
                                return 'Rp ' + val.toLocaleString('id-ID')
                            }
                        }
                    }
                };

                const chart = new ApexCharts(chartEl, options);
                chartEl.__apexcharts_instance = chart;
                chart.render();
            };
            
            renderChart();
        })();
    </script>
</div>
