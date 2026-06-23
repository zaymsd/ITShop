<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     * New users registered via form default to role 'petugas'.
     * Admin role can only be assigned by an existing admin via the Users CRUD module.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role']     = 'petugas'; // default role for self-registration

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="w-full">
    <form wire:submit="register" class="space-y-4">
        {{-- Name --}}
        <div>
            <label for="name" class="block text-[14px] font-semibold text-ink mb-1">
                Name
            </label>
            <input wire:model="name" id="name" type="text" name="name"
                   required autofocus autocomplete="name"
                   class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"
                   placeholder="Full name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-[14px] font-semibold text-ink mb-1">
                Email
            </label>
            <input wire:model="email" id="email" type="email" name="email"
                   required autocomplete="username"
                   class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"
                   placeholder="Email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-[14px] font-semibold text-ink mb-1">
                Password
            </label>
            <div class="relative" x-data="{ show: false }">
                <input wire:model="password" id="password"
                       :type="show ? 'text' : 'password'" name="password"
                       required autocomplete="new-password"
                       class="w-full h-[44px] pl-[15px] pr-10 py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"
                       placeholder="Create a password" />
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-mute hover:text-ink">
                    <svg x-show="!show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-[14px] font-semibold text-ink mb-1">
                Confirm Password
            </label>
            <input wire:model="password_confirmation" id="password_confirmation"
                   type="password" name="password_confirmation"
                   required autocomplete="new-password"
                   class="w-full h-[44px] px-[15px] py-[11px] bg-canvas text-ink text-[16px] border border-ash rounded-[16px] outline-none transition-all duration-200 focus:border-transparent focus:ring-4 focus:ring-focus-outer focus:shadow-[inset_0_0_0_2px_#000000]"
                   placeholder="Confirm your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
        </div>

        {{-- Role info note --}}
        <div class="mt-2 text-xs font-semibold text-mute">
            New accounts are registered as <strong>Cashier</strong> by default.
        </div>

        {{-- Submit --}}
        <div class="mt-6 pt-2">
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-[14px] py-[6px] h-[40px] rounded-[16px] bg-primary text-on-dark text-[14px] font-bold transition-colors hover:bg-primary-pressed focus:outline-none">
                Sign up
            </button>
        </div>
    </form>

    {{-- Divider --}}
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-hairline"></div>
        </div>
        <div class="relative flex justify-center text-xs">
            <span class="px-3 bg-canvas text-mute font-semibold">OR</span>
        </div>
    </div>

    {{-- Google OAuth Button --}}
    <a href="{{ route('google.redirect') }}" class="w-full flex items-center justify-center gap-2 px-[14px] py-[6px] h-[40px] rounded-[16px] bg-secondary-bg text-ink text-[14px] font-bold transition-colors hover:bg-secondary-pressed focus:outline-none">
        <svg class="w-5 h-5 mr-1" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        Continue with Google
    </a>

    {{-- Login link --}}
    <p class="mt-6 text-center text-[14px] font-semibold text-ink">
        Already a member?
        <a href="{{ route('login') }}" wire:navigate class="font-bold hover:underline">
            Log in
        </a>
    </p>
</div>
