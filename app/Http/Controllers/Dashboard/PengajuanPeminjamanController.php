<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\UnitFasilitas;
use App\Models\Fasilitas;
use App\Models\JenisFasilitas;
use App\Models\Peminjaman;
use App\Models\Tarif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanPeminjamanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisFasilitas = JenisFasilitas::all();
        $fasilitas = Fasilitas::with('jenisFasilitas')->get();
        $unitFasilitas = UnitFasilitas::all();
        $tarif = Tarif::all();
        $tanggalPeminjaman = Peminjaman::select('fasilitas_id', 'unit_fasilitas_id', 'tanggal_peminjaman', 'tanggal_pengembalian')
            ->get()
            ->map(function ($item) {
                return [
                    'fasilitas_id' => $item->fasilitas_id,
                    'unit_fasilitas_id' => $item->unit_fasilitas_id,
                    'from' => $item->tanggal_peminjaman,
                    'to' => $item->tanggal_pengembalian,
                ];
            })
            ->toArray();

        return view('pengajuan.index', ['jenisFasilitas' => $jenisFasilitas, 'fasilitas' => $fasilitas, 'unitFasilitas' => $unitFasilitas, 'tarif' => $tarif, 'tanggalPeminjaman' => $tanggalPeminjaman]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_fasilitas_id' => 'required|exists:jenis_fasilitas,id',
            'fasilitas_id' => 'required|exists:fasilitas,id',
            'unit_fasilitas_id' => 'nullable|exists:unit_fasilitas,id',
            'tarif_id' => 'required|exists:tarifs,id',
            'tanggal_peminjaman' => 'required|date|after_or_equal:today',
            'tanggal_pengembalian' => 'required|date|after_or_equal:tanggal_peminjaman',
            'tanggal_pemasangan_alat' => 'nullable|date',
            'tanggal_pembongkaran_alat' => 'nullable|date|after_or_equal:tanggal_pemasangan_alat',
            'layangan_eksternal' => 'nullable|string',
        ]);

        $kodeUnik = 'PEM-' . now()->format('YmdHis') . '-' . rand(1000, 9999);

        Peminjaman::create([
            'user_id' => Auth::id(),
            'fasilitas_id' => $request->fasilitas_id,
            'unit_fasilitas_id' => $request->unit_fasilitas_id, // nullable
            'tarif_id' => $request->tarif_id,
            'kode' => $kodeUnik,
            'tanggal_peminjaman' => $request->tanggal_peminjaman,
            'tanggal_pengembalian' => $request->tanggal_pengembalian,
            'tanggal_pemasangan_alat' => $request->tanggal_pemasangan_alat,
            'tanggal_pembongkaran_alat' => $request->tanggal_pembongkaran_alat,
            'layangan_eksternal' => $request->layangan_eksternal,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Peminjaman berhasil diajukan!');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
        //
    }
}
