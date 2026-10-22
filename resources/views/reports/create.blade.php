{{--
    Report form for the dashboard buttons (feat/cf-001/add-user-dashboard-routing)
    One page for both: /report/lost and /report/found
    Navy top section like the front page hero, white form sitting on top of it.
--}}
@php
    $isLost = $type === 'lost';

    // Input style: light blue fill, turns white with a navy border when typing, red when there's an error.
    $field = fn (string $name) => 'mt-1.5 block w-full rounded-lg text-slate-800 placeholder:text-slate-400 focus:bg-white '
        . ($errors->has($name)
            ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-500'
            : 'border-[#D5DEEA] bg-[#EEF3F9] focus:border-[#0F2A4A] focus:ring-[#0F2A4A]');

    $steps = $isLost
        ? ['Your report shows up on your dashboard.', 'When a matching item is dropped off, you get a notification.', 'Submit a claim. Staff check your school ID before releasing the item.']
        : ['Drop the item off at the Item Drop-Off.', 'Staff record it and mark it "In custody".', 'The owner claims it, and staff verify them before releasing it.'];
@endphp

<x-app-layout>
    {{-- Navy top section --}}
    <div class="bg-[#0F2A4A] lg:border-l lg:border-white/10">
        <div class="mx-auto max-w-6xl px-4 pb-24 pt-8 sm:px-6 lg:px-8 lg:pt-10">

            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 rounded text-sm font-semibold text-slate-300 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                Back to dashboard
            </a>

            <div class="mt-6 grid grid-cols-1 gap-10 lg:grid-cols-[minmax(0,1fr)_19rem] lg:gap-16">
                <div>
                    {{-- Switch between the two forms --}}
                    <nav class="inline-flex rounded-lg bg-white/10 p-1 text-sm font-semibold" aria-label="Report type">
                        <a href="{{ route('reports.create', 'lost') }}" @if ($isLost) aria-current="page" @endif
                           class="rounded-md px-3.5 py-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-white {{ $isLost ? 'bg-[#F5B301] text-[#0F2A4A]' : 'text-slate-200 hover:text-white' }}">
                            I lost an item
                        </a>
                        <a href="{{ route('reports.create', 'found') }}" @unless ($isLost) aria-current="page" @endunless
                           class="rounded-md px-3.5 py-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-white {{ ! $isLost ? 'bg-[#F5B301] text-[#0F2A4A]' : 'text-slate-200 hover:text-white' }}">
                            I found an item
                        </a>
                    </nav>

                    <h1 class="mt-5 font-display text-4xl font-bold tracking-tight text-white sm:text-5xl">
                        {{ $isLost ? 'Report a lost item' : 'Report a found item' }}
                    </h1>
                    <p class="mt-3 max-w-xl text-lg leading-relaxed text-slate-300">
                        {{ $isLost
                            ? 'Tell us what you lost. The more details you give, the easier it is to match it with a found item.'
                            : 'Tell us what you found, then drop it off at the Item Drop-Off. Staff keep it safe until the owner claims it.' }}
                    </p>
                </div>

                {{-- What happens next --}}
                <div class="lg:pt-1">
                    <h2 class="font-display text-lg font-bold text-white">What happens next</h2>
                    <ol class="mt-4 space-y-4 text-sm">
                        @foreach ($steps as $i => $step)
                            <li class="flex gap-3">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $i === 0 ? 'bg-[#F5B301] text-[#0F2A4A]' : 'text-white ring-1 ring-inset ring-white/40' }}" aria-hidden="true">{{ $i + 1 }}</span>
                                <span class="pt-0.5 {{ $i === 0 ? 'font-semibold text-white' : 'text-slate-300' }}">{{ $step }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- The form, pulled up over the navy section --}}
    <div class="mx-auto -mt-16 max-w-6xl px-4 pb-12 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_19rem] lg:gap-16">
            <form method="POST" action="{{ route('reports.store', $type) }}" novalidate
                  class="overflow-hidden rounded-2xl bg-white shadow-xl shadow-[#0F2A4A]/15 ring-1 ring-[#0F2A4A]/5">
                @csrf

                <div class="p-5 sm:p-8">
                    @if ($errors->any())
                        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                            Please fix the {{ $errors->count() === 1 ? 'field' : $errors->count().' fields' }} marked below.
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                        {{-- Item name --}}
                        <div class="sm:col-span-2">
                            <label for="item_name" class="text-sm font-semibold text-[#0F2A4A]">Item name</label>
                            <input id="item_name" name="item_name" type="text" maxlength="100" required @unless ($errors->any()) autofocus @endunless
                                   value="{{ old('item_name') }}"
                                   placeholder="Example: black umbrella"
                                   @error('item_name') aria-invalid="true" aria-describedby="item_name-error" @enderror
                                   class="{{ $field('item_name') }}">
                            @error('item_name')
                                <p id="item_name-error" class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label for="category" class="text-sm font-semibold text-[#0F2A4A]">Category</label>
                            <select id="category" name="category" required
                                    @error('category') aria-invalid="true" aria-describedby="category-error" @enderror
                                    class="{{ $field('category') }}">
                                <option value="" disabled @selected(! old('category'))>Choose one</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p id="category-error" class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="date" class="text-sm font-semibold text-[#0F2A4A]">{{ $isLost ? 'When did you lose it?' : 'When did you find it?' }}</label>
                            <input id="date" name="date" type="date" required
                                   value="{{ old('date', now()->toDateString()) }}"
                                   max="{{ now()->toDateString() }}"
                                   @error('date') aria-invalid="true" aria-describedby="date-error" @enderror
                                   class="{{ $field('date') }}">
                            @error('date')
                                <p id="date-error" class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Place --}}
                        <div class="sm:col-span-2">
                            <label for="place" class="text-sm font-semibold text-[#0F2A4A]">{{ $isLost ? 'Where did you lose it?' : 'Where did you find it?' }}</label>
                            <input id="place" name="place" type="text" maxlength="150" required
                                   value="{{ old('place') }}"
                                   placeholder="Example: CITC building, 2nd floor"
                                   @error('place') aria-invalid="true" aria-describedby="place-error" @enderror
                                   class="{{ $field('place') }}">
                            @error('place')
                                <p id="place-error" class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="sm:col-span-2">
                            <label for="description" class="text-sm font-semibold text-[#0F2A4A]">
                                Details <span class="font-normal text-slate-500">(optional)</span>
                            </label>
                            <textarea id="description" name="description" rows="4" maxlength="1000"
                                      placeholder="{{ $isLost ? 'Color, brand, stickers, what was inside' : 'Color, brand, anything that helps the owner know it is theirs' }}"
                                      @error('description') aria-invalid="true" aria-describedby="description-error" @enderror
                                      class="{{ $field('description') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <p id="description-error" class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- FR-07: found items must go to the Item Drop-Off --}}
                        @unless ($isLost)
                            <div class="sm:col-span-2">
                                <label for="drop_off"
                                       class="flex cursor-pointer items-start gap-3 rounded-lg border-2 bg-[#FFF8E1] p-4 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-[#0F2A4A] @error('drop_off') border-red-400 @else border-[#F5B301] @enderror">
                                    <input id="drop_off" name="drop_off" type="checkbox" value="1" required @checked(old('drop_off'))
                                           @error('drop_off') aria-invalid="true" aria-describedby="drop_off-error" @enderror
                                           class="mt-0.5 h-5 w-5 rounded border-[#0F2A4A]/40 text-[#0F2A4A] focus:ring-[#0F2A4A]">
                                    <span>
                                        <span class="block text-sm font-semibold text-[#0F2A4A]">I will drop this item off at the Item Drop-Off</span>
                                        <span class="mt-0.5 block text-sm text-slate-600">Staff record every drop-off, so the owner can only claim it once it's there.</span>
                                    </span>
                                </label>
                                @error('drop_off')
                                    <p id="drop_off-error" class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                                @enderror
                            </div>
                        @endunless
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex flex-wrap items-center gap-3 bg-[#EEF3F9] px-5 py-4 sm:px-8">
                    <button type="submit" class="rounded-lg bg-[#0F2A4A] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2 focus-visible:ring-offset-[#EEF3F9]">
                        {{ $isLost ? 'Send lost item report' : 'Send found item report' }}
                    </button>
                    <a href="{{ route('dashboard') }}" class="rounded-lg px-4 py-2.5 text-sm font-semibold text-[#0F2A4A] hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A]">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>