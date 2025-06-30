@extends('layouts.app')

@section('header')
    Fasilitas | Tambah Fasilitas
@endsection

@section('content')
    <div x-data="fasilitasForm()">

        <form method="POST" action="{{ route('fasilitas.store') }}" enctype="multipart/form-data">
            @csrf

            @include('fasilitas.partials.form-error')
            <div class="mb-2">
                <h2 class="font-bold text-lg">Data Fasilitas</h2>
                @include('fasilitas.partials.form-fasilitas', ['jenisFasilitas' => $jenisFasilitas])
            </div>
            <div class="mb-2 mt-8">
                <h2 class="font-bold text-lg">Upload Gambar</h2>
                @include('fasilitas.partials.form-upload-gambar')
            </div>
            <div x-show="!showUnit" class="mt-8">
                <x-secondary-button type="button" @click="aktifkanUnit()" class="text-sm rounded-sm">+ Tambah Unit
                    Fasilitas</x-secondary-button>
            </div>
            <div class="mb-2 mt-8" x-show="!showUnit">
                <h2 class="font-bold text-lg">Data Tarif</h2>
                @include('fasilitas.partials.form-tarif')
            </div>
            <div class="mb-2 mt-8" x-show="showUnit">
                <h2 class="font-bold text-lg">Data Unit Fasilitas</h2>
                @include('fasilitas.partials.form-unit-fasilitas')
            </div>


            <div class="flex items-center justify-end gap-2 mt-10">
                <x-secondary-button class="bg-red-600 hover:bg-red-500 font-normal rounded-md">
                    <a href={{ route('fasilitas.index') }}>Keluar</a>
                </x-secondary-button>
                <x-secondary-button type="submit" class=" font-normal rounded-md">
                    Simpan
                </x-secondary-button>
            </div>
        </form>
    </div>


    <script>
        function fasilitasForm() {
            return {
                fasilitas: {
                    jenis_fasilitas_id: '',
                    nama: '',
                    unit: '',
                    luas: '',
                    lama_sewa: '',
                    fitur: ['']
                },
                fiturBaru: '',
                tarif: {
                    eksternal: '',
                    internal: '',
                    sosial: ''
                },
                showUnit: false,
                unitList: [],

                formatRupiah(value) {
                    if (!value) return '';
                    return 'Rp' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },

                parseRupiah(value) {
                    return parseInt(value.replace(/\D/g, '') || 0);
                },

                aktifkanUnit() {
                    this.showUnit = true;
                    this.tarif = {
                        eksternal: '',
                        internal: '',
                        sosial: ''
                    };
                    this.tambahUnit();
                },

                tambahUnit() {
                    this.unitList.push({
                        nama: '',
                        unit: '',
                        luas: '',
                        lama_sewa: '',
                        tarif: {
                            eksternal: '',
                            internal: '',
                            sosial: ''
                        }
                    });
                },

                hapusUnit(index) {
                    this.unitList.splice(index, 1);
                    if (this.unitList.length === 0) {
                        this.showUnit = false;
                    }
                },

                tambahFitur() {
                    if (this.fiturBaru.trim() !== '') {
                        this.form.fitur.push(this.fiturBaru.trim());
                        this.fiturBaru = '';
                    }
                },

                hapusFitur(index) {
                    this.form.fitur.splice(index, 1);
                }
            }
        }
    </script>
@endsection
