<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

        // 2. Rate Limiting — PRD Bab 10.4: "Rate limiting pada form inquiry & login"
        // Maksimal 5 percobaan login per menit per kombinasi email + IP
        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, maxAttempts: 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // 3. Mencoba mencocokkan kredensial & melakukan login
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            
            // 4. Reset rate limiter setelah login berhasil
            RateLimiter::clear($throttleKey);

            // 5. Regenerasi session ID untuk keamanan (mencegah Session Fixation)
            $request->session()->regenerate();

            // 6. Redirect ke halaman yang dituju sebelumnya (atau ke /admin)
            return redirect()->intended('/admin');
        }

        // 7. Catat percobaan gagal ke rate limiter
        RateLimiter::hit($throttleKey, decay: 60);

        // 8. Jika gagal, kembalikan dengan error
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
