<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // Menampilkan formulir login.
     
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses request login.
    
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2. Mencoba mencocokkan kredensial & melakukan login
        // 'remember' opsional (di sini diset false, bisa ditambah checkbox jika perlu)
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            
            // 3. Regenerasi session ID untuk keamanan (Mencegah Session Fixation)
            $request->session()->regenerate();

            // 4. Redirect ke halaman yang dituju sebelumnya (atau ke /admin)
            return redirect()->intended('/admin');
        }

        // 5. Jika gagal, kembalikan dengan error
        throw ValidationException::withMessages([
            'email' => __('Mungkin email atau password salah.'),
        ]);
    }

    // Memproses request logout.
    
    public function logout(Request $request)
    {
        // 1. Logout dari auth guard
        Auth::logout();

        // 2. Hapus data sesi aktif saat ini
        $request->session()->invalidate();

        // 3. Buat ulang CSRF token baru untuk keamanan
        $request->session()->regenerateToken();

        // 4. Redirect kembali ke halaman login
        return redirect()->route('login');
    }
}
