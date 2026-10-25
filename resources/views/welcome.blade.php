<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F7F9F8]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Servis Komputer siap - Sistem Manajemen Pelayanan Servis</title>
    
    <!-- Fonts & Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-[#112019] antialiased flex flex-col justify-between p-6 sm:p-10">
    <header class="max-w-4xl w-full mx-auto flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#15573F] flex items-center justify-center text-white font-bold text-lg shadow-sm">
                SK
            </div>
            <div>
                <h1 class="text-base font-semibold text-[#112019] tracking-tight">Servis Komputer</h1>
                <p class="text-xs text-[#576961]">Sistem Manajemen Pelayanan Servis</p>
            </div>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium bg-[#EDF7F2] text-[#15573F] border border-[#B2DEC6]">
            <span class="w-2 h-2 rounded-full bg-[#15573F] animate-pulse"></span>
            Tahap 1 Siap
        </div>
    </header>

    <main class="max-w-4xl w-full mx-auto my-auto py-12">
        <div class="bg-white rounded-2xl border border-[#E3E9E5] shadow-xs p-8 sm:p-12 text-center max-w-xl mx-auto">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#EDF7F2] text-[#15573F] mb-6">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            
            <p class="text-xs font-bold uppercase tracking-widest text-[#15573F] mb-2 flex items-center justify-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-[#15573F]"></span>
                Inisialisasi Sistem
            </p>
            
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-[#112019] mb-3">
                Servis Komputer siap
            </h2>
            
            <p class="text-sm text-[#576961] leading-relaxed mb-8">
                Infrastruktur Laravel 12, MySQL Laragon, Tailwind CSS 4, dan Alpine.js telah terpasang dan siap digunakan untuk tahap berikutnya.
            </p>

            <!-- Interaktivitas Alpine.js -->
            <div x-data="{ connected: true, toggle() { this.connected = !this.connected } }" class="pt-6 border-t border-[#E3E9E5] text-left">
                <div class="flex items-center justify-between p-3.5 rounded-xl bg-[#F7F9F8] border border-[#E3E9E5]">
                    <div class="flex items-center gap-2.5">
                        <span :class="connected ? 'bg-emerald-500' : 'bg-amber-500'" class="w-2.5 h-2.5 rounded-full transition-colors"></span>
                        <span class="text-xs font-semibold text-[#112019]" x-text="connected ? 'Alpine.js & Tooling: Aktif' : 'Status: Dijeda'"></span>
                    </div>
                    <button @click="toggle()" type="button" class="text-xs font-medium text-[#15573F] hover:text-[#0C3627] underline cursor-pointer">
                        Tes Interaksi
                    </button>
                </div>
            </div>
        </div>
    </main>

    <footer class="max-w-4xl w-full mx-auto text-center text-xs text-[#576961]">
        &copy; {{ date('Y') }} Servis Komputer &bull; Lingkungan Lokal Laragon
    </footer>
</body>
</html>
