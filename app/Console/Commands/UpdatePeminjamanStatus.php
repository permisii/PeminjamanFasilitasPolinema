<?php

namespace App\Console\Commands;

use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdatePeminjamanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'peminjaman:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status peminjaman berdasarkan tanggal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        Peminjaman::whereDate('tanggal_peminjaman', $now->toDateString())
            ->where('status', 'disetujui')
            ->whereHas('pembayaran', function ($query) {
                $query->where('status', 'disetujui');
            })
            ->update(['status' => 'aktif']);

        // Ubah status jadi SELESAI
        Peminjaman::whereDate('tanggal_pengembalian', '<=', $now->toDateString())
            ->where('status', 'aktif')
            ->whereHas('pembayaran', function ($query) {
                $query->where('status', 'disetujui');
            })
            ->update(['status' => 'selesai']);

        $this->info('Status peminjaman berhasil diperbarui.');
    }
}
