<?php
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public string $search = '';
    public string $filterCategory = '';

    public array $cart = [];
    public float $discount = 0;
    public float $taxRate = 11;
    public string $customerName = 'Pelanggan Umum';

    public bool $showPaymentModal = false;
    public string $paymentMethod = 'cash';
    public float $paidAmount = 0;
    
    public bool $showReceiptModal = false;
    public ?Sale $lastSale = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);
        
        if ($product->stock <= 0) {
            session()->flash('error', "Stok {$product->name} habis!");
            return;
        }

        $cartIndex = array_search($productId, array_column($this->cart, 'product_id'));
        
        if ($cartIndex !== false) {
            if ($this->cart[$cartIndex]['qty'] >= $product->stock) {
                session()->flash('error', "Stok tidak mencukupi!");
                return;
            }
            $this->cart[$cartIndex]['qty']++;
        } else {
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->sell_price,
                'qty' => 1,
                'max_qty' => $product->stock,
                'discount' => 0,
            ];
        }
    }

    public function updateQty(int $index, int $qty): void
    {
        if ($qty <= 0) {
            $this->removeFromCart($index);
            return;
        }
        
        if ($qty > $this->cart[$index]['max_qty']) {
             session()->flash('error', "Stok tidak mencukupi!");
             return;
        }

        $this->cart[$index]['qty'] = $qty;
    }

    public function removeFromCart(int $index): void
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart);
    }
    
    public function clearCart(): void
    {
        $this->cart = [];
        $this->discount = 0;
        $this->customerName = 'Pelanggan Umum';
    }

    public function getSubtotalProperty(): float
    {
        return array_reduce($this->cart, function ($carry, $item) {
            return $carry + (($item['price'] - $item['discount']) * $item['qty']);
        }, 0);
    }

    public function getTaxAmountProperty(): float
    {
        $taxable = $this->subtotal - $this->discount;
        return $taxable > 0 ? ($taxable * ($this->taxRate / 100)) : 0;
    }

    public function getGrandTotalProperty(): float
    {
        $total = ($this->subtotal - $this->discount) + $this->taxAmount;
        return max(0, $total);
    }
    
    public function getChangeAmountProperty(): float
    {
        if ($this->paymentMethod !== 'cash') return 0;
        return $this->paidAmount - $this->grandTotal;
    }

    public function openPayment(): void
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }
        
        $this->paidAmount = $this->grandTotal;
        $this->paymentMethod = 'cash';
        $this->showPaymentModal = true;
    }

    public function processPayment(): void
    {
        $this->validate([
            'paidAmount' => $this->paymentMethod === 'cash' ? 'required|numeric|min:' . $this->grandTotal : 'nullable',
            'customerName' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $sale = Sale::create([
                'invoice_no' => Sale::generateInvoiceNo(),
                'user_id' => auth()->id(),
                'customer_name' => $this->customerName ?: 'Pelanggan Umum',
                'total' => $this->subtotal,
                'discount' => $this->discount,
                'tax' => $this->taxAmount,
                'grand_total' => $this->grandTotal,
                'payment_method' => $this->paymentMethod,
                'paid_amount' => $this->paymentMethod === 'cash' ? $this->paidAmount : $this->grandTotal,
                'change_amount' => $this->paymentMethod === 'cash' ? ($this->paidAmount - $this->grandTotal) : 0,
                'status' => 'completed',
            ]);

            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'subtotal' => ($item['price'] - $item['discount']) * $item['qty'],
                ]);
            }

            DB::commit();
            
            $this->lastSale = $sale;
            $this->clearCart();
            $this->showPaymentModal = false;
            $this->showReceiptModal = true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function closeReceipt(): void
    {
        $this->showReceiptModal = false;
        $this->lastSale = null;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.pos.index', [
            'products' => Product::with(['category', 'brand'])
                ->when($this->search, fn($q) => $q->where(function($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('sku', 'like', "%{$this->search}%");
                }))
                ->when($this->filterCategory, fn($q) => $q->where('category_id', $this->filterCategory))
                ->latest()
                ->paginate(12),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}; ?>

