@php
    $input = 'w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-[#0F2A4A] focus:outline-none focus:ring-2 focus:ring-[#F5B301]/60';
    $button = 'w-full rounded-lg bg-[#0F2A4A] px-5 py-3 font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2';
@endphp

<x-guest-layout>
    <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">Choose a new password</h1>
    <p class="mt-2 text-slate-600">Pick a password you haven’t used before. Use at least 8 characters.</p>

    <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-slate-800">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                   class="{{ $input }}">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium text-slate-800">New password</label>
            <div x-data="{ show: false }" class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password"
                       class="{{ $input }} pr-16">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-slate-500 hover:text-[#0F2A4A] focus:outline-none focus-visible:underline">
                    <span x-text="show ? 'Hide' : 'Show'">Show</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-800">Confirm new password</label>
            <div x-data="{ show: false }" class="relative">
                <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required autocomplete="new-password"
                       class="{{ $input }} pr-16">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-slate-500 hover:text-[#0F2A4A] focus:outline-none focus-visible:underline">
                    <span x-text="show ? 'Hide' : 'Show'">Show</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="{{ $button }}">Reset password</button>
    </form>
</x-guest-layout>