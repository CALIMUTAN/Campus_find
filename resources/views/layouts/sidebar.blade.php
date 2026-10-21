{{--
    Sidebar navigation (design/cf-002/user-dashboard-ui)
    Replaces layouts/navigation.blade.php for logged-in pages.
    TODO (logic branch): replace the '#' links with real routes once they exist.
--}}
@php
    $icons = [
        'home'   => '<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V20a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V9.5"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'file'   => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h4"/>',
        'tag'    => '<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
        'bell'   => '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>',
        'user'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
    ];

    $mainLinks = [
        ['label' => 'Dashboard',     'href' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'home'],
        ['label' => 'Browse items',  'href' => '#', 'active' => false, 'icon' => 'search'],
        ['label' => 'My reports',    'href' => '#', 'active' => false, 'icon' => 'file'],
        ['label' => 'My claims',     'href' => '#', 'active' => false, 'icon' => 'tag'],
        ['label' => 'Notifications', 'href' => '#', 'active' => false, 'icon' => 'bell'],
    ];

    $linkBase = 'group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]';
    $linkOn   = 'bg-white text-[#0F2A4A]';
    $linkOff  = 'text-slate-300 hover:bg-white/10 hover:text-white';
@endphp

{{-- Dark backdrop behind the sidebar on phones --}}
<div x-cloak x-show="sidebarOpen" x-transition.opacity
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-[#0F2A4A]/60 lg:hidden" aria-hidden="true"></div>

<aside id="sidebar"
       class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-[#0F2A4A] px-3 pb-4 transition-transform duration-200 ease-out motion-reduce:transition-none lg:translate-x-0"
       :class="{ '!translate-x-0': sidebarOpen }"
       aria-label="Sidebar">

    {{-- Logo (same as the front page) --}}
    <div class="flex h-16 shrink-0 items-center justify-between px-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301]">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#F5B301] text-[#0F2A4A]" aria-hidden="true">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </span>
            <span class="font-display text-xl font-bold text-white">Campus Find</span>
        </a>

        <button type="button" @click="sidebarOpen = false"
                class="rounded-lg p-1.5 text-slate-300 hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F5B301] lg:hidden"
                aria-label="Close menu">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </div>

    {{-- Main links --}}
    <nav class="mt-6 flex-1 overflow-y-auto" aria-label="Main">
        <ul class="space-y-1">
            @foreach ($mainLinks as $link)
                <li>
                    <a href="{{ $link['href'] }}" @if ($link['active']) aria-current="page" @endif
                       class="{{ $linkBase }} {{ $link['active'] ? $linkOn : $linkOff }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$link['icon']] !!}</svg>
                        {{ $link['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    {{-- Account links, pinned to the bottom --}}
    <ul class="space-y-1 border-t border-white/10 pt-4">
        <li>
            <a href="{{ route('profile.edit') }}" @if (request()->routeIs('profile.*')) aria-current="page" @endif
               class="{{ $linkBase }} {{ request()->routeIs('profile.*') ? $linkOn : $linkOff }}">
                <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons['user'] !!}</svg>
                Profile
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="{{ $linkBase }} {{ $linkOff }} w-full">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons['logout'] !!}</svg>
                    Log out
                </button>
            </form>
        </li>
    </ul>
</aside>