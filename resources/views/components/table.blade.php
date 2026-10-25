<div {{ $attributes->merge(['class' => 'w-full overflow-hidden bg-white rounded-2xl border border-[#E3E8E5] shadow-xs']) }}>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-[#13251C]">
            {{ $slot }}
        </table>
    </div>
</div>
