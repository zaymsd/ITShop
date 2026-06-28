<?php
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductImage;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    public int $id;
    public string $name        = '';
    public string $sku         = '';
    public string $barcode     = '';
    public string $buy_price   = '';
    public string $sell_price  = '';
    public string $stock       = '';
    public string $min_stock   = '';
    public string $specs       = '';
    public string $category_id = '';
    public string $brand_id    = '';
    public string $supplier_id = '';

    public $photos = [];
    public $existingImages = [];

    public bool $showUpdateModal = false;

    public function mount(int $id): void
    {
        $product = Product::findOrFail($id);
        $this->id          = $product->id;
        $this->name        = $product->name;
        $this->sku         = $product->sku;
        $this->barcode     = $product->barcode ?? '';
        $this->buy_price   = (string)$product->buy_price;
        $this->sell_price  = (string)$product->sell_price;
        $this->stock       = (string)$product->stock;
        $this->min_stock   = (string)$product->min_stock;
        $this->specs       = $product->specs ?? '';
        $this->category_id = (string)$product->category_id;
        $this->brand_id    = (string)$product->brand_id;
        $this->supplier_id = (string)($product->supplier_id ?? '');
        $this->existingImages = $product->images()->get();
    }

    public function deleteImage(int $imageId): void
    {
        $image = ProductImage::find($imageId);
        if ($image && $image->product_id === $this->id) {
            Storage::disk('public')->delete($image->image_path);
            $wasPrimary = $image->is_primary;
            $image->delete();
            
            // If deleted image was primary, make the first available image primary
            if ($wasPrimary) {
                $firstAvailable = ProductImage::where('product_id', $this->id)->first();
                if ($firstAvailable) {
                    $firstAvailable->update(['is_primary' => true]);
                }
            }
            
            $this->existingImages = ProductImage::where('product_id', $this->id)->get();
        }
    }

    public function confirmUpdate(): void
    {
        $this->validate([
            'name'        => ['required', 'string', 'max:200'],
            'sku'         => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($this->id)],
            'barcode'     => ['nullable', 'string', 'max:50'],
            'buy_price'   => ['required', 'numeric', 'min:0'],
            'sell_price'  => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'min_stock'   => ['required', 'integer', 'min:0'],
            'specs'       => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'brand_id'    => ['required', 'exists:brands,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'photos.*'    => ['image', 'max:5120'], // Max 5MB per image
        ]);
        $this->showUpdateModal = true;
    }

    public function save(): void
    {
        $product = Product::findOrFail($this->id);
        $product->update([
            'name'        => $this->name,
            'sku'         => strtoupper(trim($this->sku)),
            'barcode'     => $this->barcode ?: null,
            'buy_price'   => $this->buy_price,
            'sell_price'  => $this->sell_price,
            'stock'       => $this->stock,
            'min_stock'   => $this->min_stock,
            'specs'       => $this->specs ?: null,
            'category_id' => $this->category_id,
            'brand_id'    => $this->brand_id,
            'supplier_id' => $this->supplier_id ?: null,
        ]);
        
        if (!empty($this->photos)) {
            $hasPrimary = ProductImage::where('product_id', $this->id)->where('is_primary', true)->exists();
            
            foreach ($this->photos as $index => $photo) {
                $path = $photo->store('products', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => !$hasPrimary && $index === 0,
                ]);
            }
        }
        
        session()->flash('success', 'Produk berhasil diperbarui.');
        $this->redirect(route('products.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'brands'     => Brand::orderBy('name')->get(),
            'suppliers'  => Supplier::orderBy('name')->get(),
        ];
    }
}; ?>

