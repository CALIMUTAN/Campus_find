{{--
    Report Item Form UI (design/cf-003/report-item-form-ui)
    Developed by: Danica Almonia
    Requirements: FR-05, FR-06, FR-07
    Features:
    - Item Name, Category, Date, Location/Place, Description
    - Optional Photo Upload UI layout with live preview (max 2MB JPG/PNG)
    - Drop-off Agreement box for found items (3 school days requirement per FR-07)
    - Field instructions, required-field indicators (*), and error validation spaces
    - Styled with Campus Find theme: Navy (#0F2A4A), Yellow (#F5B301), Light Blue (#EEF3F9)
--}}
@php
    $isLost = $type === 'lost';

    // Input style: light blue fill, turns white with navy border when focused, red on error.
    $field = fn (string $name) => 'mt-1.5 block w-full rounded-xl text-slate-800 placeholder:text-slate-400 focus:bg-white transition duration-150 ease-in-out '
        . ($errors->has($name)
            ? 'border-red-400 bg-red-50 focus:border-red-500 focus:ring-red-500'
            : 'border-[#D5DEEA] bg-[#EEF3F9] focus:border-[#0F2A4A] focus:ring-[#0F2A4A]');

    $steps = $isLost
        ? [
            'Your report shows up on your personal dashboard.',
            'When a matching item is dropped off, you will receive a notification.',
            'Submit a claim. Staff will verify your student ID before releasing the item.'
          ]
        : [
            'Submit this found report and bring the item to the Campus Lost & Found Office within 3 school days.',
            'Staff record the drop-off and change status to "In Custody".',
            'When the owner claims and verifies identity, staff release the item.'
          ];
@endphp

<x-app-layout>
    {{-- Navy top section --}}
    <div class="bg-[#0F2A4A] lg:border-l lg:border-white/10 font-sans">
        <div class="mx-auto max-w-6xl px-4 pb-24 pt-8 sm:px-6 lg:px-8 lg:pt-10">

            {{-- Back link --}}
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 rounded-lg text-sm font-semibold text-slate-300 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                Back to dashboard
            </a>

            <div class="mt-6 grid grid-cols-1 gap-10 lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-16">
                <div>
                    {{-- Switch tabs between Lost and Found forms --}}
                    <nav class="inline-flex rounded-xl bg-white/10 p-1 text-sm font-semibold shadow-inner" aria-label="Report type">
                        <a href="{{ route('reports.create', 'lost') }}" @if ($isLost) aria-current="page" @endif
                           class="rounded-lg px-4 py-2 transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-white {{ $isLost ? 'bg-[#F5B301] text-[#0F2A4A] font-bold shadow-sm' : 'text-slate-200 hover:text-white hover:bg-white/5' }}">
                             I lost an item
                        </a>
                        <a href="{{ route('reports.create', 'found') }}" @unless ($isLost) aria-current="page" @endunless
                           class="rounded-lg px-4 py-2 transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-white {{ ! $isLost ? 'bg-[#F5B301] text-[#0F2A4A] font-bold shadow-sm' : 'text-slate-200 hover:text-white hover:bg-white/5' }}">
                             I found an item
                        </a>
                    </nav>

                    <h1 class="mt-5 font-display text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                        {{ $isLost ? 'Report a Lost Item' : 'Report a Found Item' }}
                    </h1>
                    <p class="mt-3 max-w-xl text-base leading-relaxed text-slate-300 sm:text-lg">
                        {{ $isLost
                            ? 'Fill out the form below with specific details about your missing belonging so we can help match it quickly.'
                            : 'Thank you for being honest! Log the item details here, then bring it to the Campus Drop-Off Office within 3 school days.' }}
                    </p>
                </div>

                {{-- What happens next sidebar info --}}
                <div class="rounded-2xl bg-white/5 p-5 ring-1 ring-white/10 backdrop-blur-sm">
                    <h2 class="font-display text-base font-bold text-white flex items-center gap-2">
                        <span class="inline-block h-2 w-2 rounded-full bg-[#F5B301]"></span>
                        What happens next
                    </h2>
                    <ol class="mt-4 space-y-4 text-xs sm:text-sm">
                        @foreach ($steps as $i => $step)
                            <li class="flex gap-3">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $i === 0 ? 'bg-[#F5B301] text-[#0F2A4A]' : 'bg-white/10 text-slate-200 ring-1 ring-inset ring-white/20' }}" aria-hidden="true">
                                    {{ $i + 1 }}
                                </span>
                                <span class="pt-0.5 {{ $i === 0 ? 'font-medium text-white' : 'text-slate-300' }} leading-snug">
                                    {{ $step }}
                                </span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Form Card --}}
    <div class="mx-auto -mt-16 max-w-6xl px-4 pb-16 sm:px-6 lg:px-8 font-sans">
        <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_20rem] lg:gap-16">
            <form method="POST" action="{{ route('reports.store', $type) }}" enctype="multipart/form-data" novalidate
                  class="overflow-hidden rounded-2xl bg-white shadow-2xl shadow-[#0F2A4A]/10 ring-1 ring-[#0F2A4A]/5">
                @csrf

                <div class="p-6 sm:p-8">
                    {{-- Global error banner if validation fails --}}
                    @if ($errors->any())
                        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 flex items-start gap-3" role="alert">
                            <svg class="h-5 w-5 text-red-500 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-bold">Please check the required fields:</p>
                                <p class="text-xs text-red-700 mt-0.5">Some fields need your attention before submitting.</p>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                        {{-- Item name --}}
                        <div class="sm:col-span-2">
                            <div class="flex justify-between items-baseline">
                                <label for="item_name" class="text-sm font-bold text-[#0F2A4A]">
                                    Item Name <span class="text-red-500" title="Required">*</span>
                                </label>
                                <span class="text-xs text-slate-400">Max 100 characters</span>
                            </div>
                            <input id="item_name" name="item_name" type="text" maxlength="100" required @unless ($errors->any()) autofocus @endunless
                                   value="{{ old('item_name') }}"
                                   placeholder="e.g. Navy Blue Hydro Flask with Stickers, Black Lenovo Laptop"
                                   @error('item_name') aria-invalid="true" aria-describedby="item_name-error" @enderror
                                   class="{{ $field('item_name') }}">
                            <p class="mt-1 text-xs text-slate-500">Provide a clear, brief name for the item.</p>
                            @error('item_name')
                                <p id="item_name-error" class="mt-1.5 text-xs font-semibold text-red-600 flex items-center gap-1">
                                    <span></span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label for="category" class="text-sm font-bold text-[#0F2A4A]">
                                Category <span class="text-red-500" title="Required">*</span>
                            </label>
                            <select id="category" name="category" required
                                    @error('category') aria-invalid="true" aria-describedby="category-error" @enderror
                                    class="{{ $field('category') }}">
                                <option value="" disabled @selected(! old('category'))>Select an item category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Helps students search by item group.</p>
                            @error('category')
                                <p id="category-error" class="mt-1.5 text-xs font-semibold text-red-600 flex items-center gap-1">
                                    <span></span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Date --}}
                        <div>
                            <label for="date" class="text-sm font-bold text-[#0F2A4A]">
                                {{ $isLost ? 'Date Lost' : 'Date Found' }} <span class="text-red-500" title="Required">*</span>
                            </label>
                            <input id="date" name="date" type="date" required
                                   value="{{ old('date', now()->toDateString()) }}"
                                   max="{{ now()->toDateString() }}"
                                   @error('date') aria-invalid="true" aria-describedby="date-error" @enderror
                                   class="{{ $field('date') }}">
                            <p class="mt-1 text-xs text-slate-500">Approximate date the event occurred.</p>
                            @error('date')
                                <p id="date-error" class="mt-1.5 text-xs font-semibold text-red-600 flex items-center gap-1">
                                    <span></span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Place / Location --}}
                        <div class="sm:col-span-2">
                            <div class="flex justify-between items-baseline">
                                <label for="place" class="text-sm font-bold text-[#0F2A4A]">
                                    {{ $isLost ? 'Location Lost' : 'Location Found' }} <span class="text-red-500" title="Required">*</span>
                                </label>
                                <span class="text-xs text-slate-400">Campus location</span>
                            </div>
                            <input id="place" name="place" type="text" maxlength="150" required
                                   value="{{ old('place') }}"
                                   placeholder="e.g. CITC Building Room 302, Gym Bleachers, University Library 2nd Floor"
                                   @error('place') aria-invalid="true" aria-describedby="place-error" @enderror
                                   class="{{ $field('place') }}">
                            <p class="mt-1 text-xs text-slate-500">Be as specific as possible (building name, room number, or landmark).</p>
                            @error('place')
                                <p id="place-error" class="mt-1.5 text-xs font-semibold text-red-600 flex items-center gap-1">
                                    <span></span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="sm:col-span-2">
                            <div class="flex justify-between items-baseline">
                                <label for="description" class="text-sm font-bold text-[#0F2A4A]">
                                    Description & Identifiers <span class="font-normal text-slate-500 text-xs">(optional but recommended)</span>
                                </label>
                                <span class="text-xs text-slate-400">Max 1000 characters</span>
                            </div>
                            <textarea id="description" name="description" rows="4" maxlength="1000"
                                      placeholder="{{ $isLost ? 'Mention brand, color, scratches, keychain attached, case color, or contents to prove ownership.' : 'Describe color, model, condition, or general features. (Do not reveal unique security codes or passwords).' }}"
                                      @error('description') aria-invalid="true" aria-describedby="description-error" @enderror
                                      class="{{ $field('description') }}">{{ old('description') }}</textarea>
                            <p class="mt-1 text-xs text-slate-500">Include distinct characteristics that make the item recognizable.</p>
                            @error('description')
                                <p id="description-error" class="mt-1.5 text-xs font-semibold text-red-600 flex items-center gap-1">
                                    <span></span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Optional Photo Upload Control (FR-05, FR-06) --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-bold text-[#0F2A4A] mb-1">
                                Item Photo <span class="font-normal text-slate-500 text-xs">(optional - JPG, PNG up to 2MB)</span>
                            </label>
                            
                            <div class="mt-1 flex justify-center rounded-xl border-2 border-dashed border-[#D5DEEA] bg-[#EEF3F9]/50 hover:bg-[#EEF3F9] px-6 pt-5 pb-6 transition-colors duration-150 relative">
                                <div class="space-y-2 text-center" id="upload-placeholder">
                                    <svg class="mx-auto h-12 w-12 text-[#0F2A4A]/50" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-slate-600 justify-center">
                                        <label for="photo" class="relative cursor-pointer rounded-md font-semibold text-[#0F2A4A] hover:underline focus-within:outline-none">
                                            <span>Upload a photo</span>
                                            <input id="photo" name="photo" type="file" accept="image/png, image/jpeg, image/jpg" class="sr-only" onchange="previewImage(event)">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-slate-500">Clear photo helps owners quickly verify their item</p>
                                </div>

                                {{-- Image preview container --}}
                                <div id="image-preview-wrapper" class="hidden text-center">
                                    <img id="image-preview" src="#" alt="Uploaded Item Preview" class="mx-auto max-h-48 rounded-lg object-contain shadow-md border border-slate-200">
                                    <button type="button" onclick="removeImage()" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-red-600 hover:text-red-700">
                                        ✕ Remove photo
                                    </button>
                                </div>
                            </div>
                            @error('photo')
                                <p class="mt-1.5 text-xs font-semibold text-red-600 flex items-center gap-1">
                                    <span></span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- FR-07: Found Item Drop-off Agreement Checkbox --}}
                        @unless ($isLost)
                            <div class="sm:col-span-2 mt-2">
                                <label for="drop_off"
                                       class="flex cursor-pointer items-start gap-3.5 rounded-xl border-2 bg-[#FFF8E1] p-4 has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-[#0F2A4A] transition-all @error('drop_off') border-red-400 @else border-[#F5B301] @enderror">
                                    <input id="drop_off" name="drop_off" type="checkbox" value="1" required @checked(old('drop_off'))
                                           @error('drop_off') aria-invalid="true" aria-describedby="drop_off-error" @enderror
                                           class="mt-1 h-5 w-5 rounded border-[#0F2A4A]/40 text-[#0F2A4A] focus:ring-[#0F2A4A]">
                                    <div class="text-xs sm:text-sm">
                                        <span class="block font-bold text-[#0F2A4A]">
                                            Agreement: Drop-off required within 3 school days <span class="text-red-500">*</span>
                                        </span>
                                        <span class="mt-1 block text-slate-700 leading-relaxed">
                                            I agree to surrender this item to the <strong>Campus Lost & Found Office</strong> within <strong>3 school days</strong>. 
                                            I understand the item cannot be verified or claimed by its owner until it is physically deposited in custody.
                                        </span>
                                    </div>
                                </label>
                                @error('drop_off')
                                    <p id="drop_off-error" class="mt-1.5 text-xs font-semibold text-red-600 flex items-center gap-1">
                                        <span></span> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        @endunless

                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 bg-[#EEF3F9] px-6 py-4 sm:px-8">
                    <a href="{{ route('dashboard') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-[#0F2A4A] hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A] transition-colors">
                        Cancel
                    </a>

                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#0F2A4A] px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-[#16396a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] focus-visible:ring-offset-2 transition-all">
                        <span>{{ $isLost ? 'Submit Lost Item Report' : 'Submit Found Item Report' }}</span>
                        <svg class="h-4 w-4 text-[#F5B301]" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Lightweight JavaScript for Image Preview --}}
    <script>
        function previewImage(event) {
            const input = event.target;
            const placeholder = document.getElementById('upload-placeholder');
            const previewWrapper = document.getElementById('image-preview-wrapper');
            const previewImg = document.getElementById('image-preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    placeholder.classList.add('hidden');
                    previewWrapper.classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function removeImage() {
            const input = document.getElementById('photo');
            const placeholder = document.getElementById('upload-placeholder');
            const previewWrapper = document.getElementById('image-preview-wrapper');
            const previewImg = document.getElementById('image-preview');

            input.value = '';
            previewImg.src = '#';
            previewWrapper.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
    </script>
</x-app-layout>