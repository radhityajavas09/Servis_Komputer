<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Cetak Dokumen - Servis Komputer' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 15mm;
            }
        }
    </style>
</head>
<body class="font-sans text-[#13251C] antialiased bg-[#F6F8F7] min-h-full py-8 px-4 sm:px-6">
    <div class="max-w-3xl mx-auto">
        <!-- Control buttons for print and return -->
        <div class="no-print mb-6 flex items-center justify-between gap-3">
            <a href="{{ $backUrl ?? url()->previous() }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#576B61] hover:text-[#13251C] transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" />
                <span>Kembali</span>
            </a>

            <div class="flex items-center gap-2.5">
                <button onclick="window.print()" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-[#15573F] text-white hover:bg-[#104430] transition-colors shadow-xs cursor-pointer">
                    <x-icon name="printer" class="w-4 h-4" />
                    <span>Cetak Dokumen / Simpan PDF</span>
                </button>
            </div>
        </div>

        <!-- Printable Document Canvas -->
        <div class="bg-white rounded-2xl border border-[#E3E8E5] p-8 sm:p-12 shadow-sm print:shadow-none print:border-none print:p-0">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
