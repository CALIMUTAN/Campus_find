{{--
    Profile page (design/cf-002/user-dashboard-ui)
    Same page width and title style as the dashboard. The forms inside are unchanged.
--}}
<x-app-layout>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
        <h1 class="font-display text-3xl font-bold tracking-tight text-[#0F2A4A]">Profile</h1>
        <p class="mt-2 text-slate-600">Change your name, email, or password.</p>

        <div class="mt-8 max-w-3xl space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="rounded-xl border border-red-200 bg-white p-5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>