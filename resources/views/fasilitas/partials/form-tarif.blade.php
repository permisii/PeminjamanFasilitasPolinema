<!-- Input Tarif Global (Jika !showUnit) -->
<div x-show="!showUnit" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <template x-for="group in ['eksternal', 'internal', 'sosial']" :key="group">
        <div>
            <!-- Tampilan Input Format Rupiah -->
            <input type="text"
                class="w-full border-gray-300 focus:border-gray-300 focus:ring-gray-300 rounded-md shadow-xs text-black bg-secondary"
                :placeholder="'Masukan Tarif ' + group" :value="formatRupiah(tarif[group])"
                @input="
                    tarif[group] = parseRupiah($event.target.value);
                    $event.target.value = formatRupiah(tarif[group]);
                "
                :required="!showUnit">

            <!-- Hidden input untuk kirim ke Laravel -->
            <input type="hidden" :name="'tarif[' + group + '][harga]'" :value="tarif[group]">
        </div>
    </template>
</div>
