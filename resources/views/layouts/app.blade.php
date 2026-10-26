@props([
    'title' => 'Dashboard',
    'breadcrumb' => null,
])

@php
    use App\Support\MenuHelper;
    use App\Models\Servis;

    $currentUser = auth()->user();
    $role = $currentUser->role ?? 'admin';
    $menuItems = MenuHelper::getMenuForRole($role);

    // Latest activities for notification bell
    try {
        $latestActivities = Servis::with(['pelanggan'])->latest('updated_at')->take(5)->get();
        $hasRecentActivity = Servis::where('updated_at', '>=', now()->subHours(24))->exists();
    } catch (\Throwable $e) {
        $latestActivities = collect();
        $hasRecentActivity = false;
    }

    $todayFormatted = now()->translatedFormat('l, d M Y');
    $initials = $currentUser?->inisial ?? 'AD';
    $userName = $currentUser?->nama ?? 'Administrator';
    $roleLabel = $currentUser?->role_label ?? 'Administrator';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#F6F8F7]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} &bull; {{ config('app.name', 'Servis Komputer') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="{
        sidebarOpen: false,
        calendarOpen: false,
        notificationsOpen: false,
        profileOpen: false,
    }"
    @keydown.escape.window="sidebarOpen = false; calendarOpen = false; notificationsOpen = false; profileOpen = false"
    class="h-full font-sans text-[#13251C] antialiased flex bg-[#F6F8F7]"
