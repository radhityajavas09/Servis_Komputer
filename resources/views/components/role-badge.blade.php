@props([
    'role',
])

@php
    use App\Enums\RolePengguna;

    $roleEnum = $role instanceof RolePengguna ? $role : RolePengguna::tryFrom((string)$role);
    $roleValue = $roleEnum?->value ?? (string)$role;
    $label = $roleEnum?->label() ?? ucfirst($roleValue);

    $styles = match ($roleValue) {
        'admin' => 'bg-[#EDF7F2] text-[#15573F] border-[#B2DEC6]',
        'petugas' => 'bg-sky-50 text-sky-800 border-sky-200',
        'teknisi' => 'bg-amber-50 text-amber-800 border-amber-200',
        default => 'bg-slate-50 text-slate-700 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {$styles} select-none"]) }}>
    {{ $label }}
</span>
