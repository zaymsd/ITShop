<?php

use App\Models\Sale;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public string $startDate = '';
    public string $endDate = '';

    public function mount(): void
    {
        $this->startDate = today()->startOfMonth()->format('Y-m-d');
        $this->endDate = today()->format('Y-m-d');
    }

    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(new SalesExport($this->startDate, $this->endDate), 'Laporan_Penjualan_'.date('Ymd').'.xlsx');
    }


    public function render(): \Illuminate\View\View
    {
        $salesQuery = Sale::with(['user', 'saleItems'])
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->latest();

        $summary = [
            'total_transactions' => (clone $salesQuery)->count(),
            'total_revenue' => (clone $salesQuery)->sum('grand_total'),
            'total_tax' => (clone $salesQuery)->sum('tax'),
        ];

        return view('livewire.pages.reports.sales', [
            'sales' => $salesQuery->paginate(15),
            'summary' => $summary,
        ]);
    }
}; ?>

<div class="relative min-h-[80vh]">
    {{-- ── Page Header ───────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-heading-lg text-ink flex items-center gap-3">
                <div class="w-10 h-10 rounded-[12px] bg-primary/10 flex items-center justify-center text-primary">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                Laporan Penjualan
            </h1>
            <p class="text-body-md text-mute mt-1">Pantau dan ekspor data transaksi penjualan toko.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('reports.sales.pdf', ['start' => $startDate, 'end' => $endDate]) }}" target="_blank" class="btn-secondary px-4 py-[11px] text-[14px] flex items-center gap-2">
                <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Preview PDF
            </a>
            <button wire:click="exportExcel" wire:loading.attr="disabled" class="btn-secondary px-4 py-[11px] text-[14px] flex items-center gap-2">
                <svg class="w-5 h-5 text-[#137333]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export Excel
            </button>
        </div>
    </div>

    {{-- ── Summary Cards ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-surface-card rounded-[32px] p-6 border border-hairline hover:shadow-[0_0_0_2px_#111111] transition-all duration-200 group flex flex-col justify-between">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-[16px] bg-surface-soft group-hover:bg-[#ffeaea] transition-colors flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-mute group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <span class="text-xs font-bold text-mute uppercase tracking-wider">Total Transaksi</span>
            </div>
            <div class="text-[36px] font-black text-ink leading-none">{{ number_format($summary['total_transactions']) }}</div>
        </div>
        
        <div class="bg-surface-card rounded-[32px] p-6 border border-hairline hover:shadow-[0_0_0_2px_#111111] transition-all duration-200 group flex flex-col justify-between">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-[16px] bg-surface-soft group-hover:bg-[#ffeaea] transition-colors flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-mute group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-mute uppercase tracking-wider">Total Pendapatan</span>
            </div>
            <div class="text-[28px] font-black text-ink leading-none mt-2 truncate" title="Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}">
                Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}
            </div>
        </div>
        
        <div class="bg-surface-card rounded-[32px] p-6 border border-hairline hover:shadow-[0_0_0_2px_#111111] transition-all duration-200 group flex flex-col justify-between">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-[16px] bg-surface-soft group-hover:bg-[#ffeaea] transition-colors flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-mute group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <span class="text-xs font-bold text-mute uppercase tracking-wider">Total PPN (11%)</span>
            </div>
            <div class="text-[28px] font-black text-ink leading-none mt-2 truncate" title="Rp {{ number_format($summary['total_tax'], 0, ',', '.') }}">
                Rp {{ number_format($summary['total_tax'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- ── Main Table Card ───────────────────────────────────────────────────── --}}
    <div class="bg-surface-card rounded-[24px] border border-hairline overflow-hidden">
        {{-- Toolbar Filter --}}
        <div class="p-6 border-b border-hairline flex flex-col md:flex-row gap-4 md:items-center justify-between">
            <div class="flex items-center gap-2 text-[14px] font-bold text-ink">
                <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Filter Periode
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <input wire:model.live="startDate" type="date" class="flex-1 md:w-[180px] h-[44px] px-4 py-[11px] bg-canvas text-ink text-[14px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]">
                <span class="text-mute font-bold text-[14px]">s/d</span>
                <input wire:model.live="endDate" type="date" class="flex-1 md:w-[180px] h-[44px] px-4 py-[11px] bg-canvas text-ink text-[14px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]">
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-surface-soft border-b border-hairline">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Tanggal & Waktu</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">No. Invoice</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Kasir</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-mute uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-mute uppercase tracking-wider">Total (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-surface-soft transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="font-bold text-ink">{{ $sale->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-mute font-mono mt-0.5">{{ $sale->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-[12px] bg-canvas px-2 py-1 rounded-[8px] text-mute border border-ash">
                                    {{ $sale->invoice_no }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-ink font-semibold">
                                {{ $sale->customer_name }}
                            </td>
                            <td class="px-6 py-4 text-ink flex items-center gap-2 mt-2">
                                <div class="w-6 h-6 rounded-[6px] bg-ink text-on-dark flex items-center justify-center text-[10px] font-bold">
                                    {{ strtoupper(substr($sale->user->name ?? '?', 0, 1)) }}
                                </div>
                                {{ $sale->user->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($sale->payment_method === 'cash')
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-[8px] text-[10px] font-black bg-[#E6F4EA] text-[#137333] uppercase tracking-wider">
                                        Tunai
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center px-2 py-1 rounded-[8px] text-[10px] font-black bg-[#FFF3E0] text-[#E65100] uppercase tracking-wider">
                                        Transfer
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-black text-ink text-[16px]">
                                {{ number_format($sale->grand_total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-mute">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-sm font-bold">Belum ada data penjualan untuk periode ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($sales->hasPages())
        <div class="px-6 py-4 border-t border-hairline bg-canvas">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
