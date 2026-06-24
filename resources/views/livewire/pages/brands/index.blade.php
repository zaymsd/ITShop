<?php
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Brand;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    public bool $showDeleteModal = false;
    public ?int $deleteId        = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $brands = Brand::withCount('products')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(10);

        return view('livewire.pages.brands.index', compact('brands'));
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId        = $id;
        $this->showDeleteModal = true;
    }

    public function destroy(): void
    {
        Brand::findOrFail($this->deleteId)->delete();
        $this->showDeleteModal = false;
        $this->deleteId        = null;
        session()->flash('success', 'Brand berhasil dihapus.');
    }
}; ?>

<div class="relative min-h-[80vh]">
    {{-- ── Flash Message ─────────────────────────────────────────────────────── --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mb-6 flex items-center justify-between gap-3 rounded-[16px] bg-[#E6F4EA] border border-[#CEEAD6] px-4 py-3 text-sm text-[#137333] shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-[#137333] hover:opacity-70 transition-opacity">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- ── Page Header ───────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-heading-lg text-ink">Brand / Merek</h1>
            <p class="text-body-md text-mute">Kelola brand dan merek produk</p>
        </div>
    </div>

    {{-- ── Search Bar ────────────────────────────────────────────────────────── --}}
    <div class="mb-6 relative w-full md:max-w-2xl">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
            <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input wire:model.live.debounce.300ms="search"
               id="search-brands"
               type="text"
               placeholder="Cari nama brand..."
               class="w-full h-[44px] pl-11 pr-4 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
    </div>

    {{-- ── Table Card ────────────────────────────────────────────────────────── --}}
    <div class="bg-surface-card rounded-[16px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-soft border-b border-hairline">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider w-12">No</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider w-20">Logo</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Nama Brand</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-mute uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-mute uppercase tracking-wider w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse ($brands as $item)
                        <tr class="hover:bg-surface-soft transition-colors duration-150">
                            <td class="px-6 py-4 text-mute font-mono text-xs">
                                {{ $brands->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->logo)
                                    <img src="{{ asset('storage/' . $item->logo) }}"
                                         alt="{{ $item->name }}"
                                         class="w-10 h-10 object-contain rounded-[8px] bg-canvas border border-ash p-1"/>
                                @else
                                    <div class="w-10 h-10 rounded-[8px] bg-canvas border border-ash flex items-center justify-center">
                                        <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-ink">{{ $item->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center px-2 py-1 rounded-[8px] text-xs font-bold bg-canvas border border-ash text-ink">
                                    {{ $item->products_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('brands.edit', $item->id) }}" wire:navigate
                                       title="Edit"
                                       class="w-8 h-8 rounded-[8px] flex items-center justify-center text-mute hover:bg-canvas hover:text-ink transition-colors border border-transparent hover:border-ash">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button wire:click="confirmDelete({{ $item->id }})"
                                            title="Hapus"
                                            class="w-8 h-8 rounded-[8px] flex items-center justify-center text-mute hover:bg-[#ffeaea] hover:text-primary transition-colors border border-transparent hover:border-[#ffcaca]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2 text-mute">
                                    <svg class="w-12 h-12 opacity-20 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <p class="text-sm font-semibold">
                                        @if ($search)
                                            Tidak ada brand yang cocok dengan "{{ $search }}"
                                        @else
                                            Belum ada brand.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($brands->hasPages())
            <div class="px-6 py-4 border-t border-hairline bg-canvas">
                {{ $brands->links() }}
            </div>
        @endif
    </div>

    {{-- ── Floating Action Button (FAB) ──────────────────────────────────────── --}}
    <a href="{{ route('brands.create') }}" wire:navigate
       class="fixed bottom-8 right-8 w-14 h-14 rounded-full bg-primary text-on-dark flex items-center justify-center shadow-[0_4px_16px_rgba(230,0,35,0.4)] hover:scale-105 transition-transform z-40"
       title="Tambah Brand">
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

            <div class="w-16 h-16 rounded-full bg-[#ffeaea] flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            
            <h3 class="text-heading-lg text-ink mb-2">Hapus Brand?</h3>
            <p class="text-body-md text-body mb-8">
                Tindakan ini tidak dapat dibatalkan. Data brand akan dihapus permanen.
            </p>
            
            <div class="flex w-full gap-3">
                <button @click="open = false; $wire.showDeleteModal = false" class="btn-secondary flex-1">
                    Batal
                </button>
                <button wire:click="destroy" class="btn-primary flex-1">
                    <span wire:loading.remove wire:target="destroy">Ya, Hapus</span>
                    <span wire:loading wire:target="destroy">Menghapus...</span>
                </button>
            </div>
        </div>
    </div>
</div>
