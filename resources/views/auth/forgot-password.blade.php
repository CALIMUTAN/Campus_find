@php
    $input = 'w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-[#0F2A4A] focus:outline-none focus:ring-2 focus:ring-[#F5B301]/60';
    $button = 'w-full rounded-lg bg-[#0F2A4A] px-5 py-3 font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2';
@endphp

<x-guest-layout>
    <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">Forgot your password?</h1>
    <p class="mt-2 text-slate-600">Enter your email and we’ll send you a link to choose a new password.</p>

    <x-auth-session-status class="mt-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-slate-800">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   placeholder="you@school.edu.ph" class="{{ $input }}">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="{{ $button }}">Email me a reset link</button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-600">
        Remembered it?
        <a href="{{ route('login') }}" class="font-semibold text-[#0F2A4A] underline-offset-2 hover:underline">Back to log in</a>
    </p>
</x-guest-layout>