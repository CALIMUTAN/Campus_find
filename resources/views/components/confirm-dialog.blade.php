{{--
    fix/cf-017: reusable confirmation modal, replacing browser confirm().

    Usage: put this inside the <form> the action belongs to. The form
    needs x-data="{ confirming: false }" and an @submit guard so native
    required-field validation still runs before this dialog shows:

        <form method="POST" action="..."
              x-data="{ confirming: false }"
              @submit="if (!confirming) { $event.preventDefault(); confirming = true; }">
            ...fields...
            <button type="submit">Suspend account</button>

            <x-confirm-dialog
                message="Suspend this account? They will be signed out immediately."
                confirm-label="Suspend"
                variant="danger" />
        </form>
--}}
@props([
    'message',
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'variant' => 'primary',
])

<div
    x-show="confirming"
    x-cloak
    x-transition.opacity
    x-on:keydown.escape.window="confirming = false"
    class="fixed inset-0 z-50 flex items-center justify-center px-4"
>
    <div class="fixed inset-0 bg-slate-900/50" x-on:click="confirming = false"></div>

    <div
        x-show="confirming"
        x-transition
        x-on:click.stop
        class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl"
        role="alertdialog"
        aria-modal="true"
    >
        <p class="text-sm leading-relaxed text-slate-700">{{ $message }}</p>

        <div class="mt-5 flex justify-end gap-3">
            <button
                type="button"
                x-on:click="confirming = false"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0F2A4A]">
                {{ $cancelLabel }}
            </button>
            <button
                type="button"
                x-on:click="$event.target.closest('form').submit()"
                class="rounded-lg px-4 py-2 text-sm font-semibold text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2
                    {{ $variant === 'danger' ? 'bg-red-600 hover:bg-red-700 focus-visible:ring-red-600' : 'bg-[#0F2A4A] hover:bg-[#16396a] focus-visible:ring-[#0F2A4A]' }}">
                {{ $confirmLabel }}
            </button>
        </div>
    </div>
</div>
