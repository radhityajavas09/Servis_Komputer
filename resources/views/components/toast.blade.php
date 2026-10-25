<div
    x-data="{
        show: false,
        type: 'success',
        message: '',
        timeout: null,
        trigger(type, message) {
            this.type = type;
            this.message = message;
            this.show = true;
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => { this.show = false }, 4000);
        },
        init() {
            @if (session('success'))
                this.trigger('success', '{{ addslashes(session('success')) }}');
            @elseif (session('error'))
                this.trigger('error', '{{ addslashes(session('error')) }}');
            @endif
        }
    }"
    x-on:show-toast.window="trigger($event.detail.type || 'success', $event.detail.message)"
    x-cloak
    class="fixed bottom-5 right-5 z-50 max-w-sm w-full pointer-events-none"
>
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-3 sm:translate-y-0 sm:translate-x-3"
        x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
        x-transition:leave-end="opacity-0 translate-y-3 sm:translate-y-0 sm:translate-x-3"
        class="pointer-events-auto rounded-2xl bg-white p-4 shadow-lg border border-[#E3E8E5] flex items-start gap-3"
    >
        <template x-if="type === 'success'">
            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                <x-icon name="check-circle" class="w-4.5 h-4.5" />
            </div>
        </template>
        <template x-if="type === 'error'">
            <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0 border border-red-100">
                <x-icon name="alert" class="w-4.5 h-4.5" />
            </div>
        </template>

        <div class="flex-1 pt-0.5">
            <p class="text-xs font-bold text-[#13251C]" x-text="type === 'success' ? 'Berhasil' : 'Pemberitahuan'"></p>
            <p class="text-xs text-[#576B61] mt-0.5" x-text="message"></p>
        </div>

        <button @click="show = false" type="button" class="text-[#84968C] hover:text-[#13251C] p-1 rounded-lg hover:bg-[#F0F4F2] transition-colors cursor-pointer">
            <x-icon name="x" class="w-4 h-4" />
        </button>
    </div>
</div>