<div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-8rem)]">
    
    {{-- ── Kiri: Keranjang / Pesanan (Cart) ─────────────────────────────────── --}}
    <div class="w-full lg:w-96 flex flex-col h-full bg-surface-card rounded-[24px] overflow-hidden shrink-0">
        {{-- Cart Header --}}
        <div class="p-4 border-b border-hairline flex justify-between items-center bg-canvas">
            <h3 class="font-bold text-ink flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                Pesanan Saat Ini
            </h3>
            @if(count($cart) > 0)
                <button wire:click="clearCart" class="text-xs font-semibold text-primary hover:text-opacity-70 transition-colors">
                    Kosongkan
                </button>
            @endif
        </div>
        
        {{-- Customer Name --}}
        <div class="px-4 py-3 border-b border-hairline bg-canvas">
            <input wire:model="customerName" type="text" placeholder="Nama Pelanggan (opsional)" 
                   class="w-full bg-transparent border-0 border-b border-hairline focus:ring-0 focus:border-ink px-0 py-1 text-sm text-ink placeholder-mute transition-colors outline-none">
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar bg-canvas">
            @forelse($cart as $index => $item)
                <div class="flex gap-3 p-3 bg-surface-soft rounded-[16px] group relative">
                    <div class="flex-1 min-w-0">
                        <div class="text-[14px] font-bold text-ink truncate pr-6">{{ $item['name'] }}</div>
                        <div class="text-xs text-mute mb-2">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                        
                        <div class="flex items-center gap-2">
                            {{-- Qty Control --}}
                            <div class="flex items-center bg-canvas border border-hairline rounded-[8px] overflow-hidden shrink-0">
                                <button wire:click="updateQty({{ $index }}, {{ $item['qty'] - 1 }})" class="w-7 h-7 flex items-center justify-center text-mute hover:bg-ash transition-colors font-bold">-</button>
                                <input type="number" wire:change="updateQty({{ $index }}, $event.target.value)" value="{{ $item['qty'] }}" class="w-10 h-7 border-0 text-center text-xs font-bold bg-transparent p-0 focus:ring-0 text-ink outline-none" min="1" max="{{ $item['max_qty'] }}">
                                <button wire:click="updateQty({{ $index }}, {{ $item['qty'] + 1 }})" class="w-7 h-7 flex items-center justify-center text-mute hover:bg-ash transition-colors font-bold">+</button>
                            </div>
                            
                            <div class="text-[14px] font-black text-ink ml-auto">
                                Rp {{ number_format(($item['price'] - $item['discount']) * $item['qty'], 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                    
                    {{-- Remove button --}}
                    <button wire:click="removeFromCart({{ $index }})" class="absolute top-2 right-2 p-1.5 text-mute hover:text-primary transition-colors opacity-0 group-hover:opacity-100 rounded-full hover:bg-[#ffeaea]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-mute text-center">
                    <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    <p class="text-sm font-semibold">Belum ada pesanan.</p>
                    <p class="text-xs mt-1 text-mute">Pilih produk di sebelah kanan.</p>
                </div>
            @endforelse
        </div>

        {{-- Summary --}}
        <div class="p-4 border-t border-hairline bg-surface-card space-y-3">
            <div class="flex items-center justify-between text-sm">
                <span class="text-mute font-medium">Diskon Keseluruhan</span>
                <input wire:model.live.debounce.500ms="discount" type="number" min="0" placeholder="Rp"
                       class="w-28 h-[36px] bg-canvas border border-ash rounded-[12px] text-right text-sm py-1 px-3 focus:border-ink outline-none transition-colors">
            </div>
            
            <div class="flex justify-between text-sm">
                <span class="text-mute font-medium">Subtotal</span>
                <span class="font-bold text-ink">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-mute font-medium">PPN (11%)</span>
                <span class="font-bold text-ink">Rp {{ number_format($this->taxAmount, 0, ',', '.') }}</span>
            </div>
            
            <hr class="border-hairline">
            
            <div class="flex justify-between items-end pb-2">
                <span class="text-xs text-mute font-bold uppercase tracking-wider">Total Tagihan</span>
                <span class="text-2xl font-black text-primary leading-none">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
            </div>

            <button wire:click="openPayment" 
                    class="btn-primary w-full py-4 text-[16px]"
                    @if(empty($cart)) disabled @endif>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Proses Pembayaran
            </button>
        </div>
    </div>


    {{-- ── Kanan: Daftar Produk ─────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col h-full bg-surface-card rounded-[24px] overflow-hidden">
        {{-- Header & Filters --}}
        <div class="p-4 border-b border-hairline flex flex-col sm:flex-row gap-3 bg-canvas">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                    <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau SKU produk..."
                       class="w-full h-[44px] pl-11 pr-4 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]">
            </div>
            <select wire:model.live="filterCategory" class="h-[44px] px-4 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000] min-w-[180px]">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Alerts --}}
        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="m-4 flex items-center justify-between gap-3 rounded-[16px] bg-[#ffeaea] border border-[#ffcaca] px-4 py-3 text-sm text-primary shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-semibold">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Grid Produk --}}
        <div class="flex-1 p-4 overflow-y-auto custom-scrollbar bg-canvas">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @forelse($products as $product)
                    <button wire:click="addToCart({{ $product->id }})" 
                            class="relative flex flex-col text-left bg-surface-card border border-ash rounded-[24px] p-4 hover:shadow-[0_0_0_2px_#111111] transition-all group overflow-hidden"
                            @if($product->stock <= 0) disabled @endif>
                        
                        {{-- Image Placeholder --}}
                        <div class="aspect-square bg-surface-soft rounded-[16px] mb-4 flex items-center justify-center text-mute group-hover:bg-[#ffeaea] group-hover:text-primary transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        
                        <div class="text-[12px] font-bold text-mute mb-1 line-clamp-1">{{ $product->category->name }}</div>
                        <div class="text-[14px] font-bold text-ink mb-2 line-clamp-2 leading-tight">{{ $product->name }}</div>
                        
                        <div class="mt-auto flex items-end justify-between pt-2">
                            <span class="text-[14px] font-black text-ink">Rp {{ number_format($product->sell_price, 0, ',', '.') }}</span>
                        </div>

                        {{-- Stock Badge di pojok kanan atas --}}
                        <div class="absolute top-4 right-4 text-[10px] px-2 py-1 rounded-[8px] font-bold {{ $product->stock > 0 ? 'bg-ink text-on-dark' : 'bg-primary text-on-dark' }}">
                            {{ $product->stock > 0 ? 'Stok: '.$product->stock : 'Habis' }}
                        </div>

                        {{-- Overlay out of stock --}}
                        @if($product->stock <= 0)
                            <div class="absolute inset-0 bg-canvas/60 backdrop-blur-[2px] flex items-center justify-center cursor-not-allowed z-10"></div>
                        @endif
                    </button>
                @empty
                    <div class="col-span-full py-16 flex flex-col items-center justify-center text-mute">
                        <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="text-[16px] font-bold text-ink mb-1">Produk tidak ditemukan</p>
                        <p class="text-sm">Coba sesuaikan filter atau kata kunci pencarian.</p>
                    </div>
                @endforelse
            </div>
        </div>
        
        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="p-4 border-t border-hairline bg-surface-card">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    {{-- ============================================================
         Payment Modal
         ============================================================ --}}
    <div x-data="{ show: @entangle('showPaymentModal') }"
         x-show="show"
         style="display: none;"
         wire:ignore.self
         @click.self="show = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        
        <div x-show="show"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-[480px] bg-canvas rounded-[32px] overflow-hidden shadow-[0_0_16px_rgba(0,0,0,0.1)] flex flex-col">
            
            <div class="px-8 py-6 border-b border-hairline flex justify-between items-center bg-canvas">
                <h3 class="text-heading-md text-ink">Detail Pembayaran</h3>
                <button @click="show = false" class="text-mute hover:text-ink transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-8 py-6 space-y-6">
                {{-- Total Bill --}}
                <div class="bg-surface-soft rounded-[24px] p-6 text-center">
                    <div class="text-[14px] font-bold text-mute mb-2">Total Tagihan</div>
                    <div class="text-[32px] font-black text-ink">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</div>
                </div>

                {{-- Payment Method --}}
                <div>
                    <label class="block text-[14px] font-bold text-ink mb-3">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="paymentMethod" value="cash" class="peer sr-only">
                            <div class="rounded-[16px] border border-ash px-4 py-4 flex flex-col items-center gap-2 hover:bg-surface-soft peer-checked:border-ink peer-checked:shadow-[inset_0_0_0_1px_#111111] transition-all bg-canvas">
                                <svg class="w-6 h-6 text-mute peer-checked:text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span class="text-sm font-bold text-ink">Tunai</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model.live="paymentMethod" value="transfer" class="peer sr-only">
                            <div class="rounded-[16px] border border-ash px-4 py-4 flex flex-col items-center gap-2 hover:bg-surface-soft peer-checked:border-ink peer-checked:shadow-[inset_0_0_0_1px_#111111] transition-all bg-canvas">
                                <svg class="w-6 h-6 text-mute peer-checked:text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                <span class="text-sm font-bold text-ink">Transfer/Qris</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Cash Amount --}}
                @if($paymentMethod === 'cash')
                <div x-data="{
                    quickAmounts: [
                        {{ ceil($this->grandTotal / 50000) * 50000 }},
                        {{ ceil($this->grandTotal / 100000) * 100000 }}
                    ]
                }">
                    <label class="block text-[14px] font-bold text-ink mb-2">Jumlah Uang Diterima</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <span class="text-mute font-bold text-[18px]">Rp</span>
                        </div>
                        <input wire:model.live.debounce.300ms="paidAmount" type="number" min="{{ $this->grandTotal }}"
                               class="w-full h-[52px] pl-12 pr-4 bg-canvas border border-ash rounded-[16px] text-[18px] font-bold text-ink focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000] outline-none"
                               placeholder="0">
                    </div>
                    @error('paidAmount') <span class="text-primary font-bold text-xs mt-2 block">{{ $message }}</span> @enderror
                    
                    {{-- Quick buttons --}}
                    <div class="flex gap-2 mt-3">
                        <button type="button" wire:click="$set('paidAmount', {{ $this->grandTotal }})" class="px-4 py-2 bg-surface-soft hover:bg-ash text-ink text-sm font-bold rounded-[12px] transition-colors">Uang Pas</button>
                        <template x-for="amount in quickAmounts" :key="amount">
                            <button type="button" @click="$wire.set('paidAmount', amount)" x-text="'Rp ' + amount.toLocaleString('id-ID')" class="px-4 py-2 bg-surface-soft hover:bg-ash text-ink text-sm font-bold rounded-[12px] transition-colors" x-show="amount > {{ $this->grandTotal }}"></button>
                        </template>
                    </div>

                    {{-- Change --}}
                    @if($this->changeAmount > 0)
                    <div class="mt-6 flex justify-between items-center p-4 bg-[#E6F4EA] border border-[#CEEAD6] rounded-[16px]">
                        <span class="text-[14px] font-bold text-[#137333]">Kembalian</span>
                        <span class="text-[20px] font-black text-[#137333]">Rp {{ number_format($this->changeAmount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <div class="px-8 py-6 bg-canvas border-t border-hairline flex gap-3">
                <button @click="show = false" type="button" class="btn-secondary flex-1">Batal</button>
                <button wire:click="processPayment" type="button" class="btn-primary flex-1" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="processPayment">Selesaikan</span>
                    <span wire:loading wire:target="processPayment">Memproses...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================
         Receipt / Success Modal
         ============================================================ --}}
    <div x-data="{ show: @entangle('showReceiptModal') }"
         x-show="show"
         style="display: none;"
         wire:ignore.self
         @click.self="closeReceipt"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        
        <div x-show="show"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-[400px] bg-canvas rounded-[32px] p-8 shadow-[0_0_16px_rgba(0,0,0,0.1)] flex flex-col items-center text-center">
            
            <div class="w-20 h-20 bg-[#E6F4EA] text-[#137333] rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
            </div>
            
            <h3 class="text-heading-lg text-ink mb-2">Transaksi Berhasil!</h3>
            @if($lastSale)
                <p class="text-body-md text-mute font-mono bg-surface-soft px-3 py-1 rounded-[8px] mb-8">{{ $lastSale->invoice_no }}</p>
            @endif
            
            <div class="w-full flex flex-col gap-3">
                <button class="btn-primary w-full justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Struk
                </button>
                <button wire:click="closeReceipt" class="btn-secondary w-full justify-center">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>
