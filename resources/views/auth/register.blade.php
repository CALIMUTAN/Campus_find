@php
    $input = 'w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-[#0F2A4A] focus:outline-none focus:ring-2 focus:ring-[#F5B301]/60';
    $button = 'w-full rounded-lg bg-[#0F2A4A] px-5 py-3 font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2';
@endphp

<x-guest-layout>
    <h1 class="font-display text-3xl font-bold text-[#0F2A4A]">Create your account</h1>
    <p class="mt-2 text-slate-600">It only takes a minute. Use your school email.</p>

    {{-- TODO (logic branch): the requirements (FR-01) also call for a student ID number field. --}}
    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-slate-800">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                   placeholder="Juan Dela Cruz" class="{{ $input }}">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-slate-800">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                   placeholder="you@gmail.com" class="{{ $input }}">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium text-slate-800">Password</label>
            <div x-data="{ show: false }" class="relative">
                <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="new-password"
                       class="{{ $input }} pr-16">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-slate-500 hover:text-[#0F2A4A] focus:outline-none focus-visible:underline">
                    <span x-text="show ? 'Hide' : 'Show'">Show</span>
                </button>
            </div>
            <p class="mt-1.5 text-sm text-slate-500">Use at least 8 characters.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-800">Confirm password</label>
            <div x-data="{ show: false }" class="relative">
                <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required autocomplete="new-password"
                       class="{{ $input }} pr-16">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-sm font-medium text-slate-500 hover:text-[#0F2A4A] focus:outline-none focus-visible:underline">
                    <span x-text="show ? 'Hide' : 'Show'">Show</span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="{{ $button }}">Create account</button>
    </form>

    <p class="mt-8 text-center text-sm text-slate-600">
        Already have an account?
        <a href="{{ route('login') }}" class="font-semibold text-[#0F2A4A] underline-offset-2 hover:underline">Log in</a>
    </p>
</x-guest-layout>