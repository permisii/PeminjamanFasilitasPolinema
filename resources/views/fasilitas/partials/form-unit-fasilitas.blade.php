<div x-show="showUnit">
    <template x-for="(unit, index) in unitList" :key="index">
        <div class="border border-gray-300 p-4 rounded mb-4">
            <!-- Input Unit Fasilitas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Input Nama -->
                <input type="text" :name="'unit_fasilitas[' + index + '][nama]'" placeholder="Nama Unit Fasilitas"
                    x-model="unit.nama" class="w-full border-gray-300 rounded-md bg-secondary text-black">
                <!-- Input Unit -->
                <input type="text" :name="'unit_fasilitas[' + index + '][unit]'" placeholder="Jumlah Unit"
                    x-model="unit.unit" class="w-full border-gray-300 rounded-md bg-secondary text-black">
                <!-- Input Luas -->
                <input type="number" :name="'unit_fasilitas[' + index + '][luas]'" placeholder="Luas"
                    x-model="unit.luas" class="w-full border-gray-300 rounded-md bg-secondary text-black">
                <!-- Input Lama Sewa -->
                <input type="text" :name="'unit_fasilitas[' + index + '][lama_sewa]'" placeholder="Lama Sewa"
                    x-model="unit.lama_sewa" class="w-full border-gray-300 rounded-md bg-secondary text-black">
            </div>

            <!-- Input Tarif (Eksternal, Internal, Sosial) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <template x-for="group in ['eksternal', 'internal', 'sosial']" :key="group">
                    <div>
                        <!-- Tampilan input (format Rp) -->
                        <input type="text" :placeholder="'Tarif ' + group" :value="formatRupiah(unit.tarif[group])"
                            @input="
                                unit.tarif[group] = parseRupiah($event.target.value);
                                $event.target.value = formatRupiah(unit.tarif[group]);
                            "
                            class="w-full border-gray-300 rounded-md bg-secondary text-black" :required="showUnit">

                        <!-- Hidden input untuk dikirim ke Laravel -->
                        <input type="hidden" :name="'unit_fasilitas[' + index + '][tarif][' + group + '][harga]'"
                            :value="unit.tarif[group]">
                    </div>
                </template>

            </div>

            <!-- Input Hidden ID -->
            <input type="hidden" :name="'unit_fasilitas[' + index + '][id]'" :value="unit.id">

            <!-- Tombol Hapus Unit -->
            <x-secondary-button type="button" @click="hapusUnit(index)"
                class="text-white bg-red-500 hover:bg-red-400 text-xs rounded-sm mt-3">Hapus</x-secondary-button>
        </div>
    </template>

    <!-- Tombol Tambah Unit -->
    <x-secondary-button type="button" @click="tambahUnit()" class="rounded-sm text-sm">
        + Tambah Unit Lagi
    </x-secondary-button>
</div>
