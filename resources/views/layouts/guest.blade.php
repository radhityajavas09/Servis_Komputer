<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F6F8F7]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Servis Komputer') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-[#13251C] antialiased flex flex-col justify-between p-4 sm:p-6">
    <div class="w-full {{ $maxWidth ?? 'max-w-md' }} mx-auto my-auto py-8">
        <!-- Logo / Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-[#15573F] text-white font-bold text-xl shadow-xs mb-3">
                SK
            </div>
            <h1 class="text-xl font-bold tracking-tight text-[#13251C]">Servis Komputer</h1>
            <p class="text-xs text-[#576B61] mt-0.5">Sistem Manajemen Pelayanan Servis</p>
        </div>

        @if(isset($rawSlot) && $rawSlot)
            {{ $slot }}
        @else
            <!-- Main Card -->
            <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs p-6 sm:p-8">
                {{ $slot }}
            </div>
        @endif
    </div>

    <footer class="text-center text-xs text-[#576B61] py-4">
        &copy; {{ date('Y') }} Servis Komputer &bull; Seluruh hak cipta dilindungi
    </footer>

    <x-toast />
</body>
</html>
