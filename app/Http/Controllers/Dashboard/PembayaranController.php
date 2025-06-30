<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MetodePembayaran;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tanggal = $request->input('tanggal');

        $query = Pembayaran::with(['peminjaman', 'metodePembayaran']);

        if ($user->hasRole('admin')) {
            if ($tanggal) {
                $query->whereHas('peminjaman', function ($q) use ($tanggal) {
                    $q->whereDate('tanggal_peminjaman', $tanggal);
                });
            }

            $pembayarans = $query->latest()->get();

            return view('pembayaran.index', [
                'role' => 'admin',
                'pembayarans' => $pembayarans,
                'tanggal' => $tanggal
            ]);
        }

        $query->whereHas('peminjaman', function ($q) use ($user, $tanggal) {
            $q->where('user_id', $user->id);
            if ($tanggal) {
                $q->whereDate('tanggal_peminjaman', $tanggal);
            }
        });

        $pembayarans = $query->latest()->get();

        return view('pembayaran.index', [
            'role' => $user->getRoleNames()->first(),
            'pembayarans' => $pembayarans,
            'tanggal' => $tanggal
        ]);


    }

    public function show(string $id)
    {
        $pembayaran = Pembayaran::with('peminjaman')->findOrFail($id);
        $user = auth()->user();
        if ($pembayaran->expired_at !== null) {
            return redirect()->route('pembayaran.bayar', $id);
        }
        if ($pembayaran->expired_at !== null && Carbon::parse($pembayaran->expired_at)->isPast()) {
            return redirect()->route('pembayaran.index', $id)
                ->with('error', 'Pembayaran ini sudah kedaluwarsa, Anda tidak dapat memilih metode pembayaran lagi.');
        }

        // Jika user adalah 'admin', izinkan akses semua pembayaran
        if ($user->hasRole('admin')) {
            $metode = MetodePembayaran::all();
            return view('pembayaran.show', compact('pembayaran', 'metode'));
        }

        // Jika user adalah 'peminjam', cek apakah pembayaran milik dia
        if ($user->hasRole('peminjam') || $user->hasRole('pengelola')) {
            // Asumsinya relasi: pembayaran -> peminjaman -> user_id
            if ($pembayaran->peminjaman->user_id === $user->id) {
                $metode = MetodePembayaran::all();
                return view('pembayaran.show', compact('pembayaran', 'metode'));
            } else {
                abort(403, 'Anda tidak memiliki izin untuk melihat pembayaran ini.');
            }
        }

        // Jika bukan admin atau peminjam, tolak akses
        abort(403, 'Anda tidak memiliki izin.');
    }

    public function pilihMetode(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $request->validate([
            'metode_pembayaran' => 'required|exists:metode_pembayarans,id',
        ]);

        if ($pembayaran->status === 'ditolak') {
            $pembayaran->status = 'menunggu pembayaran';
            $pembayaran->save();
        }

        $pembayaran->metode_pembayaran_id = $request->metode_pembayaran;
        $pembayaran->expired_at = now()->addMinutes(30);
        $pembayaran->save();

        return redirect()->route('pembayaran.bayar', $pembayaran->id)
            ->with('success', 'Metode pembayaran berhasil dipilih.');
    }

    public function bayarForm($id)
    {
        $pembayaran = Pembayaran::with('metodePembayaran', 'peminjaman')->findOrFail($id);
        $user = auth()->user();

        if ($pembayaran->expired_at !== null && Carbon::parse($pembayaran->expired_at)->isPast()) {
            return redirect()->route('pembayaran.show', $id)
                ->with('error', 'Pembayaran ini sudah kedaluwarsa.');
        }

        if ($user->hasRole('admin')) {
            return view('pembayaran.bayar', compact('pembayaran'));
        }

        // Peminjam dan Pengelola hanya bisa akses pembayaran miliknya
        if ($user->hasRole('peminjam') || $user->hasRole('pengelola')) {
            if ($pembayaran->peminjaman->user_id === $user->id) {
                return view('pembayaran.bayar', compact('pembayaran'));
            } else {
                abort(403, 'Anda tidak memiliki izin untuk mengakses pembayaran ini.');
            }
        }

        abort(403, 'Anda tidak memiliki izin.');
    }

    public function bayarStore(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'bukti_pembayaran' => 'required|image|max:2048',
        ]);

        $pembayaran = Pembayaran::findOrFail($id);

        if ($pembayaran->expired_at && now()->gt($pembayaran->expired_at)) {
            $pembayaran->status = 'ditolak';  // Jika kedaluwarsa, ubah status ke 'ditolak'
            $pembayaran->save();
            return back()->with('error', 'Waktu pembayaran telah habis.');
        }

        $buktiPembayaran = $request->file('bukti_pembayaran');
        $buktiPembayaranName = time() . '_' . $buktiPembayaran->getClientOriginalName();
        $buktiPembayaranPath = $buktiPembayaran->storeAs('images/bukti-pembayaran', $buktiPembayaranName, 'public');

        // Simpan bukti pembayaran dan ubah status menjadi 'menunggu disetujui'
        $pembayaran->bukti_pembayaran = '/storage/' . $buktiPembayaranPath;
        $pembayaran->status = 'menunggu disetujui';
        $pembayaran->expired_at = null;
        $pembayaran->save();

        // Redirect ke halaman detail pembayaran
        return redirect()->route('pembayaran.show', $pembayaran->id)->with('success', 'Bukti pembayaran berhasil dikirim.');
    }

    public function konfirmasiPembayaranShow(string $id)
    {
        $pembayaran = Pembayaran::with(['metodePembayaran', 'peminjaman'])->findOrFail($id);
        // dd($pembayaran);
        return view('pembayaran.konfirmasi', compact('pembayaran'));
    }

    public function konfirmasiPembayaran(string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $pembayaran->status = 'disetujui';
        $pembayaran->save();

        return redirect()->route('pembayaran.index')->with('success', 'Pembyaran berhasil disetujui.');
    }

    public function tolakPembayaran(string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $pembayaran->status = 'ditolak';
        $pembayaran->expired_at = null;
        $pembayaran->save();

        return redirect()->route('pembayaran.index')->with('success', 'Pembyaran berhasil ditolak.');
    }

}
