@php
    $button = 'w-full rounded-lg bg-[#0F2A4A] px-5 py-3 font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2';
@endphp

<x-guest-layout>
    <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">Check your email</h1>
    <p class="mt-2 text-slate-600">Thanks for signing up! Before you start, please verify your email address by clicking the link we just sent you. If it didn’t arrive, we can send another.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="mt-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
            A new verification link has been sent to the email address you used to register.
        </div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mt-8">
        @csrf
        <button type="submit" class="{{ $button }}">Resend verification email</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="mt-6 text-center">
        @csrf
        <button type="submit" class="text-sm font-medium text-slate-600 underline-offset-2 hover:text-[#0F2A4A] hover:underline focus:outline-none focus-visible:underline">Log out</button>
    </form>
</x-guest-layout>