<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F6F8F7]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan &bull; Servis Komputer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-[#13251C] antialiased flex flex-col justify-between p-6">
    <div class="max-w-md w-full mx-auto my-auto text-center">
        <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs p-8 sm:p-10">
            <div class="w-14 h-14 rounded-2xl bg-[#EDF7F2] text-[#15573F] flex items-center justify-center mx-auto mb-4 border border-[#B2DEC6]">
                <x-icon name="search" class="w-7 h-7" />
            </div>

            <span class="text-xs font-bold tracking-widest uppercase text-[#15573F]">Error 404 &bull; Not Found</span>
            <h1 class="text-2xl font-bold tracking-tight text-[#13251C] mt-1 mb-2">Halaman Tidak Ditemukan</h1>
            <p class="text-xs sm:text-sm text-[#576B61] leading-relaxed mb-6">
                Halaman yang Anda tuju tidak ditemukan, telah dipindahkan, atau alamat URL yang dimasukkan salah.
            </p>

            <div class="flex items-center justify-center gap-3">
                <x-button variant="primary" href="{{ url('/') }}" icon="chart">
                    Kembali ke Beranda
                </x-button>
            </div>
        </div>
    </div>

    <footer class="text-center text-xs text-[#576B61] py-4">
        &copy; {{ date('Y') }} Servis Komputer &bull; Sistem Manajemen Pelayanan Servis
    </footer>
</body>
</html>
