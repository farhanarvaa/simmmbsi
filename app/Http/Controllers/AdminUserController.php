<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Menampilkan daftar akun khusus Admin dan Mentor BSI.
     */
    public function index(): View
    {
        $users = User::where('role', 'admin')->latest()->paginate(10);

        return view('pages.admin.pengguna', compact('users'));
    }

    /**
     * Membuat akun Admin / Mentor BSI baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ], [
            'name.required' => 'Nama lengkap akun admin/mentor wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin',
            'status' => 'aktif',
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Akun Admin/Mentor baru berhasil dibuat.');
    }

    /**
     * Mereset kata sandi akun Admin / Mentor.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        if ($user->role !== 'admin') {
            return back()->with('error', 'Hanya akun Admin/Mentor yang dapat dikelola di halaman ini.');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Kata sandi untuk akun ' . $user->name . ' berhasil di-reset.');
    }

    /**
     * Mengaktifkan atau menonaktifkan akun Admin / Mentor.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->role !== 'admin') {
            return back()->with('error', 'Hanya akun Admin/Mentor yang dapat dikelola di halaman ini.');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif digunakan.');
        }

        $newStatus = ($user->status === 'nonaktif') ? 'aktif' : 'nonaktif';

        $user->update([
            'status' => $newStatus,
        ]);

        $statusLabel = $newStatus === 'aktif' ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()->route('pengguna.index')->with('success', "Status akun {$user->name} berhasil {$statusLabel}.");
    }

}