<div x-data="{ openModal: @entangle('showUpdateModal') }">
    {{-- ── Page Header ───────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h1 class="text-heading-lg text-ink">Edit Produk</h1>
        <p class="text-body-md text-mute">Perbarui informasi produk</p>
    </div>

    {{-- ── Form Card ──────────────────────────────────────────────────────────── --}}
    <div class="max-w-4xl bg-surface-card rounded-[24px] p-8 border border-hairline">
        <form wire:submit.prevent="confirmUpdate" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-6">
                    {{-- Name --}}
                    <div>
                        <label class="block text-[14px] font-semibold text-ink mb-1.5">
                            Nama Produk <span class="text-primary">*</span>
                        </label>
                        <input wire:model="name" type="text" placeholder="Contoh: Laptop ASUS..." autofocus
                               class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                        @error('name') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                    </div>

                    {{-- SKU & Barcode --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-semibold text-ink mb-1.5">
                                SKU <span class="text-primary">*</span>
                            </label>
                            <input wire:model="sku" type="text" placeholder="Contoh: AS-001"
                                   class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000] font-mono"/>
                            @error('sku') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[14px] font-semibold text-ink mb-1.5">
                                Barcode
                            </label>
                            <input wire:model="barcode" type="text" placeholder="Opsional"
                                   class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000] font-mono"/>
                            @error('barcode') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Category & Brand --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-semibold text-ink mb-1.5">
                                Kategori <span class="text-primary">*</span>
                            </label>
                            <select wire:model="category_id"
                                    class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]">
                                <option value="">-- Pilih --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[14px] font-semibold text-ink mb-1.5">
                                Brand <span class="text-primary">*</span>
                            </label>
                            <select wire:model="brand_id"
                                    class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]">
                                <option value="">-- Pilih --</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-6">
                    {{-- Buy Price & Sell Price --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-semibold text-ink mb-1.5">
                                Harga Beli <span class="text-primary">*</span>
                            </label>
                            <input wire:model="buy_price" type="number" min="0" placeholder="0"
                                   class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                            @error('buy_price') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[14px] font-semibold text-ink mb-1.5">
                                Harga Jual <span class="text-primary">*</span>
                            </label>
                            <input wire:model="sell_price" type="number" min="0" placeholder="0"
                                   class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                            @error('sell_price') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Stock & Min Stock --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-semibold text-ink mb-1.5">
                                Stok <span class="text-primary">*</span>
                            </label>
                            <input wire:model="stock" type="number" min="0" placeholder="0"
                                   class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                            @error('stock') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[14px] font-semibold text-ink mb-1.5">
                                Stok Min <span class="text-primary">*</span>
                            </label>
                            <input wire:model="min_stock" type="number" min="0" placeholder="5"
                                   class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                            @error('min_stock') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Supplier --}}
                    <div>
                        <label class="block text-[14px] font-semibold text-ink mb-1.5">
                            Supplier
                        </label>
                        <select wire:model="supplier_id"
                                class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]">
                            <option value="">-- Opsional --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Specs (Full Width) --}}
            <div>
                <label class="block text-[14px] font-semibold text-ink mb-1.5">
                    Spesifikasi
                </label>
                <textarea wire:model="specs" rows="3" placeholder="Contoh: Processor Intel i5..."
                          class="w-full px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000] resize-none"></textarea>
                @error('specs') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
            </div>

            {{-- Photos --}}
            <div>
                <label class="block text-[14px] font-semibold text-ink mb-1.5">
                    Foto Produk
                </label>
                
                @if (count($existingImages) > 0)
                    <div class="mb-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach ($existingImages as $image)
                            <div class="relative aspect-square bg-canvas rounded-[12px] border border-ash overflow-hidden group">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-full object-cover">
                                @if($image->is_primary)
                                    <div class="absolute top-1 left-1 bg-ink text-canvas text-[10px] font-bold px-1.5 py-0.5 rounded-[4px]">Utama</div>
                                @endif
                                <button type="button" wire:click="deleteImage({{ $image->id }})" wire:confirm="Yakin ingin menghapus foto ini?"
                                        class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <input wire:model="photos" type="file" multiple accept="image/*"
                       class="w-full px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                <p class="mt-1 text-xs text-mute">Tambahkan foto baru (Bisa pilih lebih dari satu)</p>
                @error('photos.*') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
                
                @if ($photos)
                    <div class="mt-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 opacity-50">
                        @foreach ($photos as $photo)
                            <div class="aspect-square bg-canvas rounded-[12px] border border-ash overflow-hidden relative">
                                <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 flex items-center justify-center text-white bg-black/30 text-[10px] font-bold">New</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-hairline">
                <a href="{{ route('products.index') }}" wire:navigate class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- ── Update Confirm Modal ──────────────────────────────────────────────── --}}
    <div x-show="openModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
         style="display:none;"
         wire:ignore.self
         @click.self="openModal = false; $wire.showUpdateModal = false">

        <div x-show="openModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-[400px] bg-canvas rounded-[32px] p-8 shadow-[0_0_16px_rgba(0,0,0,0.1)] flex flex-col items-center text-center">

            <div class="w-16 h-16 rounded-full bg-surface-soft flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            
            <h3 class="text-heading-lg text-ink mb-2">Simpan Perubahan?</h3>
            <p class="text-body-md text-body mb-8">
                Apakah Anda yakin ingin menyimpan perubahan data ini?
            </p>
            
            <div class="flex w-full gap-3">
                <button @click="openModal = false; $wire.showUpdateModal = false" class="btn-secondary flex-1">
                    Batal
                </button>
                <button wire:click="save" class="btn-primary flex-1">
                    <span wire:loading.remove wire:target="save">Ya, Simpan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>
