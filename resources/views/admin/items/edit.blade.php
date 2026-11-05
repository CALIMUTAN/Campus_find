@php $input = 'mt-1 block w-full rounded-lg border-slate-300 shadow-sm focus:border-[#0F2A4A] focus:ring-[#0F2A4A]'; @endphp
<x-app-layout>
    <div class="mx-auto max-w-2xl px-4 py-8 sm:px-6 lg:py-10">
        <a href="{{ route('admin.items.index') }}" class="text-sm font-semibold text-[#0F2A4A] hover:underline">&larr; Back to items</a>
        <h1 class="mt-3 font-display text-3xl font-bold text-[#0F2A4A]">Edit {{ $item->type }} item</h1>
        <p class="text-sm text-slate-500">Reported by {{ $item->user->name }} ({{ $item->user->email }}) on {{ $item->created_at->format('M j, Y') }}</p>

        <form method="POST" action="{{ route('admin.items.update', $item) }}" class="mt-6 space-y-4">
            @csrf @method('PATCH')

            <div>
                <label for="item_name" class="text-sm font-semibold text-[#0F2A4A]">Item name</label>
                <input id="item_name" name="item_name" value="{{ old('item_name', $item->item_name) }}" required class="{{ $input }}">
                <x-input-error :messages="$errors->get('item_name')" />
            </div>
            <div>
                <label for="category" class="text-sm font-semibold text-[#0F2A4A]">Category</label>
                <select id="category" name="category" class="{{ $input }}">
                    @foreach ($categories as $c)
                        <option value="{{ $c }}" @selected(old('category', $item->category) === $c)>{{ $c }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('category')" />
            </div>
            <div>
                <label for="place" class="text-sm font-semibold text-[#0F2A4A]">Place</label>
                <input id="place" name="place" value="{{ old('place', $item->place) }}" required class="{{ $input }}">
                <x-input-error :messages="$errors->get('place')" />
            </div>
            <div>
                <label for="date" class="text-sm font-semibold text-[#0F2A4A]">Date</label>
                <input id="date" type="date" name="date" max="{{ now()->toDateString() }}" value="{{ old('date', $item->date->toDateString()) }}" required class="{{ $input }}">
                <x-input-error :messages="$errors->get('date')" />
            </div>
            <div>
                <label for="description" class="text-sm font-semibold text-[#0F2A4A]">Description</label>
                <textarea id="description" name="description" rows="4" maxlength="1000" class="{{ $input }}">{{ old('description', $item->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>
            <div>
                <label for="status" class="text-sm font-semibold text-[#0F2A4A]">Status</label>
                <select id="status" name="status" class="{{ $input }}">
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @selected(old('status', $item->status) === $s)>{{ $s }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('status')" />
            </div>

            <button class="rounded-lg bg-[#0F2A4A] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#16396a]">Save changes</button>
        </form>
    </div>
</x-app-layout>