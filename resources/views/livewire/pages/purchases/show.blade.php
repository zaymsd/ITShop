<?php
use App\Models\Purchase;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public Purchase $purchase;

    public function mount(Purchase $purchase): void
    {
        $this->purchase = $purchase->load(['supplier', 'user', 'purchaseItems.product']);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.purchases.show');
    }
}; ?>

<div>
    {{-- ── Header ────────────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-heading-lg text-ink flex items-center gap-3">
                <a href="{{ route('purchases.index') }}" wire:navigate class="w-10 h-10 rounded-[12px] bg-canvas border border-ash flex items-center justify-center text-mute hover:text-ink hover:border-ink transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                Detail Pembelian
            </h1>
            <p class="text-body-md text-mute mt-1 ml-[52px]">Informasi lengkap transaksi riwayat pembelian.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="btn-secondary text-sm h-10 px-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Bukti
            </button>
        </div>
    </div>

    {{-- ── Content ───────────────────────────────────────────────────────────── --}}
    <div class="bg-surface-card rounded-[24px] border border-hairline overflow-hidden print:shadow-none print:border-none print:bg-transparent">
        <div class="p-6 md:p-8">
            {{-- Print Header --}}
            <div class="hidden print:block text-center mb-8">
                <h2 class="text-2xl font-black text-ink">{{ config('app.name', 'IT Shop') }}</h2>
                <p class="text-sm text-mute">Bukti Transaksi Pembelian Stok (Restock)</p>
            </div>

            <div class="flex flex-col md:flex-row justify-between gap-6 mb-8 pb-8 border-b border-hairline">
                {{-- Info Left --}}
                <div class="space-y-4">
                    <div>
                        <div class="text-xs font-bold text-mute uppercase tracking-wider mb-1">Supplier</div>
                        <div class="font-black text-[18px] text-ink">{{ $purchase->supplier->name }}</div>
                        @if($purchase->supplier->phone)
                            <div class="text-sm text-mute mt-1">{{ $purchase->supplier->phone }}</div>
                        @endif
                        @if($purchase->supplier->address)
                            <div class="text-sm text-mute max-w-xs mt-1">{{ $purchase->supplier->address }}</div>
                        @endif
                    </div>
                </div>

                {{-- Info Right --}}
                <div class="space-y-4 md:text-right">
                    <div>
                        <div class="text-xs font-bold text-mute uppercase tracking-wider mb-1">Nomor PO</div>
                        <div class="font-black text-ink font-mono text-[20px] bg-canvas px-3 py-1 rounded-[8px] inline-block">{{ $purchase->invoice_no }}</div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-1 gap-4 md:gap-4 mt-4">
                        <div>
                            <div class="text-xs font-bold text-mute uppercase tracking-wider mb-1">Tanggal</div>
                            <div class="font-bold text-ink">{{ $purchase->purchase_date->format('d F Y') }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-mute uppercase tracking-wider mb-1">Petugas / Admin</div>
                            <div class="font-bold text-ink">{{ $purchase->user->name }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="overflow-x-auto mb-8 border border-hairline rounded-[16px] bg-canvas">
                <table class="w-full text-left text-sm">
                    <thead class="bg-surface-soft text-mute font-bold border-b border-hairline">
                        <tr>
                            <th class="px-4 py-3 uppercase tracking-wider text-xs">No</th>
                            <th class="px-4 py-3 uppercase tracking-wider text-xs">Nama Produk</th>
                            <th class="px-4 py-3 text-center uppercase tracking-wider text-xs">Qty</th>
                            <th class="px-4 py-3 text-right uppercase tracking-wider text-xs">Harga Satuan</th>
                            <th class="px-4 py-3 text-right uppercase tracking-wider text-xs">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline">
                        @foreach($purchase->purchaseItems as $index => $item)
                            <tr class="hover:bg-surface-soft transition-colors">
                                <td class="px-4 py-4 text-mute font-mono">{{ $index + 1 }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-ink">{{ $item->product->name }}</div>
                                    <div class="text-xs text-mute font-mono mt-1">{{ $item->product->sku }}</div>
                                </td>
                                <td class="px-4 py-4 text-center font-bold text-ink bg-surface-soft w-[80px]">{{ $item->qty }}</td>
                                <td class="px-4 py-4 text-right text-ink">Rp {{ number_format($item->buy_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-right font-black text-ink">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Total --}}
            <div class="flex justify-end">
                <div class="w-full md:w-80 bg-surface-soft rounded-[16px] p-6 border border-ash space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-mute font-medium">Total Item</span>
                        <span class="font-bold text-ink">{{ $purchase->purchaseItems->count() }} Jenis</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-mute font-medium">Total Qty</span>
                        <span class="font-bold text-ink">{{ $purchase->purchaseItems->sum('qty') }} Pcs</span>
                    </div>
                    <hr class="border-hairline my-3">
                    <div class="flex flex-col items-end">
                        <span class="text-xs font-bold uppercase tracking-wider text-mute mb-1">Grand Total</span>
                        <span class="text-[28px] font-black text-ink leading-none">Rp {{ number_format($purchase->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="hidden print:block mt-16 text-center text-sm text-mute font-mono">
                Dicetak pada: {{ now()->format('d M Y H:i:s') }}
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            body * { visibility: hidden; }
            .print\:block, .print\:block * { visibility: visible; }
            .bg-canvas, .bg-surface-card, .bg-surface-soft { background-color: white !important; }
            .border-hairline, .border-ash { border-color: #e2e8f0 !important; }
            .text-ink { color: #1e293b !important; }
            .text-mute { color: #64748b !important; }
            .text-primary { color: #000000 !important; }
            .print\:shadow-none { box-shadow: none !important; }
            .print\:border-none { border: none !important; }
            .print\:bg-transparent { background: transparent !important; }
            .rounded-[24px], .rounded-[16px] { border-radius: 0 !important; }
            
            /* The main container to print */
            .bg-surface-card {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .bg-surface-card * {
                visibility: visible;
            }
        }
    </style>
</div>
