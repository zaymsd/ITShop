<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';
    public bool $showDeleteModal = false;

    public function confirmUserDeletion(): void
    {
        $this->password = '';
        $this->showDeleteModal = true;
    }

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-heading-md text-primary">
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-body-md text-mute">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Harap pastikan sebelum menghapus akun Anda.') }}
        </p>
    </header>

    <div class="pt-6 border-t border-hairline">
        <button wire:click="confirmUserDeletion" class="btn-primary">
            {{ __('Hapus Akun') }}
        </button>
    </div>

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
            
            <h2 class="text-heading-lg text-ink mb-2">
                {{ __('Yakin ingin menghapus?') }}
            </h2>

            <p class="text-body-md text-mute mb-8">
                {{ __('Masukkan password Anda untuk mengonfirmasi penghapusan akun ini secara permanen.') }}
            </p>
            
            <form wire:submit="deleteUser" class="w-full">
                <div class="w-full text-left mb-8">
                    <label for="password" class="block text-[14px] font-bold text-ink mb-2">{{ __('Password') }}</label>
                    <input wire:model="password" id="password" name="password" type="password" class="w-full h-[44px] px-4 bg-surface-soft text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer" placeholder="{{ __('Password') }}" />
                    @error('password') <span class="mt-2 text-primary text-sm font-semibold block">{{ $message }}</span> @enderror
                </div>

                <div class="flex w-full gap-3">
                    <button type="button" @click="open = false; $wire.showDeleteModal = false" class="btn-secondary flex-1">
                        {{ __('Batal') }}
                    </button>

                    <button class="btn-primary flex-1">
                        {{ __('Ya, Hapus') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
