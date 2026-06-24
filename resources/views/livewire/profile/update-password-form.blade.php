<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section>
    <header>
        <h2 class="text-heading-md text-ink">
            {{ __('Ubah Password') }}
        </h2>

        <p class="mt-1 text-body-md text-mute">
            {{ __('Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-8 space-y-6">
        <div>
            <label for="update_password_current_password" class="block text-[14px] font-bold text-ink mb-2">{{ __('Password Saat Ini') }}</label>
            <input wire:model="current_password" id="update_password_current_password" name="current_password" type="password" class="w-full h-[44px] px-4 bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer" autocomplete="current-password" />
            @error('current_password') <span class="mt-2 text-primary text-sm font-semibold block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="update_password_password" class="block text-[14px] font-bold text-ink mb-2">{{ __('Password Baru') }}</label>
            <input wire:model="password" id="update_password_password" name="password" type="password" class="w-full h-[44px] px-4 bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer" autocomplete="new-password" />
            @error('password') <span class="mt-2 text-primary text-sm font-semibold block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-[14px] font-bold text-ink mb-2">{{ __('Konfirmasi Password Baru') }}</label>
            <input wire:model="password_confirmation" id="update_password_password_confirmation" name="password_confirmation" type="password" class="w-full h-[44px] px-4 bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer" autocomplete="new-password" />
            @error('password_confirmation') <span class="mt-2 text-primary text-sm font-semibold block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center gap-4 mt-8 pt-6 border-t border-hairline">
            <button class="btn-primary">
                {{ __('Ubah Password') }}
            </button>

            <x-action-message class="me-3 text-sm font-bold text-[#137333]" on="password-updated">
                {{ __('Tersimpan.') }}
            </x-action-message>
        </div>
    </form>
</section>
