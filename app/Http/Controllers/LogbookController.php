<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LogbookController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        // Blokir mahasiswa yang belum ditempatkan
        if ($user->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
                return redirect()->route('mahasiswa.dashboard')->with('error', '                Anda belum dapat mengakses logbook. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
            }
        }

        $logbooks = $user->role === 'admin'
            ? Logbook::with('user')->latest('tanggal')->latest()->paginate(10)
            : Logbook::where('user_id', $user->id)->latest('tanggal')->latest()->paginate(10);

        return view('logbooks.index', compact('logbooks'));
    }

    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return redirect()->route('logbooks.index')->with('error', 'Admin/Mentor tidak perlu membuat logbook kegiatan.');
        }

        // Blokir jika mahasiswa belum ditempatkan
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
            return redirect()->route('mahasiswa.dashboard')->with('error', '            Anda belum dapat mengakses logbook. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
        }

        if ($mahasiswa->status === 'selesai') {
            return redirect()->route('logbooks.index')->with('error', 'Program magang Anda telah selesai. Halaman logbook kini hanya tersedia dalam mode riwayat (hanya baca).');
        }

        return view('logbooks.create');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Admin/Mentor tidak diizinkan membuat logbook.'], 403);
            }
            abort(403, 'Admin/Mentor tidak diizinkan membuat logbook.');
        }

        // Blokir jika mahasiswa belum ditempatkan
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        if (!$mahasiswa || ($mahasiswa->status_pendaftaran !== 'ditempatkan' && $mahasiswa->status !== 'selesai')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => '                Anda belum dapat mengakses logbook. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.'], 403);
            }
            return redirect()->route('mahasiswa.dashboard')->with('error', '            Anda belum dapat mengakses logbook. Silakan tunggu hingga Anda ditempatkan di cabang/unit oleh Admin.');
        }

        if ($mahasiswa->status === 'selesai') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Program magang Anda telah selesai. Anda tidak dapat membuat logbook baru.'], 403);
            }
            return redirect()->route('logbooks.index')->with('error', 'Program magang Anda telah selesai. Anda tidak dapat membuat logbook baru.');
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'judul' => ['required', 'string', 'max:150'],
            'aktivitas' => ['required', 'string'],
            'hasil' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = $user->id;
        $validated['status'] = 'diajukan';

        $logbook = Logbook::create($validated);
        $logbook->load('user');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Logbook berhasil diajukan.',
                'data' => [
                    'id' => $logbook->id,
                    'tanggal' => $logbook->tanggal,
                    'tanggal_formatted' => Carbon::parse($logbook->tanggal)->format('d-m-Y'),
                    'judul' => $logbook->judul,
                    'aktivitas' => Str::limit($logbook->aktivitas, 85),
                    'hasil' => $logbook->hasil ? Str::limit($logbook->hasil, 60) : null,
                    'status' => $logbook->status,
                    'status_upper' => strtoupper($logbook->status),
                    'user_name' => $logbook->user->name ?? $user->name,
                    'edit_url' => route('logbooks.edit', $logbook),
                    'destroy_url' => route('logbooks.destroy', $logbook),
                ]
            ]);
        }

        return redirect()->route('logbooks.index')->with('success', 'Logbook berhasil diajukan.');
    }

    public function show(Logbook $logbook): RedirectResponse
    {
        return redirect()->route('logbooks.edit', $logbook);
    }

    public function edit(Logbook $logbook): View|RedirectResponse
    {
        $this->authorizeRecord($logbook);

        if (Auth::user()->role === 'admin') {
            return redirect()->route('logbooks.index')->with('info', 'Gunakan menu Logbook & Monitoring untuk meninjau logbook mahasiswa.');
        }

        $user = Auth::user();
        if ($user->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if ($mahasiswa && $mahasiswa->status === 'selesai') {
                return redirect()->route('logbooks.index')->with('error', 'Program magang Anda telah selesai. Logbook bersifat read-only.');
            }
        }

        return view('logbooks.create', compact('logbook'));
    }

    public function update(Request $request, Logbook $logbook): RedirectResponse
    {
        $this->authorizeRecord($logbook);

        $user = Auth::user();
        if ($user->role === 'admin') {
            $validated = $request->validate([
                'tanggal' => ['nullable', 'date'],
                'judul' => ['nullable', 'string', 'max:150'],
                'aktivitas' => ['nullable', 'string'],
                'hasil' => ['nullable', 'string'],
                'catatan_mentor' => ['nullable', 'string'],
                'status' => ['required', 'in:draft,diajukan,disetujui,revisi'],
            ]);

            $validated['tanggal'] = $validated['tanggal'] ?? $logbook->tanggal;
            $validated['judul'] = $validated['judul'] ?? $logbook->judul;
            $validated['aktivitas'] = $validated['aktivitas'] ?? $logbook->aktivitas;
        } else {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if ($mahasiswa && $mahasiswa->status === 'selesai') {
                return redirect()->route('logbooks.index')->with('error', 'Program magang Anda telah selesai. Logbook bersifat read-only.');
            }

            $validated = $request->validate([
                'tanggal' => ['required', 'date'],
                'judul' => ['required', 'string', 'max:150'],
                'aktivitas' => ['required', 'string'],
                'hasil' => ['nullable', 'string'],
            ]);
            $validated['status'] = 'diajukan';
        }

        $logbook->update($validated);

        return redirect()->back()->with('success', 'Status logbook berhasil diperbarui.');
    }

    public function destroy(Logbook $logbook): RedirectResponse
    {
        $user = Auth::user();

        // Admin tidak diizinkan menghapus logbook mahasiswa (data historis)
        if ($user->role === 'admin') {
            return redirect()->route('logbooks.index')->with('error', 'Admin tidak diizinkan menghapus logbook mahasiswa.');
        }

        $this->authorizeRecord($logbook);

        if ($user->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
            if ($mahasiswa && $mahasiswa->status === 'selesai') {
                return redirect()->route('logbooks.index')->with('error', 'Program magang Anda telah selesai. Logbook bersifat read-only.');
            }
        }

        $logbook->delete();

        return redirect()->route('logbooks.index')->with('success', 'Logbook berhasil dihapus.');
    }

    private function authorizeRecord(Logbook $logbook): void
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $logbook->user_id !== $user->id) {
            abort(403, 'Akses ditolak.');
        }
    }
}
