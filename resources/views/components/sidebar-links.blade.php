@php
    // Daftar link dengan penyesuaian route name berdasarkan role pengguna
    $links = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'permission' => 'read dashboard'],
        ['label' => 'Pengguna', 'route' => 'pengguna.*', 'permission' => 'read pengguna'],
        ['label' => 'Fasilitas', 'route' => 'fasilitas.*', 'permission' => 'read fasilitas'],
        [
            'label' => 'Manajemen Fasilitas',
            'route' => 'manajemen-fasilitas.*',
            'permission' => 'read manajemen fasilitas',
        ],
        ['label' => 'Pengajuan Peminjaman', 'route' => 'pengajuan.*', 'permission' => 'read pengajuan peminjaman'],
        [
            'label' => 'Konfirmasi Peminjaman',
            'route' => 'konfirmasi-peminjaman.*',
            'permission' => 'read konfirmasi peminjaman',
        ],
        ['label' => 'Pembayaran', 'route' => 'pembayaran.*', 'permission' => 'read pembayaran'],
        ['label' => 'Laporan', 'route' => 'laporan.*', 'permission' => 'read laporan'],
    ];
@endphp

@foreach ($links as $link)
    @if ($link['permission'] === null || auth()->user()->can($link['permission']))
        @php
            $isActive = request()->routeIs($link['route']);
        @endphp
        <a href="{{ route(str_replace('.*', '.index', $link['route'])) }}"
            class="flex items-center gap-4 px-4 py-2 rounded-md transition sidebar-link
               hover:bg-gray-800 hover:text-white
               {{ $isActive ? 'bg-gray-800 text-white' : '' }}">
            <span class="sidebar-text">{{ $link['label'] }}</span>
        </a>
    @endif
@endforeach
