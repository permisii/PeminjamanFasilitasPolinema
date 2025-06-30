@extends('layouts.app')

@section('header')
    Tambah Fasilitas
@endsection

@section('content')
    <div x-data="fasilitasForm(@js($fasilitas))">
        <form method="POST" action="{{ route('fasilitas.update', $fasilitas->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('fasilitas.partials.form-error')
            <div class="mb-2">
                <h2 class="font-bold text-lg">Data Fasilitas</h2>
                @include('fasilitas.partials.form-fasilitas', [
                    'jenisFasilitas' => $jenisFasilitas,
                    'fasilitas' => $fasilitas,
                ])
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
        function fasilitasForm(fasilitas = {}) {
            return {
                fasilitas: {
                    jenis_fasilitas_id: fasilitas.jenis_fasilitas_id || '',
                    nama: fasilitas.nama || '',
                    unit: fasilitas.unit || '',
                    luas: fasilitas.luas || '',
                    lama_sewa: fasilitas.lama_sewa || '',
                    fitur: Array.isArray(fasilitas.fitur) && fasilitas.fitur.length > 0 ?
                        fasilitas.fitur : ['']
                },
                fiturBaru: '',
                tarif: (fasilitas.tarif || []).reduce((obj, item) => {
                    obj[item.kelompok_tarif] = item.harga;
                    return obj;
                }, {
                    eksternal: '',
                    internal: '',
                    sosial: ''
                }),
                showUnit: Array.isArray(fasilitas.unit_fasilitas) && fasilitas.unit_fasilitas.length > 0,
                unitList: Array.isArray(fasilitas.unit_fasilitas) ?
                    fasilitas.unit_fasilitas.map(unit => {
                        const tarifObj = Array.isArray(unit.tarif) ?
                            unit.tarif.reduce((obj, item) => {
                                obj[item.kelompok_tarif] = item.harga;
                                return obj;
                            }, {}) :
                            unit.tarif || {};
                        return {
                            id: unit.id || 0,
                            nama: unit.nama || '',
                            unit: unit.unit || '',
                            luas: unit.luas || '',
                            lama_sewa: unit.lama_sewa || '',
                            tarif: {
                                eksternal: tarifObj.eksternal || '',
                                internal: tarifObj.internal || '',
                                sosial: tarifObj.sosial || ''
                            }
                        };
                    }) : [],

                formatRupiah(value) {
                    if (!value) return '';
                    return 'Rp' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                },
                parseRupiah(value) {
                    return parseInt(value.toString().replace(/[^\d]/g, '')) || 0;
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
                    const item = this.unitList[index];

                    if (item.id) {
                        if (confirm("Apakah yakin ingin menghapus data ini dari database?")) {
                            fetch(`/fasilitas/unit/${item.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                            'content')
                                    }
                                })
                                .then(res => {
                                    if (res.ok) {
                                        this.unitList.splice(index, 1);
                                        if (this.unitList.length === 0) {
                                            this.showUnit = false;
                                        }
                                        alert('Berhasil dihapus dari database');
                                    } else {
                                        alert('Gagal menghapus data dari database');
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    alert('Terjadi kesalahan saat menghapus data');
                                });
                        }
                    } else {
                        // Kalau data belum disimpan di database
                        this.unitList.splice(index, 1);
                        if (this.unitList.length === 0) {
                            this.showUnit = false;
                        }
                    }
                },
                tambahFitur() {
                    if (this.fiturBaru.trim() !== '') {
                        this.fasilitas.fitur.push(this.fiturBaru.trim());
                        this.fiturBaru = '';
                    }
                },

                hapusFitur(index) {
                    this.fasilitas.fitur.splice(index, 1);
                }
            }
        }
    </script>
@endsection
