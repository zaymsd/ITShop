<?php
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

new #[Layout('layouts.app')] class extends Component {
    
    public string $purchaseDate;
    public string $supplierId = '';
    
    public string $searchProduct = '';
    public array $searchResults = [];
    
    public array $items = [];

    public bool $showSaveModal = false;

    public function mount(): void
    {
        $this->purchaseDate = date('Y-m-d');
    }

    public function updatedSearchProduct(): void
    {
        if (strlen($this->searchProduct) > 1) {
            $this->searchResults = Product::where('name', 'like', "%{$this->searchProduct}%")
                ->orWhere('sku', 'like', "%{$this->searchProduct}%")
                ->take(5)
                ->get()
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function addProduct(int $productId): void
    {
        $product = Product::findOrFail($productId);
        
        $index = array_search($productId, array_column($this->items, 'product_id'));
        if ($index !== false) {
            $this->items[$index]['qty']++;
        } else {
            $this->items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'qty' => 1,
                'buy_price' => $product->buy_price,
            ];
        }
        
        $this->searchProduct = '';
        $this->searchResults = [];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function getTotalProperty(): float
    {
        return array_reduce($this->items, function ($carry, $item) {
            return $carry + ($item['qty'] * $item['buy_price']);
        }, 0);
    }

    public function confirmSave(): void
    {
        $this->validate([
            'supplierId' => 'required|exists:suppliers,id',
            'purchaseDate' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.buy_price' => 'required|numeric|min:0',
        ], [
            'supplierId.required' => 'Silakan pilih supplier.',
            'items.required' => 'Keranjang pembelian tidak boleh kosong.',
            'items.min' => 'Keranjang pembelian tidak boleh kosong.',
        ]);
        $this->showSaveModal = true;
    }

    public function save(): void
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::create([
                'invoice_no' => Purchase::generateInvoiceNo(),
                'user_id' => auth()->id(),
                'supplier_id' => $this->supplierId,
                'purchase_date' => $this->purchaseDate,
                'total' => $this->total,
            ]);

            foreach ($this->items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'buy_price' => $item['buy_price'],
                    'subtotal' => $item['qty'] * $item['buy_price'],
                ]);
            }

            DB::commit();
            session()->flash('success', 'Transaksi pembelian berhasil disimpan. Stok produk otomatis bertambah.');
            $this->redirectRoute('purchases.index', navigate: true);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.purchases.create', [
            'suppliers' => Supplier::orderBy('name')->get(),
        ]);
    }
}; ?>

