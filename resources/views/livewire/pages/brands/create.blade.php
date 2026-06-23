<?php
use App\Models\Brand;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

    public string $name = '';
    public bool $showSaveModal = false;

    public function confirmSave(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
        ]);
        $this->showSaveModal = true;
    }

    public function save(): void
    {
        Brand::create(['name' => $this->name]);

        session()->flash('success', 'Brand berhasil ditambahkan.');
        $this->redirect(route('brands.index'), navigate: true);
    }
}; ?>

<div x-data="{ openModal: @entangle('showSaveModal') }">
    {{-- ── Page Header ───────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h1 class="text-heading-lg text-ink">Tambah Brand</h1>
        <p class="text-body-md text-mute">Buat brand / merek produk baru</p>
    </div>

    {{-- ── Form Card ──────────────────────────────────────────────────────────── --}}
    <div class="max-w-2xl bg-surface-card rounded-[24px] p-8 border border-hairline">
        <form wire:submit.prevent="confirmSave" class="space-y-6">

            {{-- Nama Brand --}}
            <div>
                <label for="input-brand-name" class="block text-[14px] font-semibold text-ink mb-1.5">
                    Nama Brand <span class="text-primary">*</span>
                </label>
                <input wire:model="name" id="input-brand-name" type="text" placeholder="Contoh: ASUS, Lenovo, HP..." autofocus
                       class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                @error('name') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
            </div>

            <p class="text-xs font-semibold text-mute">
                Upload logo brand tersedia di halaman detail brand (opsional).
            </p>

            {{-- Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-hairline">
                <a href="{{ route('brands.index') }}" wire:navigate class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    Simpan
                </button>
            </div>
        </form>
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
                <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
            </div>
            
            <h3 class="text-heading-lg text-ink mb-2">Simpan Brand?</h3>
            <p class="text-body-md text-body mb-8">
                Apakah Anda yakin ingin menyimpan brand baru ini ke database?
            </p>
            
            <div class="flex w-full gap-3">
                <button @click="openModal = false; $wire.showSaveModal = false" class="btn-secondary flex-1">
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
