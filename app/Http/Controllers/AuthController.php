<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required'],
        ], [
            'login_id.required' => 'Email atau NIM wajib diisi.',
        ]);

        $loginId = trim($request->input('login_id'));
        $password = $request->input('password');
        $user = null;

        // Deteksi apakah input berupa email atau NIM
        if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
            // Login via email
            $user = User::where('email', $loginId)->first();
        } else {
            // Login via NIM: cari mahasiswa berdasarkan NIM, lalu ambil user terkait
            $mahasiswa = Mahasiswa::where('nim', $loginId)
                ->whereNotNull('nim')
                ->where('nim', '!=', '')
                ->first();
            if ($mahasiswa && $mahasiswa->user_id) {
                $user = User::find($mahasiswa->user_id);
            }
        }

        // Verifikasi password
        if ($user && Hash::check($password, $user->password)) {
            // Cek status akun
            if ($user->status === 'nonaktif') {
                throw ValidationException::withMessages([
                    'login_id' => ['Akun Anda telah dinonaktifkan oleh Admin/Mentor cabang.'],
                ]);
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('mahasiswa.dashboard');
        }

        throw ValidationException::withMessages([
            'login_id' => ['Email/NIM atau password salah.'],
        ]);
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'no_hp' => ['required', 'string', 'max:20'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'no_hp.required' => 'No. WhatsApp wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
            'password' => Hash::make($data['password']),
            'role' => 'mahasiswa', // Registrasi publik hanya untuk mahasiswa
        ]);

        // Inisialisasi record profil mahasiswa kosong (terisolasi per user)
        // Field akademik (NIM, universitas, fakultas, prodi, semester) di-set null
        Mahasiswa::create([
            'user_id' => $user->id,
            'nama_lengkap' => $user->name,
            'no_hp' => $user->no_hp,
            'nim' => null,
            'universitas' => null,
            'fakultas' => null,
            'jurusan' => null,
            'semester' => null,
            'status_pendaftaran' => 'belum_daftar',
            'status' => 'aktif',
        ]);

        Auth::login($user);

        return redirect()->route('mahasiswa.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
