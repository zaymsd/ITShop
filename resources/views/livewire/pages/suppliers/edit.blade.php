<?php
use App\Models\Supplier;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {

    public int    $id;
    public string $name    = '';
    public string $phone   = '';
    public string $email   = '';
    public string $address = '';
    public bool $showUpdateModal = false;

    public function mount(int $id): void
    {
        $item          = Supplier::findOrFail($id);
        $this->id      = $id;
        $this->name    = $item->name;
        $this->phone   = $item->phone;
        $this->email   = $item->email   ?? '';
        $this->address = $item->address ?? '';
    }

    public function confirmUpdate(): void
    {
        $this->validate([
            'name'    => 'required|string|max:100',
            'phone'   => 'required|string|max:20',
            'email'   => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);
        $this->showUpdateModal = true;
    }

    public function save(): void
    {
        Supplier::findOrFail($this->id)->update([
            'name'    => $this->name,
            'phone'   => $this->phone,
            'email'   => $this->email ?: null,
            'address' => $this->address ?: null,
        ]);

        session()->flash('success', 'Supplier berhasil diperbarui.');
        $this->redirect(route('suppliers.index'), navigate: true);
    }
}; ?>

<div x-data="{ openModal: @entangle('showUpdateModal') }">
    {{-- ── Page Header ───────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h1 class="text-heading-lg text-ink">Edit Supplier</h1>
        <p class="text-body-md text-mute">Perbarui data pemasok</p>
    </div>

    {{-- ── Form Card ──────────────────────────────────────────────────────────── --}}
    <div class="max-w-2xl bg-surface-card rounded-[24px] p-8 border border-hairline">
        <form wire:submit.prevent="confirmUpdate" class="space-y-6">

            {{-- Name --}}
            <div>
                <label for="input-name" class="block text-[14px] font-semibold text-ink mb-1.5">
                    Nama Supplier <span class="text-primary">*</span>
                </label>
                <input wire:model="name" id="input-name" type="text" placeholder="Nama perusahaan atau pemasok..." autofocus
                       class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                @error('name') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="input-phone" class="block text-[14px] font-semibold text-ink mb-1.5">
                    No. Telepon <span class="text-primary">*</span>
                </label>
                <input wire:model="phone" id="input-phone" type="text" placeholder="Contoh: 08123456789"
                       class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                @error('phone') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="input-email" class="block text-[14px] font-semibold text-ink mb-1.5">
                    Email
                </label>
                <input wire:model="email" id="input-email" type="email" placeholder="supplier@contoh.com (opsional)"
                       class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                @error('email') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
            </div>

            {{-- Address --}}
            <div>
                <label for="input-address" class="block text-[14px] font-semibold text-ink mb-1.5">
                    Alamat
                </label>
                <textarea wire:model="address" id="input-address" rows="3" placeholder="Alamat lengkap (opsional)..."
                          class="w-full px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000] resize-none"></textarea>
                @error('address') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-hairline">
                <a href="{{ route('suppliers.index') }}" wire:navigate class="btn-secondary">
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
