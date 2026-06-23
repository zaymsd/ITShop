<?php
use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public string $search = '';

    public bool $showDeleteModal = false;
    public ?int $deleteId   = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        if (auth()->id() === $id) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }
        $this->deleteId   = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            if (auth()->id() === $this->deleteId) {
                session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
                $this->showDeleteModal = false;
                $this->deleteId   = null;
                return;
            }
            User::findOrFail($this->deleteId)->delete();
            session()->flash('success', 'Pengguna berhasil dihapus.');
        }
        $this->showDeleteModal = false;
        $this->deleteId   = null;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.users.index', [
            'users' => User::when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            }))->latest()->paginate(10),
        ]);
    }
}; ?>

<div class="relative min-h-[80vh]">
    {{-- ── Flash Messages ────────────────────────────────────────────────────── --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-6 flex items-center justify-between gap-3 rounded-[16px] bg-[#E6F4EA] border border-[#CEEAD6] px-4 py-3 text-sm text-[#137333] shadow-sm">
            <div class="flex items-center gap-2">
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-[#137333] hover:opacity-70">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 flex items-center justify-between gap-3 rounded-[16px] bg-[#ffeaea] border border-[#ffcaca] px-4 py-3 text-sm text-primary shadow-sm">
            <div class="flex items-center gap-2">
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-primary hover:opacity-70">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- ── Page Header ───────────────────────────────────────────────────────── --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-heading-lg text-ink">Manajemen Pengguna</h1>
            <p class="text-body-md text-mute">Kelola akun admin & petugas</p>
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
               type="text"
               placeholder="Cari nama atau email pengguna..."
               class="w-full h-[44px] pl-11 pr-4 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
    </div>

    {{-- ── Table Card ────────────────────────────────────────────────────────── --}}
    <div class="bg-surface-card rounded-[16px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-surface-soft border-b border-hairline">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider w-12">No</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-4 text-xs font-bold text-mute uppercase tracking-wider">Email</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-mute uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-mute uppercase tracking-wider">Terdaftar</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-mute uppercase tracking-wider w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline">
                    @forelse ($users as $user)
                        <tr class="hover:bg-surface-soft transition-colors duration-150 {{ auth()->id() === $user->id ? 'bg-[#f0f0f0]' : '' }}">
                            <td class="px-6 py-4 text-mute font-mono text-xs">
                                {{ $users->firstItem() + $loop->index }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $words = explode(' ', trim($user->name));
                                        $initials = strtoupper(count($words) >= 2 ? $words[0][0] . $words[1][0] : substr($user->name, 0, 2));
                                    @endphp
                                    <div class="w-8 h-8 rounded-[8px] bg-ink flex items-center justify-center text-xs font-bold text-on-dark shrink-0">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-ink flex items-center gap-2">
                                            {{ $user->name }}
                                            @if (auth()->id() === $user->id)
                                                <span class="text-[10px] bg-ink text-on-dark px-1.5 py-0.5 rounded-[4px]">Anda</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-ink">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($user->role === 'admin')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-[8px] text-xs font-bold bg-[#E8F0FE] text-[#1967D2]">
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-[8px] text-xs font-bold bg-surface-soft text-ink">
                                        Petugas
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-mute text-xs">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('users.edit', $user->id) }}" wire:navigate
                                       title="Edit"
                                       class="w-8 h-8 rounded-[8px] flex items-center justify-center text-mute hover:bg-canvas hover:text-ink transition-colors border border-transparent hover:border-ash">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button wire:click="confirmDelete({{ $user->id }})"
                                            title="{{ auth()->id() === $user->id ? 'Tidak dapat menghapus akun sendiri' : 'Hapus' }}"
                                            @class([
                                                'w-8 h-8 rounded-[8px] flex items-center justify-center transition-colors border border-transparent',
                                                'text-mute/30 cursor-not-allowed' => auth()->id() === $user->id,
                                                'text-mute hover:bg-[#ffeaea] hover:text-primary hover:border-[#ffcaca]' => auth()->id() !== $user->id,
                                            ])
                                            @if(auth()->id() === $user->id) disabled @endif>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-2 text-mute">
                                    <svg class="w-12 h-12 opacity-20 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <p class="text-sm font-semibold">
                                        @if ($search)
                                            Tidak ada pengguna yang cocok dengan "{{ $search }}"
                                        @else
                                            Belum ada pengguna.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-hairline bg-canvas">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    {{-- ── Floating Action Button (FAB) ──────────────────────────────────────── --}}
    <a href="{{ route('users.create') }}" wire:navigate
       class="fixed bottom-8 right-8 w-14 h-14 rounded-full bg-primary text-on-dark flex items-center justify-center shadow-[0_4px_16px_rgba(230,0,35,0.4)] hover:scale-105 transition-transform z-40"
       title="Tambah Pengguna">
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
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            
            <h3 class="text-heading-lg text-ink mb-2">Hapus Pengguna?</h3>
            <p class="text-body-md text-body mb-8">
                Tindakan ini tidak dapat dibatalkan. Akun ini akan dihapus permanen.
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
