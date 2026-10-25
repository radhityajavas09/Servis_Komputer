@props([
    'label' => null,
    'name' => null,
    'error' => null,
    'hint' => null,
    'required' => false,
])

<div {{ $attributes->merge(['class' => 'space-y-1.5']) }}>
    @if ($label)
        <label @if($name) for="{{ $name }}" @endif class="block text-xs font-semibold text-[#13251C]">
            {{ $label }}
            @if ($required)
                <span class="text-red-500 ml-0.5">*</span>
            @endif
        </label>
    @endif

    <div>
        {{ $slot }}
    </div>

    @if ($error)
        <p class="text-xs text-red-600 mt-1 flex items-center gap-1 font-medium">
            <x-icon name="alert" class="w-3.5 h-3.5 shrink-0" />
            <span>{{ $error }}</span>
        </p>
    @elseif ($hint)
        <p class="text-xs text-[#576B61] mt-1">{{ $hint }}</p>
    @endif
</div>
