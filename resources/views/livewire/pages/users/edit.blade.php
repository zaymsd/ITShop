<?php
use App\Models\User;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

new #[Layout('layouts.app')] class extends Component {

    public int    $id;
    public string $name     = '';
    public string $email    = '';
    public string $password = '';
    public string $role     = 'petugas';
    public bool $showUpdateModal = false;

    public function mount(int $id): void
    {
        $user        = User::findOrFail($id);
        $this->id    = $id;
        $this->name  = $user->name;
        $this->email = $user->email;
        $this->role  = $user->role;
    }

    public function confirmUpdate(): void
    {
        $rules = [
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->id)],
            'role'  => ['required', 'in:admin,petugas'],
        ];

        if ($this->password !== '') {
            $rules['password'] = ['required', 'string', 'min:8'];
        }

        $this->validate($rules);
        $this->showUpdateModal = true;
    }

    public function save(): void
    {
        $data = [
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role,
        ];

        if ($this->password !== '') {
            $data['password'] = Hash::make($this->password);
        }

        User::findOrFail($this->id)->update($data);

        session()->flash('success', 'Pengguna berhasil diperbarui.');
        $this->redirect(route('users.index'), navigate: true);
    }
}; ?>

<div x-data="{ openModal: @entangle('showUpdateModal') }">
    {{-- ── Page Header ───────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <h1 class="text-heading-lg text-ink">Edit Pengguna</h1>
        <p class="text-body-md text-mute">Perbarui data akun pengguna</p>
    </div>

    {{-- ── Form Card ──────────────────────────────────────────────────────────── --}}
    <div class="max-w-2xl bg-surface-card rounded-[24px] p-8 border border-hairline">
        <form wire:submit.prevent="confirmUpdate" class="space-y-6">

            {{-- Name --}}
            <div>
                <label class="block text-[14px] font-semibold text-ink mb-1.5">
                    Nama Lengkap <span class="text-primary">*</span>
                </label>
                <input wire:model="name" type="text" placeholder="Contoh: Budi Santoso" autofocus
                       class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                @error('name') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-[14px] font-semibold text-ink mb-1.5">
                    Email <span class="text-primary">*</span>
                </label>
                <input wire:model="email" type="email" placeholder="budi@example.com"
                       class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                @error('email') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-[14px] font-semibold text-ink mb-1.5">
                    Password <span class="text-mute font-normal">(Kosongkan jika tidak ingin mengubah)</span>
                </label>
                <div x-data="{ showPw: false }" class="relative">
                    <input wire:model="password" :type="showPw ? 'text' : 'password'" placeholder="••••••••"
                           class="w-full h-[44px] pl-[15px] pr-10 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"/>
                    <button type="button" @click="showPw = !showPw" class="absolute right-3 top-1/2 -translate-y-1/2 text-mute hover:text-ink">
                        <svg x-show="!showPw" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="showPw" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                @error('password') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-[14px] font-semibold text-ink mb-2">
                    Role <span class="text-primary">*</span>
                </label>
                <div class="grid grid-cols-2 gap-3">
                    {{-- Admin --}}
                    <label class="relative flex items-center gap-3 p-4 rounded-[16px] border cursor-pointer transition-all duration-200
                                  @if($role === 'admin') border-ink shadow-[inset_0_0_0_1px_#111111] bg-canvas @else border-ash bg-canvas hover:border-ink @endif">
                        <input wire:model="role" type="radio" value="admin" class="sr-only"/>
                        <div class="w-8 h-8 rounded-[8px] bg-[#E8F0FE] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-[#1967D2]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9.243 3.03a1 1 0 01.727 1.213L9.53 6h2.94l.56-2.243a1 1 0 111.94.486L14.53 6H17a1 1 0 110 2h-2.97l-1 4H15a1 1 0 110 2h-2.47l-.56 2.242a1 1 0 11-1.94-.485L10.47 14H7.53l-.56 2.242a1 1 0 11-1.94-.485L5.47 14H3a1 1 0 110-2h2.97l1-4H5a1 1 0 110-2h2.47l.56-2.243a1 1 0 011.213-.727zM9.03 8l-1 4h2.938l1-4H9.031z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <p class="text-[14px] font-bold text-ink">Admin</p>
                            <p class="text-[12px] text-mute">Akses penuh</p>
                        </div>
                    </label>

                    {{-- Petugas --}}
                    <label class="relative flex items-center gap-3 p-4 rounded-[16px] border cursor-pointer transition-all duration-200
                                  @if($role === 'petugas') border-ink shadow-[inset_0_0_0_1px_#111111] bg-canvas @else border-ash bg-canvas hover:border-ink @endif">
                        <input wire:model="role" type="radio" value="petugas" class="sr-only"/>
                        <div class="w-8 h-8 rounded-[8px] bg-surface-soft flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-ink" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <p class="text-[14px] font-bold text-ink">Petugas</p>
                            <p class="text-[12px] text-mute">Akses terbatas</p>
                        </div>
                    </label>
                </div>
                @error('role') <p class="mt-1.5 text-xs font-semibold text-primary">{{ $message }}</p> @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-hairline">
                <a href="{{ route('users.index') }}" wire:navigate class="btn-secondary">
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
                <svg class="w-8 h-8 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
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
