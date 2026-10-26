<x-app-layout title="Dashboard Teknisi" breadcrumb="Dashboard">
    <x-page-header
        eyebrow="PANEL TEKNISI"
        :title="\App\Support\TimeGreeting::greeting() . ', ' . auth()->user()->nama"
        subtitle="Pemeriksaan, diagnosis, dan perbaikan perangkat pelanggan."
    />

    <x-card class="p-8 text-center max-w-xl mx-auto">
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center mx-auto mb-3 border border-amber-100">
            <x-icon name="wrench" class="w-6 h-6" />
        </div>
        <h3 class="text-base font-bold text-[#13251C]">Dashboard Teknisi</h3>
        <p class="text-xs text-[#576B61] mt-1 mb-4">Segera hadir pada Tahap 12 (Antrean servis masuk, status pengerjaan, dan riwayat selesai).</p>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
            <span class="w-2 h-2 rounded-full bg-amber-600 animate-pulse"></span>
            <span>Workbench Aktif</span>
        </div>
        <div class="mt-5">
            <x-button href="{{ route('teknisi.servis.index') }}" variant="primary" icon="wrench">
                Buka Daftar Servis Masuk
            </x-button>
        </div>
    </x-card>
</x-app-layout>
