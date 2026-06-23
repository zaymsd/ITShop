<?php
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use App\Models\Supplier;

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
        $suppliers = Supplier::query()
            ->when($this->search, fn ($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")
            )
            ->latest()
            ->paginate(10);

        return view('livewire.pages.suppliers.index', compact('suppliers'));
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId        = $id;
        $this->showDeleteModal = true;
    }

    public function destroy(): void
    {
        Supplier::findOrFail($this->deleteId)->delete();
        $this->showDeleteModal = false;
        $this->deleteId        = null;
        session()->flash('success', 'Supplier berhasil dihapus.');
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
            <h1 class="text-heading-lg text-ink">Supplier</h1>
            <p class="text-body-md text-mute">Kelola data pemasok / supplier</p>
        </div>
    </div>

    {{-- ── Search Bar ────────────────────────────────────────────────────────── --}}
    <div class="mb-6 relative max-w-md">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
            <svg class="w-5 h-5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <input wire:model.live.debounce.300ms="search"
               id="search-suppliers"
               type="text"
               placeholder="Cari nama, email, telepon..."
               class="w-full h-[44px] pl-11 pr-4 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
    </div>

    {{-- ── Table Card ────────────────────────────────────────────────────────── --}}
    <div class="bg-surface-card rounded-[16px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-soft border-b border-hairline">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider w-12">No</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Nama Supplier</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">No. Telepon</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Alamat</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-mute uppercase tracking-wider w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse ($suppliers as $item)
                        <tr class="hover:bg-surface-soft transition-colors duration-150">
                            <td class="px-6 py-4 text-mute font-mono text-xs">
                                {{ $suppliers->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-[8px] flex items-center justify-center text-xs font-bold text-on-dark bg-ink shrink-0">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </div>
                                    <span class="font-bold text-ink">{{ $item->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="tel:{{ $item->phone }}" class="text-ink hover:underline">
                                    {{ $item->phone }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if ($item->email)
                                    <a href="mailto:{{ $item->email }}" class="text-ink hover:underline">
                                        {{ $item->email }}
                                    </a>
                                @else
                                    <span class="text-mute">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-body max-w-[200px] truncate">
                                {{ $item->address ?: '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('suppliers.edit', $item->id) }}" wire:navigate
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
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2 text-mute">
                                    <svg class="w-12 h-12 opacity-20 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-sm font-semibold">
                                        @if ($search)
                                            Tidak ada supplier yang cocok dengan "{{ $search }}"
                                        @else
                                            Belum ada supplier.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($suppliers->hasPages())
            <div class="px-6 py-4 border-t border-hairline bg-canvas">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

    {{-- ── Floating Action Button (FAB) ──────────────────────────────────────── --}}
    <a href="{{ route('suppliers.create') }}" wire:navigate
       class="fixed bottom-8 right-8 w-14 h-14 rounded-full bg-primary text-on-dark flex items-center justify-center shadow-[0_4px_16px_rgba(230,0,35,0.4)] hover:scale-105 transition-transform z-40"
       title="Tambah Supplier">
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
            
            <h3 class="text-heading-lg text-ink mb-2">Hapus Supplier?</h3>
            <p class="text-body-md text-body mb-8">
                Tindakan ini tidak dapat dibatalkan. Data supplier akan dihapus permanen.
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
