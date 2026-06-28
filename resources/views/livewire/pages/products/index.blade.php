<?php
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public string $search         = '';
    public string $filterCategory = '';

    public bool $showDeleteModal = false;
    public ?int $deleteId   = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterCategory(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId   = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            Product::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Produk berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deleteId   = null;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.products.index', [
            'products' => Product::with(['category', 'brand', 'primaryImage'])
                ->when($this->search, fn ($q) => $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('sku', 'like', "%{$this->search}%");
                }))
                ->when($this->filterCategory, fn ($q) => $q->where('category_id', $this->filterCategory))
                ->latest()
                ->paginate(10),
            'categories' => Category::orderBy('name')->get(),
            'brands'     => Brand::orderBy('name')->get(),
            'suppliers'  => Supplier::orderBy('name')->get(),
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
            <h1 class="text-heading-lg text-ink">Produk</h1>
            <p class="text-body-md text-mute">Kelola inventaris produk</p>
        </div>
    </div>

    {{-- ── Search & Filter Bar ───────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1 w-full min-w-0">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama produk, SKU..."
                   class="w-full h-[44px] pl-11 pr-4 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
        </div>
        <select wire:model.live="filterCategory"
                class="w-full md:w-[240px] shrink-0 h-[44px] px-4 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- ── Table Card ────────────────────────────────────────────────────────── --}}
    <div class="bg-surface-card rounded-[16px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-soft border-b border-hairline">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider w-12">#</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Nama Produk</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Kategori & Brand</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-mute uppercase tracking-wider">Harga Jual</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-mute uppercase tracking-wider">Stok</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-mute uppercase tracking-wider w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse ($products as $product)
                        <tr class="hover:bg-surface-soft transition-colors duration-150">
                            <td class="px-6 py-4 text-mute font-mono text-xs">
                                {{ $products->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-[8px] bg-canvas border border-ash overflow-hidden shrink-0 flex items-center justify-center">
                                        @if($product->primaryImage)
                                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-ink">
                                            {{ $product->name }}
                                        </div>
                                        <div class="text-xs text-mute mt-1 font-mono bg-canvas px-1.5 py-0.5 rounded-[4px] inline-block">
                                            {{ $product->sku }}
                                        </div>
                                        @if($product->barcode)
                                            <div class="text-[10px] text-mute mt-0.5">{{ $product->barcode }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-ink text-sm">
                                <div>{{ $product->category?->name ?? '—' }}</div>
                                <div class="text-xs text-mute mt-0.5">{{ $product->brand?->name ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-ink text-sm">
                                    Rp {{ number_format($product->sell_price, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="font-bold text-[16px] {{ $product->stock <= $product->min_stock ? 'text-primary' : 'text-[var(--color-success-text)]' }}">
                                        {{ $product->stock }}
                                    </span>
                                    @if ($product->stock <= $product->min_stock)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-[8px] text-[10px] font-bold bg-[var(--color-danger-bg)] text-primary">
                                            Stok Tipis
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('products.edit', $product->id) }}" wire:navigate
                                       title="Edit"
                                       class="w-8 h-8 rounded-[8px] flex items-center justify-center text-mute hover:bg-canvas hover:text-ink transition-colors border border-transparent hover:border-ash">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button wire:click="confirmDelete({{ $product->id }})"
                                            title="Hapus"
                                            class="w-8 h-8 rounded-[8px] flex items-center justify-center text-mute hover:bg-[var(--color-danger-bg)] hover:text-primary transition-colors border border-transparent hover:border-[var(--color-danger-border)]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2 text-mute">
                                    <svg class="w-12 h-12 opacity-20 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <p class="text-sm font-semibold">
                                        @if ($search || $filterCategory)
                                            Tidak ada produk yang cocok dengan filter Anda.
                                        @else
                                            Belum ada produk.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="px-6 py-4 border-t border-hairline bg-canvas">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    {{-- ── Floating Action Button (FAB) ──────────────────────────────────────── --}}
    <a href="{{ route('products.create') }}" wire:navigate
       class="fixed bottom-8 right-8 w-14 h-14 rounded-full bg-primary text-on-dark flex items-center justify-center shadow-[0_4px_16px_rgba(230,0,35,0.4)] hover:scale-105 transition-transform z-40"
       title="Tambah Produk">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
    </a>

    {{-- ── Delete Confirm Modal ──────────────────────────────────────────────── --}}
    <div x-data="{ open: @entangle('showDeleteModal') }"
         x-show="open"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none;"
         wire:ignore.self
         @click.self="open = false; $wire.showDeleteModal = false">

        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-[400px] bg-canvas rounded-[32px] p-8 shadow-[0_0_16px_rgba(0,0,0,0.1)] flex flex-col items-center text-center">

            <div class="w-16 h-16 rounded-full bg-[var(--color-danger-bg)] flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            
            <h3 class="text-heading-lg text-ink mb-2">Hapus Produk?</h3>
            <p class="text-body-md text-body mb-8">
                Tindakan ini tidak dapat dibatalkan. Produk akan dihapus permanen.
            </p>
            
            <div class="flex w-full gap-3">
                <button @click="open = false; $wire.showDeleteModal = false" class="btn-secondary flex-1">
                    Batal
                </button>
                <button wire:click="delete" class="btn-primary flex-1">
                    <span wire:loading.remove wire:target="delete">Ya, Hapus</span>
                    <span wire:loading wire:target="delete">Menghapus...</span>
                </button>
            </div>
        </div>
    </div>
</div>
