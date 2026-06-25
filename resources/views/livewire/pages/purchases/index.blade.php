<?php
use App\Models\Purchase;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.purchases.index', [
            'purchases' => Purchase::with(['supplier', 'user'])
                ->when($this->search, fn ($q) => $q->where('invoice_no', 'like', "%{$this->search}%")
                    ->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                )
                ->latest()
                ->paginate(10),
        ]);
    }
}; ?>

<div class="relative min-h-[80vh]">
    {{-- ── Flash Messages ────────────────────────────────────────────────────── --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-6 flex items-center justify-between gap-3 rounded-[16px] bg-[var(--color-success-bg)] border border-[var(--color-success-border)] px-4 py-3 text-sm text-[var(--color-success-text)] shadow-sm">
            <div class="flex items-center gap-2">
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-[var(--color-success-text)] hover:opacity-70">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- ── Page Header ───────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-heading-lg text-ink">Pembelian (Restock)</h1>
            <p class="text-body-md text-mute">Kelola data riwayat pembelian stok barang</p>
        </div>
    </div>

    {{-- ── Search Bar ────────────────────────────────────────────────────────── --}}
    <div class="mb-6 relative w-full md:max-w-2xl">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
            <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari No. PO atau Supplier..."
               class="w-full h-[44px] pl-11 pr-4 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
    </div>

    {{-- ── Table Card ────────────────────────────────────────────────────────── --}}
    <div class="bg-surface-card rounded-[16px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-soft border-b border-hairline">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">No. PO</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Petugas</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-mute uppercase tracking-wider">Total (Rp)</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-mute uppercase tracking-wider w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-surface-soft transition-colors duration-150 group">
                            <td class="px-6 py-4 text-ink font-semibold">
                                {{ $purchase->purchase_date->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-mono text-mute">
                                <span class="bg-canvas px-2 py-1 rounded-[8px]">{{ $purchase->invoice_no }}</span>
                            </td>
                            <td class="px-6 py-4 text-ink">
                                {{ $purchase->supplier->name }}
                            </td>
                            <td class="px-6 py-4 text-ink flex items-center gap-2">
                                <div class="w-6 h-6 rounded-[6px] bg-ink text-on-dark flex items-center justify-center text-[10px] font-bold">
                                    {{ strtoupper(substr($purchase->user->name, 0, 1)) }}
                                </div>
                                {{ $purchase->user->name }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-ink">
                                {{ number_format($purchase->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('purchases.show', $purchase->id) }}" wire:navigate 
                                   class="w-8 h-8 rounded-[8px] flex items-center justify-center mx-auto text-mute hover:bg-canvas hover:text-ink transition-colors border border-transparent hover:border-ash" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-mute">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="font-bold">Belum ada data pembelian.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($purchases->hasPages())
        <div class="px-6 py-4 border-t border-hairline bg-canvas">
            {{ $purchases->links() }}
        </div>
        @endif
    </div>

    {{-- ── Floating Action Button (FAB) ──────────────────────────────────────── --}}
    <a href="{{ route('purchases.create') }}" wire:navigate
       class="fixed bottom-8 right-8 w-14 h-14 rounded-full bg-primary text-on-dark flex items-center justify-center shadow-[0_4px_16px_rgba(230,0,35,0.4)] hover:scale-105 transition-transform z-40"
       title="Buat Pembelian Baru">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
    </a>
</div>
