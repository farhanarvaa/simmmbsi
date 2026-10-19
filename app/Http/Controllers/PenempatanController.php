<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PenempatanController extends Controller
{
    /**
     * Menampilkan daftar mahasiswa untuk penempatan cabang/unit BSI.
     */
    public function index(): View
    {
        $user = Auth::user();
        $isAdmin = $user->role === 'admin';

        if ($isAdmin) {
            $mahasiswas = Mahasiswa::with('user')
                ->whereIn('status_pendaftaran', ['diterima', 'ditempatkan'])
                ->latest()
                ->paginate(10);
        } else {
            $mahasiswas = Mahasiswa::with('user')
                ->where('user_id', $user->id)
                ->whereIn('status_pendaftaran', ['diterima', 'ditempatkan'])
                ->paginate(10);
        }

        $cabangs = Cabang::orderBy('nama')->get();

        return view('pages.admin.penempatan', compact('mahasiswas', 'isAdmin', 'cabangs'));
    }

    /**
     * Update penempatan cabang/unit dan periode magang mahasiswa oleh Admin.
     */
    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin/Mentor.');
        }

        $validated = $request->validate([
            'id_cabang' => ['required', 'exists:cabang,id'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ], [
            'id_cabang.required' => 'Cabang/unit penempatan wajib dipilih.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
        ]);

        $validated['status_pendaftaran'] = 'ditempatkan';
        $validated['status'] = 'aktif';

        $mahasiswa->update($validated);

        return redirect()->back()->with('success', "Penempatan cabang/unit untuk {$mahasiswa->nama_lengkap} berhasil diperbarui.");
    }
}
