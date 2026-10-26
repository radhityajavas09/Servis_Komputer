<x-app-layout title="Dashboard Petugas Servis" breadcrumb="Dashboard">
    <x-page-header
        eyebrow="PANEL PETUGAS SERVIS"
        :title="\App\Support\TimeGreeting::greeting() . ', ' . auth()->user()->nama"
        subtitle="Kelola pelayanan pelanggan, pendaftaran servis, dan penyerahan perangkat."
    />

    <x-card class="p-8 text-center max-w-xl mx-auto">
        <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-700 flex items-center justify-center mx-auto mb-3 border border-sky-100">
            <x-icon name="user" class="w-6 h-6" />
        </div>
        <h3 class="text-base font-bold text-[#13251C]">Dashboard Petugas Servis</h3>
        <p class="text-xs text-[#576B61] mt-1 mb-4">Segera hadir pada Tahap 12 (Statistik servis hari ini, siap diambil, dan antrean penyerahan).</p>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-sky-50 text-sky-800 border border-sky-200">
            <span class="w-2 h-2 rounded-full bg-sky-600 animate-pulse"></span>
            <span>Autentikasi & Hak Akses Berhasil</span>
        </div>
    </x-card>
</x-app-layout>
