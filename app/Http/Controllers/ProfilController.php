<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfilController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function show(): View
    {
        $user = Auth::user();
        $mahasiswa = null;

        if ($user->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_lengkap' => $user->name,
                    'no_hp' => $user->no_hp,
                    'nim' => null,
                    'universitas' => null,
                    'fakultas' => null,
                    'jurusan' => null,
                    'semester' => null,
                    'status_pendaftaran' => 'belum_daftar',
                    'status' => 'aktif',
                ]
            );
        }

        $stats = [];
        if ($user->role === 'admin') {
            $stats = [
                'pendaftar_menunggu' => Mahasiswa::where('status_pendaftaran', 'diajukan')->count(),
                'mahasiswa_aktif' => Mahasiswa::where('status', 'aktif')->count(),
                'laporan_menunggu' => Mahasiswa::whereNotNull('laporan_akhir')
                    ->where(function($q) {
                        $q->whereNull('status_laporan_akhir')->orWhere('status_laporan_akhir', 'menunggu_verifikasi');
                    })->count(),
                'total_mahasiswa' => Mahasiswa::count(),
            ];
        }

        return view('pages.mahasiswa.profil', compact('user', 'mahasiswa', 'stats'));
    }

    /**
     * Mengupdate data profil yang diizinkan untuk diedit pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $mahasiswa = null;
        if ($user->role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_lengkap' => $user->name,
                    'no_hp' => $user->no_hp,
                    'nim' => null,
                    'universitas' => null,
                    'fakultas' => null,
                    'jurusan' => null,
                    'semester' => null,
                    'status_pendaftaran' => 'belum_daftar',
                    'status' => 'aktif',
                ]
            );
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'nim' => ['nullable', 'string', 'max:255', Rule::unique('mahasiswas', 'nim')->ignore($mahasiswa?->id)],
            'universitas' => ['nullable', 'string', 'max:255'],
            'fakultas' => ['nullable', 'string', 'max:255'],
            'jurusan' => ['nullable', 'string', 'max:255'],
            'semester' => ['nullable', 'string', 'max:20'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah digunakan oleh akun lain.',
            'nim.unique' => 'NIM ini sudah terdaftar untuk mahasiswa lain.',
            'no_hp.max' => 'Nomor HP maksimal 30 karakter.',
        ]);

        // Update User fields
        $userData = [
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'] ?? $user->no_hp,
            'jabatan' => $validated['jabatan'] ?? $user->jabatan,
        ];
        if (!empty($validated['name'])) {
            $userData['name'] = $validated['name'];
        }

        $user->update($userData);

        // Update Mahasiswa details jika akun mahasiswa
        if ($mahasiswa) {
            $mahasiswaUpdate = [
                'nama_lengkap' => $user->name,
                'no_hp' => $validated['no_hp'] ?? $mahasiswa->no_hp,
            ];

            // Hanya update field akademik jika diisi atau disediakan
            if ($request->has('nim')) {
                $mahasiswaUpdate['nim'] = $validated['nim'] ?: null;
            }
            if ($request->has('universitas')) {
                $mahasiswaUpdate['universitas'] = $validated['universitas'] ?: null;
            }
            if ($request->has('fakultas')) {
                $mahasiswaUpdate['fakultas'] = $validated['fakultas'] ?: null;
            }
            if ($request->has('jurusan')) {
                $mahasiswaUpdate['jurusan'] = $validated['jurusan'] ?: null;
            }
            if ($request->has('semester')) {
                $mahasiswaUpdate['semester'] = $validated['semester'] ?: null;
            }

            $mahasiswa->update($mahasiswaUpdate);
        }

        return redirect()->route('profil.show')->with('success', 'Data profil berhasil diperbarui.');
    }

    /**
     * Mengubah kata sandi pengguna.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini yang Anda masukkan salah.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profil.show')->with('success', 'Kata sandi Anda berhasil diubah.');
    }
}
