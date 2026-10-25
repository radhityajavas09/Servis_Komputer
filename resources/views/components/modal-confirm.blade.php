@props([
    'name',
    'title' => 'Konfirmasi Tindakan',
    'confirmText' => 'Ya, Lanjutkan',
    'confirmVariant' => 'danger',
])

<div
    x-data="{
        open: false,
        actionUrl: '',
        message: '',
        show(url = '', msg = '') {
            this.actionUrl = url;
            if (msg) this.message = msg;
            this.open = true;
        },
        close() {
            this.open = false;
        }
    }"
    x-on:open-modal-confirm.window="if ($event.detail.name === '{{ $name }}') show($event.detail.url, $event.detail.message)"
    x-on:keydown.escape.window="close()"
    x-cloak
    class="relative z-50"
>
    <!-- Backdrop -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-[#072117]/40 backdrop-blur-xs transition-opacity"
        @click="close()"
    ></div>

    <!-- Modal Dialog -->
    <div
        x-show="open"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        class="fixed inset-0 z-10 flex items-center justify-center p-4 sm:p-6"
    >
        <div
            @click.stop
            class="relative w-full max-w-md bg-white rounded-2xl border border-[#E3E8E5] shadow-xl p-6 overflow-hidden"
        >
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-100">
                    <x-icon name="alert" class="w-5 h-5" />
                </div>
                <div class="flex-1">
                    <h3 class="text-base font-bold text-[#13251C]">{{ $title }}</h3>
                    <p class="text-xs sm:text-sm text-[#576B61] mt-1.5" x-text="message || '{{ $slot->isEmpty() ? 'Apakah Anda yakin ingin melanjutkan tindakan ini? Tindakan ini tidak dapat dibatalkan.' : '' }}'">
                        {{ $slot }}
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-[#F0F4F2]">
                <x-button variant="secondary" size="sm" @click="close()">
                    Batal
                </x-button>

                <form :action="actionUrl" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <x-button :variant="$confirmVariant" size="sm" type="submit">
                        {{ $confirmText }}
                    </x-button>
                </form>
            </div>
        </div>
    </div>
</div>