<div x-data="{ openModal: @entangle('showSaveModal') }">
    {{-- ── Header ────────────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-heading-lg text-ink flex items-center gap-3">
                <a href="{{ route('purchases.index') }}" wire:navigate class="w-10 h-10 rounded-[12px] bg-canvas border border-ash flex items-center justify-center text-mute hover:text-ink hover:border-ink transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                Buat Pembelian Baru
            </h1>
            <p class="text-body-md text-mute mt-1 ml-[52px]">Catat pembelian barang dari supplier untuk menambah stok otomatis.</p>
        </div>
    </div>

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="mb-6 flex items-center justify-between gap-3 rounded-[16px] bg-[#ffeaea] border border-[#ffcaca] px-4 py-3 text-sm text-primary shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-primary hover:opacity-70">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <div class="flex flex-col xl:flex-row gap-6 items-start">
        {{-- ── Kiri: Form & Daftar Barang ───────────────────────────────────────── --}}
        <div class="flex-1 w-full space-y-6">
            {{-- Info Pembelian Card --}}
            <div class="bg-surface-card rounded-[24px] border border-hairline p-6">
                <h3 class="text-sm font-bold text-ink uppercase tracking-wider mb-4 border-b border-hairline pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Informasi Pembelian
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[14px] font-bold text-ink mb-1.5">Supplier <span class="text-primary">*</span></label>
                        <select wire:model="supplierId" class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplierId') <span class="text-primary text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-[14px] font-bold text-ink mb-1.5">Tanggal <span class="text-primary">*</span></label>
                        <input wire:model="purchaseDate" type="date" class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]">
                        @error('purchaseDate') <span class="text-primary text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Detail Barang Card --}}
            <div class="bg-surface-card rounded-[24px] border border-hairline p-6">
                <h3 class="text-sm font-bold text-ink uppercase tracking-wider mb-4 border-b border-hairline pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Detail Barang
                </h3>

                {{-- Product Search --}}
                <div class="relative mb-6">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input wire:model.live.debounce.300ms="searchProduct" type="text" placeholder="Ketik nama produk atau SKU untuk menambah..."
                           class="w-full h-[48px] pl-11 pr-4 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]">
                    
                    @if(!empty($searchResults))
                        <div class="absolute z-10 mt-2 w-full bg-canvas border border-ash rounded-[16px] shadow-[0_4px_16px_rgba(0,0,0,0.1)] overflow-hidden">
                            <ul class="divide-y divide-hairline">
                                @foreach($searchResults as $product)
                                    <li>
                                        <button wire:click="addProduct({{ $product['id'] }})" type="button" class="w-full text-left px-4 py-3 hover:bg-surface-soft flex justify-between items-center transition-colors">
                                            <div>
                                                <div class="text-[14px] font-bold text-ink">{{ $product['name'] }}</div>
                                                <div class="text-xs text-mute font-mono">{{ $product['sku'] }}</div>
                                            </div>
                                            <div class="text-sm font-bold {{ $product['stock'] > 0 ? 'text-ink' : 'text-primary' }}">
                                                Stok: {{ $product['stock'] }}
                                            </div>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                @error('items') <div class="p-3 bg-[#ffeaea] border border-[#ffcaca] text-primary text-sm font-bold rounded-[16px] mb-4">{{ $message }}</div> @enderror

                {{-- Cart Table --}}
                <div class="overflow-x-auto border border-hairline rounded-[16px] bg-canvas">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-surface-soft text-mute font-bold border-b border-hairline">
                            <tr>
                                <th class="px-4 py-3 uppercase tracking-wider text-xs">Produk</th>
                                <th class="px-4 py-3 w-36 text-center uppercase tracking-wider text-xs">Harga Beli</th>
                                <th class="px-4 py-3 w-28 text-center uppercase tracking-wider text-xs">Qty</th>
                                <th class="px-4 py-3 w-36 text-right uppercase tracking-wider text-xs">Subtotal</th>
                                <th class="px-4 py-3 w-16 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline">
                            @forelse($items as $index => $item)
                                <tr class="hover:bg-surface-soft transition-colors group">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-ink">{{ $item['name'] }}</div>
                                        <div class="text-xs text-mute font-mono">{{ $item['sku'] }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input wire:model.live.debounce.300ms="items.{{ $index }}.buy_price" type="number" min="0" class="w-full h-[36px] text-right bg-canvas border border-ash rounded-[12px] text-sm px-3 focus:border-ink outline-none">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input wire:model.live.debounce.300ms="items.{{ $index }}.qty" type="number" min="1" class="w-full h-[36px] text-center bg-canvas border border-ash rounded-[12px] text-sm px-3 focus:border-ink outline-none">
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-ink">
                                        Rp {{ number_format($item['qty'] * $item['buy_price'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="removeItem({{ $index }})" type="button" class="p-1.5 rounded-full text-mute hover:bg-[#ffeaea] hover:text-primary transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center text-mute">
                                        <p class="font-bold">Belum ada barang.</p>
                                        <p class="text-xs mt-1">Gunakan kotak pencarian di atas untuk menambahkan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Kanan: Summary Sidebar ───────────────────────────────────────────── --}}
        <div class="w-full xl:w-80 shrink-0">
            <div class="bg-surface-card rounded-[24px] border border-hairline p-6 sticky top-24">
                <h3 class="text-sm font-bold text-ink uppercase tracking-wider mb-4 border-b border-hairline pb-3">Ringkasan</h3>
                
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-mute font-medium">Total Item</span>
                        <span class="font-bold text-ink">{{ count($items) }} Jenis</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-mute font-medium">Total Qty</span>
                        <span class="font-bold text-ink">{{ array_sum(array_column($items, 'qty')) }} Pcs</span>
                    </div>
                </div>

                <div class="bg-surface-soft rounded-[16px] p-4 border border-ash mb-6 text-center">
                    <div class="text-xs font-bold text-mute mb-1 uppercase tracking-wider">Grand Total</div>
                    <div class="text-2xl font-black text-ink truncate">
                        Rp {{ number_format($this->total, 0, ',', '.') }}
                    </div>
                </div>

                <button wire:click="confirmSave" type="button" class="btn-primary w-full py-4 text-[16px]">
                    Simpan Transaksi
                </button>
                <a href="{{ route('purchases.index') }}" wire:navigate class="btn-secondary w-full py-3 mt-3 text-[14px]">
                    Batal
                </a>
            </div>
        </div>
    </div>

    {{-- ── Save Confirm Modal ──────────────────────────────────────────────── --}}
    <div x-show="openModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none;"
         wire:ignore.self
         @click.self="openModal = false; $wire.showSaveModal = false">

        <div x-show="openModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-[400px] bg-canvas rounded-[32px] p-8 shadow-[0_0_16px_rgba(0,0,0,0.1)] flex flex-col items-center text-center">

            <div class="w-16 h-16 rounded-full bg-surface-soft flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            </div>
            
            <h3 class="text-heading-lg text-ink mb-2">Simpan Pembelian?</h3>
            <p class="text-body-md text-body mb-8">
                Transaksi akan disimpan dan stok akan otomatis bertambah ke master data produk.
            </p>
            
            <div class="flex w-full gap-3">
                <button @click="openModal = false; $wire.showSaveModal = false" type="button" class="btn-secondary flex-1">
                    Batal
                </button>
                <button wire:click="save" type="button" class="btn-primary flex-1">
                    <span wire:loading.remove wire:target="save">Ya, Simpan</span>
                    <span wire:loading wire:target="save">Memproses...</span>
                </button>
            </div>
        </div>
    </div>
</div>
