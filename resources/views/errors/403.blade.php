<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F6F8F7]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak &bull; Servis Komputer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans text-[#13251C] antialiased flex flex-col justify-between p-6">
    <div class="max-w-md w-full mx-auto my-auto text-center">
        <div class="bg-white rounded-2xl border border-[#E3E8E5] shadow-xs p-8 sm:p-10">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4 border border-amber-200">
                <x-icon name="alert" class="w-7 h-7" />
            </div>

            <span class="text-xs font-bold tracking-widest uppercase text-amber-600">Error 403 &bull; Forbidden</span>
            <h1 class="text-2xl font-bold tracking-tight text-[#13251C] mt-1 mb-2">Akses Ditolak</h1>
            <p class="text-xs sm:text-sm text-[#576B61] leading-relaxed mb-6">
                {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses atau izin yang sesuai untuk membuka halaman ini.' }}
            </p>

            <div class="flex items-center justify-center gap-3">
                <x-button variant="primary" href="{{ url('/') }}" icon="chart">
                    Kembali ke Dashboard
                </x-button>
            </div>
        </div>
    </div>

    <footer class="text-center text-xs text-[#576B61] py-4">
        &copy; {{ date('Y') }} Servis Komputer &bull; Sistem Manajemen Pelayanan Servis
    </footer>
</body>
</html>
