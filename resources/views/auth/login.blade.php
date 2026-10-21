@php
    $input = 'w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-[#0F2A4A] focus:outline-none focus:ring-2 focus:ring-[#F5B301]/60';
    $button = 'w-full rounded-lg bg-[#0F2A4A] px-5 py-3 font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2';
@endphp

<x-guest-layout>
    <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">Welcome back</h1>
    <p class="mt-2 text-slate-600">Log in to report, search, or claim an item.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-slate-800">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="you@gmail.com" class="{{ $input }}">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <div class="mb-1.5 flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-slate-800">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#0F2A4A] underline-offset-2 hover:underline focus:outline-none focus-visible:underline">Forgot password?</a>
                @endif
            </div>
            <div x-data="{ show: false }" class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password"
                       class="{{ $input }} pr-16">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-slate-500 hover:text-[#0F2A4A] focus:outline-none focus-visible:underline">
                    <span x-text="show ? 'Hide' : 'Show'">Show</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember_me" class="flex items-center gap-2 text-sm text-slate-600">
            <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-[#0F2A4A] focus:ring-[#F5B301]">
            Keep me logged in on this device
        </label>

        <button type="submit" class="{{ $button }}">Log in</button>
    </form>

    @if (Route::has('register'))
        <p class="mt-8 text-center text-sm text-slate-600">
            New to Campus Find?
            <a href="{{ route('register') }}" class="font-semibold text-[#0F2A4A] underline-offset-2 hover:underline">Create an account</a>
        </p>
    @endif
</x-guest-layout>