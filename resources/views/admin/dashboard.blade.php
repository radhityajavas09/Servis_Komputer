<x-app-layout title="Dashboard Administrator" breadcrumb="Dashboard">
    <x-page-header
        eyebrow="PANEL ADMINISTRATOR"
        :title="\App\Support\TimeGreeting::greeting() . ', ' . auth()->user()->nama"
        subtitle="Berikut ringkasan aktivitas servis di bengkel Anda."
    />

    <x-card class="p-8 text-center max-w-xl mx-auto">
        <div class="w-12 h-12 rounded-2xl bg-[#EDF7F2] text-[#15573F] flex items-center justify-center mx-auto mb-3">
            <x-icon name="chart" class="w-6 h-6" />
        </div>
        <h3 class="text-base font-bold text-[#13251C]">Dashboard Administrator</h3>
        <p class="text-xs text-[#576B61] mt-1 mb-4">Segera hadir pada Tahap 12 (Statistik lengkap, ringkasan aktivitas, dan ekspor data).</p>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#EDF7F2] text-[#15573F] border border-[#B2DEC6]">
            <span class="w-2 h-2 rounded-full bg-[#15573F] animate-pulse"></span>
            <span>Autentikasi & Hak Akses Berhasil</span>
        </div>
    </x-card>
</x-app-layout>
