@php
    $input = 'w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-[#0F2A4A] focus:outline-none focus:ring-2 focus:ring-[#F5B301]/60';
    $button = 'w-full rounded-lg bg-[#0F2A4A] px-5 py-3 font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2';
@endphp

<x-guest-layout>
    <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">Confirm your password</h1>
    <p class="mt-2 text-slate-600">This is a secure area. Please confirm your password before continuing.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium text-slate-800">Password</label>
            <div x-data="{ show: false }" class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required autofocus autocomplete="current-password"
                       class="{{ $input }} pr-16">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-slate-500 hover:text-[#0F2A4A] focus:outline-none focus-visible:underline">
                    <span x-text="show ? 'Hide' : 'Show'">Show</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <button type="submit" class="{{ $button }}">Confirm</button>
    </form>
</x-guest-layout>