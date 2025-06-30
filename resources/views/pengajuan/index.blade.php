@extends('layouts.app')

@section('header', 'Form Pengajuan Peminjaman')

@section('content')
    <div x-data="formPeminjaman(@js($fasilitas), @js($unitFasilitas), @js($tarif), @js($tanggalPeminjaman))" x-init="init()" class="p-4 sm:p-6 md:p-8 rounded shadow max-w-4xl mx-auto">
        <form method="POST" action="{{ route('pengajuan.store') }}" class="space-y-4">
            @csrf

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-4 rounded">
                    <strong>Terjadi kesalahan!</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Jenis Fasilitas --}}
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <label class="md:w-1/3 font-semibold">Jenis Fasilitas</label>
                <select x-model="selectedJenis" @change="filterFasilitas" name="jenis_fasilitas_id"
                    class="w-full md:w-2/3 rounded-md border-gray-300 bg-secondary shadow-sm text-gray-500" required>
                    <option value="">-- Pilih Jenis Fasilitas --</option>
                    @foreach ($jenisFasilitas as $jenis)
                        <option value="{{ $jenis->id }}">{{ $jenis->nama }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fasilitas --}}
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <label class="md:w-1/3 font-semibold">Fasilitas</label>
                <select x-model="selectedFasilitas" @change="filterUnitAndTarif" name="fasilitas_id"
                    class="w-full md:w-2/3 rounded-md border-gray-300 bg-secondary shadow-sm text-gray-500" required>
                    <option value="">-- Pilih Fasilitas --</option>
                    <template x-for="f in filteredFasilitas" :key="f.id">
                        <option :value="f.id" x-text="f.nama"></option>
                    </template>
                    <template x-if="filteredFasilitas.length === 0">
                        <option disabled>-- Tidak Ada Pilihan --</option>
                    </template>
                </select>
            </div>

            {{-- Unit Fasilitas --}}
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <label class="md:w-1/3 font-semibold">Unit Fasilitas</label>
                <select x-model="selectedUnit" @change="filterTarif" name="unit_fasilitas_id"
                    class="w-full md:w-2/3 rounded-md border-gray-300 bg-secondary shadow-sm text-gray-500">
                    <option value="">-- Pilih Unit --</option>
                    <template x-for="d in filteredUnitFasilitas" :key="d.id">
                        <option :value="d.id" x-text="d.nama"></option>
                    </template>
                    <template x-if="filteredUnitFasilitas.length === 0">
                        <option disabled>-- Tidak Ada Pilihan --</option>
                    </template>
                </select>
            </div>

            {{-- Tarif --}}
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <label class="md:w-1/3 font-semibold">Tarif</label>
                <select x-model="selectedTarif" name="tarif_id"
                    class="w-full md:w-2/3 rounded-md border-gray-300 bg-secondary shadow-sm text-gray-500" required>
                    <option value="">-- Pilih Tarif --</option>
                    <template x-for="t in filteredTarif" :key="t.id">
                        <option :value="t.id" x-text="`${t.kelompok_tarif} - Rp ${formatRupiah(t.harga)}`">
                        </option>
                    </template>
                    <template x-if="filteredTarif.length === 0">
                        <option disabled>-- Tidak Ada Pilihan --</option>
                    </template>
                </select>
            </div>

            <h2 class="font-bold text-lg mt-8 mb-4">Lama Sewa</h2>

            {{-- Tanggal Peminjaman --}}
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <label class="md:w-1/3 font-semibold">Tanggal Peminjaman</label>
                <x-text-input x-ref="tanggalPeminjaman" type="text" name="tanggal_peminjaman"
                    class="w-full md:w-2/3 px-3 py-2 border rounded" placeholder="Pilih tanggal" required />
            </div>


            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <label class="md:w-1/3 font-semibold">Tanggal Pengembalian</label>
                <x-text-input x-ref="tanggalPengembalian" type="text" name="tanggal_pengembalian"
                    class="w-full md:w-2/3 px-3 py-2 border rounded" placeholder="Pilih tanggal" required autocomplete="off"
                    readonly />
            </div>

            {{-- Tanggal Pemasangan --}}
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <label class="md:w-1/3 font-semibold">Pemasangan Peralatan Acara</label>
                <x-text-input x-ref="tanggalPemasangan" type="text" name="tanggal_pemasangan_alat"
                    class="w-full md:w-2/3 border rounded px-3 py-2" placeholder="Pilih tanggal" required />
            </div>

            {{-- Tanggal Pembongkaran --}}
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <label class="md:w-1/3 font-semibold">Pembongkaran Peralatan Acara</label>
                <x-text-input x-ref="tanggalPembongkaran" type="text" name="tanggal_pembongkaran_alat"
                    class="w-full md:w-2/3 border rounded px-3 py-2" placeholder="Pilih tanggal" required />
            </div>

            {{-- Layanan Eksternal --}}
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <label class="md:w-1/3 font-semibold">Layanan Eksternal</label>
                <x-text-input type="text" name="layanan_eksterna" class="w-full md:w-2/3 border rounded px-3 py-2" />
            </div>

            {{-- Checkbox --}}
            <div class="flex flex-col md:flex-row items-start md:items-center gap-2 md:justify-end">
                <label for="setuju_sk" class="text-sm text-gray-700">
                    Saya telah membaca dan menyetujui <span class="font-semibold">Syarat & Ketentuan</span> peminjaman
                </label>
                <input type="checkbox" id="setuju_sk" name="setuju_sk" class="w-5 h-5 text-blue-600 border-gray-300 rounded"
                    required>
            </div>

            <div class="text-right">
                <x-primary-button type="submit" class="text-sm font-normal rounded-md">Ajukan</x-primary-button>
            </div>
        </form>
    </div>

    {{-- Alpine & Flatpickr --}}
    <script>
        function formPeminjaman(fasilitas = {}, unitFasilitas = {}, tarif = {}, tanggalPeminjaman = []) {
            console.log(tanggalPeminjaman)
            return {
                fasilitas,
                unitFasilitas,
                tarif,
                tanggalPeminjaman,

                selectedJenis: '',
                selectedFasilitas: '',
                selectedUnit: '',
                selectedTarif: '',

                filteredFasilitas: [],
                filteredUnitFasilitas: [],
                filteredTarif: [],

                fpTanggalPeminjaman: "",
                fpTanggalPengembalian: "",

                init() {
                    this.filterFasilitas();

                    flatpickr(this.$refs.tanggalPemasangan, {
                        dateFormat: "Y-m-d",
                        minDate: "today",
                        altFormat: "d F Y",

                    });

                    flatpickr(this.$refs.tanggalPembongkaran, {
                        dateFormat: "Y-m-d",
                        minDate: "today",
                        altFormat: "d F Y",

                    });
                },

                filterFasilitas() {
                    this.selectedFasilitas = '';
                    this.selectedUnit = '';
                    this.selectedTarif = '';
                    this.filteredFasilitas = this.fasilitas.filter(f => f.jenis_fasilitas_id == this.selectedJenis);
                    this.filterUnitAndTarif();
                },

                filterUnitAndTarif() {
                    this.selectedUnit = '';
                    this.selectedTarif = '';
                    this.filteredUnitFasilitas = this.unitFasilitas.filter(d => d.fasilitas_id == this
                        .selectedFasilitas);
                    this.filterTarif();
                },
                filterTarif() {
                    if (this.selectedUnit) {
                        this.filteredTarif = this.tarif.filter(t => t.unit_fasilitas_id == this.selectedUnit);
                    } else {
                        this.filteredTarif = this.tarif.filter(t => t.fasilitas_id == this.selectedFasilitas && t
                            .unit_fasilitas_id === null);
                    }
                    this.setupTanggalDisabled()
                },
                setupTanggalDisabled() {
                    const tanggalDisabled = this.tanggalPeminjaman
                        .filter(tp => tp.fasilitas_id == this.selectedFasilitas &&
                            (tp.unit_fasilitas_id === null || tp.unit_fasilitas_id == this.selectedUnit))
                        .map(tp => ({
                            from: tp.from,
                            to: tp.to
                        }));

                    if (this.fpTanggalPeminjaman) this.fpTanggalPeminjaman.destroy();
                    if (this.fpTanggalPengembalian) this.fpTanggalPengembalian.destroy();

                    this.fpTanggalPeminjaman = flatpickr(this.$refs.tanggalPeminjaman, {
                        dateFormat: "Y-m-d",
                        minDate: "today",
                        disable: tanggalDisabled,
                        altFormat: "d F Y",
                    });

                    this.fpTanggalPengembalian = flatpickr(this.$refs.tanggalPengembalian, {
                        dateFormat: "Y-m-d",
                        minDate: "today",
                        disable: tanggalDisabled,
                        altFormat: "d F Y",
                    });
                },
                formatRupiah(value) {
                    return new Intl.NumberFormat('id-ID').format(value);
                }
            };
        }
    </script>
@endsection
