<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-heading-md text-ink">
            {{ __('Informasi Profil') }}
        </h2>

        <p class="mt-1 text-body-md text-mute">
            {{ __("Perbarui nama dan alamat email profil akun Anda.") }}
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-8 space-y-6">
        <div>
            <label for="name" class="block text-[14px] font-bold text-ink mb-2">{{ __('Nama') }}</label>
            <input wire:model="name" id="name" name="name" type="text" class="w-full h-[44px] px-4 bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer" required autofocus autocomplete="name" />
            @error('name') <span class="mt-2 text-primary text-sm font-semibold block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="email" class="block text-[14px] font-bold text-ink mb-2">{{ __('Email') }}</label>
            <input wire:model="email" id="email" name="email" type="email" class="w-full h-[44px] px-4 bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer" required autocomplete="username" />
            @error('email') <span class="mt-2 text-primary text-sm font-semibold block">{{ $message }}</span> @enderror

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div class="mt-4 p-4 rounded-[16px] bg-[#fff3cd] border border-[#ffe69c]">
                    <p class="text-sm font-semibold text-[#664d03]">
                        {{ __('Alamat email Anda belum diverifikasi.') }}

                        <button wire:click.prevent="sendVerification" class="underline text-[#664d03] hover:text-[#332701] rounded-md focus:outline-none">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-sm text-[#137333]">
                            {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 mt-8 pt-6 border-t border-hairline">
            <button class="btn-primary">
                {{ __('Simpan Perubahan') }}
            </button>

            <x-action-message class="me-3 text-sm font-bold text-[#137333]" on="profile-updated">
                {{ __('Tersimpan.') }}
            </x-action-message>
        </div>
    </form>
</section>
