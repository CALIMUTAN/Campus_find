<x-app-layout>
    <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:py-10">
        <a href="{{ route('items.show', $item) }}" class="text-sm font-semibold text-[#0F2A4A] hover:underline">&larr; Back to item</a>
        <h1 class="mt-3 font-display text-3xl font-bold text-[#0F2A4A]">Claim this item</h1>
        <p class="mt-1 text-slate-600">
            {{ $item->item_name }} &middot; {{ $item->category }} &middot; found at {{ $item->place }} on {{ $item->date->format('M j, Y') }}
        </p>

        <div class="mt-5 rounded-xl bg-[#EEF3F9] p-4 text-sm text-slate-600">
            <p class="font-semibold text-[#0F2A4A]">How to prove it's yours</p>
            <p class="mt-1">Describe things only the owner would know: unique marks, what's inside, serial number, or where and when you lost it. Don't just repeat the public listing. Staff will review your claim, and you'll still need to show your school ID at pickup.</p>
        </div>

        <form method="POST" action="{{ route('claims.store', $item) }}" class="mt-6 space-y-2">
            @csrf
            <label for="proof" class="text-sm font-semibold text-[#0F2A4A]">Proof of ownership</label>
            <textarea id="proof" name="proof" rows="6" maxlength="1000" required
                      class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-[#0F2A4A] focus:ring-[#0F2A4A]">{{ old('proof') }}</textarea>
            <x-input-error :messages="$errors->get('proof')" />
            <button type="submit" class="mt-2 rounded-lg bg-[#0F2A4A] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2">Submit claim</button>
        </form>
    </div>
</x-app-layout>