>
    <!-- MOBILE SIDEBAR BACKDROP -->
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        x-cloak
        class="fixed inset-0 bg-[#072117]/40 backdrop-blur-xs z-40 lg:hidden"
    ></div>

    <!-- SIDEBAR (DESKTOP & MOBILE DRAWER) -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-[#E3E8E5] flex flex-col justify-between transition-transform duration-200 ease-in-out lg:static lg:z-auto shrink-0"
    >
        <div>
            <!-- BRAND / LOGO -->
            <div class="h-16 px-6 flex items-center justify-between border-b border-[#E3E8E5]">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#15573F] text-white flex items-center justify-center font-bold text-sm shadow-xs">
                        SK
                    </div>
                    <div>
                        <span class="text-sm font-bold text-[#13251C] tracking-tight block">Servis Komputer</span>
                        <span class="text-[10px] text-[#576B61] block -mt-0.5 tracking-wider uppercase font-semibold">Workspace</span>
                    </div>
                </a>

                <button @click="sidebarOpen = false" type="button" class="lg:hidden text-[#576B61] hover:text-[#13251C] p-1.5 rounded-lg hover:bg-[#F0F4F2]">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <!-- NAVIGATION ITEMS -->
            <div class="p-4 space-y-1">
                <div class="px-3 py-2 text-[10px] font-bold tracking-widest uppercase text-[#84968C]">
                    Menu Utama
                </div>

                @forelse ($menuItems as $item)
                    @php
                        $isActive = false;
                        if (isset($item['active_patterns'])) {
                            foreach ($item['active_patterns'] as $pattern) {
                                if (request()->routeIs($pattern)) {
                                    $isActive = true;
                                    break;
                                }
                            }
                        }
                        $url = \Illuminate\Support\Facades\Route::has($item['route']) ? route($item['route']) : '#';
                    @endphp

                    <a
                        href="{{ $url }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-colors duration-150 {{ $isActive ? 'bg-[#EDF7F2] text-[#15573F] shadow-2xs font-bold' : 'text-[#576B61] hover:text-[#13251C] hover:bg-[#F6F8F7]' }}"
                    >
                        <x-icon :name="$item['icon']" class="w-4.5 h-4.5 shrink-0 {{ $isActive ? 'text-[#15573F]' : 'text-[#84968C]' }}" />
                        <span class="truncate">{{ $item['label'] }}</span>
                        @if ($isActive)
                            <span class="w-1.5 h-1.5 rounded-full bg-[#15573F] ml-auto"></span>
                        @endif
                    </a>
                @empty
                    <a href="{{ url('/') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold bg-[#EDF7F2] text-[#15573F]">
                        <x-icon name="chart" class="w-4.5 h-4.5" />
                        <span>Dashboard</span>
                    </a>
                @endforelse
            </div>
        </div>

        <!-- SIDEBAR FOOTER / USER BADGE -->
        <div class="p-4 border-t border-[#E3E8E5]">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-[#F6F8F7] border border-[#E3E8E5]">
                <div class="w-9 h-9 rounded-xl bg-[#EDF7F2] text-[#15573F] font-bold text-xs flex items-center justify-center border border-[#B2DEC6] shrink-0">
                    {{ $initials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-[#13251C] truncate">{{ $userName }}</p>
                    <p class="text-[10px] text-[#576B61] truncate">{{ $roleLabel }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- TOPBAR -->
        <header class="h-16 bg-white border-b border-[#E3E8E5] px-4 sm:px-6 lg:px-8 flex items-center justify-between sticky top-0 z-30">
            <!-- Left: Mobile Hamburger & Breadcrumb -->
            <div class="flex items-center gap-3 sm:gap-4">
                <button
                    @click="sidebarOpen = true"
                    type="button"
                    class="lg:hidden p-2 rounded-xl text-[#576B61] hover:text-[#13251C] hover:bg-[#F6F8F7] border border-[#E3E8E5] cursor-pointer"
                    aria-label="Buka menu navigasi"
                >
                    <x-icon name="menu" class="w-5 h-5" />
                </button>

                <!-- Breadcrumb -->
                <nav class="flex items-center gap-1.5 text-xs text-[#576B61]">
                    <span class="font-medium hover:text-[#13251C] transition-colors">Workspace</span>
                    <x-icon name="chevron-right" class="w-3.5 h-3.5 text-[#84968C]" />
                    <span class="font-semibold text-[#13251C] truncate max-w-[140px] sm:max-w-xs">{{ $breadcrumb ?? $title }}</span>
                </nav>
            </div>

            <!-- Right: Calendar, Bell, User Avatar -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- KALENDER POPOVER -->
                <div class="relative" x-data="{ open: false }">
                    <button
                        @click="open = !open; notificationsOpen = false; profileOpen = false"
                        type="button"
                        class="p-2 sm:px-3 sm:py-2 rounded-xl border border-[#E3E8E5] text-[#576B61] hover:text-[#13251C] hover:bg-[#F6F8F7] transition-colors flex items-center gap-2 text-xs font-medium cursor-pointer"
                        title="Kalender Hari Ini"
                    >
                        <x-icon name="calendar" class="w-4 h-4 text-[#15573F]" />
                        <span class="hidden md:inline">{{ $todayFormatted }}</span>
                    </button>

                    <!-- Popover Date -->
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        x-cloak
                        class="absolute right-0 mt-2 w-64 bg-white rounded-2xl border border-[#E3E8E5] shadow-lg p-4 z-40"
                    >
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 rounded-xl bg-[#EDF7F2] text-[#15573F] flex items-center justify-center">
                                <x-icon name="calendar" class="w-4 h-4" />
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-[#13251C]">Kalender Servis</h4>
                                <p class="text-[11px] text-[#576B61]">Zona Waktu Asia/Jakarta</p>
                            </div>
                        </div>
                        <div class="p-3 bg-[#F6F8F7] rounded-xl border border-[#E3E8E5] text-center">
                            <span class="text-xs font-semibold text-[#15573F] block">{{ now()->translatedFormat('l') }}</span>
                            <span class="text-2xl font-black text-[#13251C] block my-0.5">{{ now()->format('d') }}</span>
                            <span class="text-xs text-[#576B61] block">{{ now()->translatedFormat('F Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- NOTIFICATION BELL DROPDOWN -->
                <div class="relative" x-data="{ open: false }">
                    <button
                        @click="open = !open; calendarOpen = false; profileOpen = false"
                        type="button"
                        class="relative p-2 rounded-xl border border-[#E3E8E5] text-[#576B61] hover:text-[#13251C] hover:bg-[#F6F8F7] transition-colors cursor-pointer"
                        title="Notifikasi Aktivitas"
                    >
                        <x-icon name="bell" class="w-4.5 h-4.5" />
                        @if ($hasRecentActivity)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                        @endif
                    </button>

                    <!-- Dropdown List -->
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        x-cloak
                        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl border border-[#E3E8E5] shadow-lg overflow-hidden z-40"
                    >
                        <div class="p-4 border-b border-[#E3E8E5] flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-bold text-[#13251C]">Aktivitas Servis Terbaru</h4>
                                <p class="text-[11px] text-[#576B61]">5 pergerakan servis terakhir</p>
                            </div>
                            @if ($hasRecentActivity)
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                                    Ada Update
                                </span>
                            @endif
                        </div>

                        <div class="divide-y divide-[#F0F4F2] max-h-80 overflow-y-auto">
                            @forelse ($latestActivities as $activity)
                                <div class="p-3.5 hover:bg-[#F6F8F7] transition-colors flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-[#EDF7F2] text-[#15573F] flex items-center justify-center shrink-0 mt-0.5">
                                        <x-icon :name="$activity->jenis_perangkat === 'Laptop' ? 'laptop' : 'desktop'" class="w-4 h-4" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 mb-0.5">
                                            <span class="text-xs font-bold text-[#13251C] truncate">{{ $activity->no_servis }}</span>
                                            <x-status-badge :status="$activity->status" class="scale-90 origin-right" />
                                        </div>
                                        <p class="text-xs text-[#576B61] truncate">{{ $activity->merk_tipe }}</p>
                                        <p class="text-[11px] text-[#84968C] mt-1">{{ $activity->pelanggan?->nama }} &bull; {{ $activity->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <x-icon name="clipboard" class="w-8 h-8 text-[#84968C] mx-auto mb-2 opacity-50" />
                                    <p class="text-xs font-medium text-[#576B61]">Belum ada aktivitas servis tercatat</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- USER AVATAR & DROPDOWN -->
                <div class="relative" x-data="{ open: false }">
                    <button
                        @click="open = !open; calendarOpen = false; notificationsOpen = false"
                        type="button"
                        class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-[#F6F8F7] transition-colors cursor-pointer select-none"
                    >
                        <div class="w-9 h-9 rounded-full bg-[#EDF7F2] text-[#15573F] font-bold text-xs flex items-center justify-center border border-[#B2DEC6] shadow-2xs">
                            {{ $initials }}
                        </div>
                        <x-icon name="chevron-down" class="w-3.5 h-3.5 text-[#576B61] hidden sm:block" />
                    </button>

                    <!-- Dropdown Content -->
                    <div
                        x-show="open"
                        @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        x-cloak
                        class="absolute right-0 mt-2 w-56 bg-white rounded-2xl border border-[#E3E8E5] shadow-lg p-2 z-40"
                    >
                        <div class="px-3 py-2.5 border-b border-[#F0F4F2]">
                            <p class="text-xs font-bold text-[#13251C] truncate">{{ $userName }}</p>
                            <span class="inline-block mt-1">
                                <x-role-badge :role="$role" />
                            </span>
                        </div>

                        <div class="py-1">
                            <a
                                href="{{ \Illuminate\Support\Facades\Route::has('profil.edit') ? route('profil.edit') : '#' }}"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-[#576B61] hover:text-[#13251C] hover:bg-[#F6F8F7] transition-colors"
                            >
                                <x-icon name="user" class="w-4 h-4 text-[#84968C]" />
                                <span>Ubah Profil</span>
                            </a>
                        </div>

                        <div class="pt-1 border-t border-[#F0F4F2]">
                            <form action="{{ \Illuminate\Support\Facades\Route::has('logout') ? route('logout') : '#' }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-medium text-red-600 hover:bg-red-50 transition-colors text-left cursor-pointer"
                                >
                                    <x-icon name="log-out" class="w-4 h-4" />
                                    <span>Keluar (Logout)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT CONTAINER -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- GLOBAL TOAST NOTIFICATION -->
    <x-toast />

    @stack('scripts')
</body>
</html>

