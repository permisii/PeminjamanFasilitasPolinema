<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Log;

class KonfirmasiPeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $peminjaman = Peminjaman::when($request->tanggal, function ($query) use ($request) {
            $query->whereDate('tanggal_peminjaman', $request->tanggal);
        })->where('status', 'menunggu')->get();

        return view('konfirmasi-peminjaman.index', compact('peminjaman'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $peminjaman = Peminjaman::with(['user', 'fasilitas', 'fasilitas.jenisFasilitas', 'unitFasilitas'])->findOrFail($id);

        return view('konfirmasi-peminjaman.show', compact('peminjaman'));
    }

    public function tolak(string $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status = 'ditolak';
        $peminjaman->save();

        return redirect()->route('konfirmasi-peminjaman.index')->with('message', 'Peminjaman telah ditolak.');
    }

    public function rincianPembayaran($id)
    {
        $peminjaman = Peminjaman::with(['fasilitas', 'tarif'])->findOrFail($id);
        // Tampilkan view rincian pembayaran, kamu bisa ganti view-nya sesuai kebutuhan
        return view('konfirmasi-peminjaman.rincian', compact('peminjaman'));
    }

    public function tambahDiskon(string $id, Request $request)
    {
        $peminjaman = Peminjaman::with("tarif")->findOrFail($id);
        $hargaTarif = $peminjaman->tarif->harga;

        $request->validate([
            'diskon' => ['nullable', 'numeric', 'gte:0', 'lte:' . $hargaTarif],
        ]);

        $diskon = $request->input('diskon');
        $totalHarga = $hargaTarif - $diskon;
        $peminjaman->diskon = $diskon;
        $peminjaman->total_harga = $totalHarga;
        Log::info('Diskon yang diterima:', ['diskon' => $diskon]);
        $peminjaman->save();


        return redirect()->route('konfirmasi-peminjaman.rincian', $peminjaman->id)
            ->with('success', 'Diskon dan Total Harga Berhasil Diperbarui');
    }
    public function konfirmasiPeminjaman(string $id)
    {
        $peminjaman = Peminjaman::with("tarif")->findOrFail($id);
        $totalHarga = $peminjaman->total_harga;


        // Update status peminjaman
        $peminjaman->status = 'disetujui';
        $peminjaman->save();

        // Buat kode pembayaran unik
        $kodeUnik = 'BYR-' . now()->format('YmdHis') . '-' . rand(1000, 9999);

        // Create atau Update pembayaran
        Pembayaran::updateOrCreate(
            ['peminjaman_id' => $peminjaman->id],
            [
                'kode' => $kodeUnik,
                'total_harga' => $totalHarga,
            ]
        );

        return redirect()->route('konfirmasi-peminjaman.index')
            ->with('success', 'Peminjaman disetujui dan pembayaran telah diperbarui/dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->delete();

        return redirect()->route('konfirmasi-peminjaman.index')->with('success', 'Data Pengajuan Peminjaman Berhasil Dihapus!');
    }
}
