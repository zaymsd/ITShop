<x-app-layout>
    <div class="relative min-h-[80vh]">
        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-heading-lg text-ink">Profil Akun</h1>
                <p class="text-body-md text-mute">Kelola informasi dan pengaturan akun Anda.</p>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-surface-card border border-hairline rounded-[32px] p-8 max-w-3xl hover:shadow-[0_0_0_2px_#111111] transition-all duration-200">
                <livewire:profile.update-profile-information-form />
            </div>

            <div class="bg-surface-card border border-hairline rounded-[32px] p-8 max-w-3xl hover:shadow-[0_0_0_2px_#111111] transition-all duration-200">
                <livewire:profile.update-password-form />
            </div>

            <div class="bg-surface-card border border-hairline rounded-[32px] p-8 max-w-3xl hover:shadow-[0_0_0_2px_#E60023] transition-all duration-200">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
