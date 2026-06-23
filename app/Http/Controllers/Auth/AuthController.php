<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AuthController extends Controller
{
    /* ═══════════════════════════════════════════════════════════════
     │  REGISTRASI (REQ-01, REQ-02, REQ-03)
     ══════════════════════════════════════════════════════════════ */

    /**
     * Tampilkan halaman form registrasi.
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.register');
    }

    /**
     * Proses registrasi akun baru.
     *
     * - Validasi via RegisterRequest (REQ-02)
     * - Hash password bcrypt cost 12 (BCRYPT_ROUNDS=12 di .env)
     * - Status akun awal: pending (menunggu verifikasi email)
     * - Kirim email verifikasi (REQ-03)
     */
    public function register(RegisterRequest $request): JsonResponse|RedirectResponse
    {
        $user = User::create([
            'nama_lengkap'      => $request->nama_lengkap,
            'email'             => $request->email,
            'password'          => $request->password,   // Model User cast 'password' => 'hashed'
            'no_telepon'        => $request->no_telepon,
            'peran'             => $request->peran,
            'status_akun'       => 'pending',
            'email_verified_at' => null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Registrasi berhasil! Akun Anda sedang menunggu verifikasi oleh admin.',
            ], 201);
        }

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Akun Anda sedang menunggu verifikasi oleh admin.');
    }

    /* ═══════════════════════════════════════════════════════════════
     │  LOGIN (REQ-04, REQ-06)
     ══════════════════════════════════════════════════════════════ */

    /**
     * Tampilkan halaman form login.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses login.
     *
     * - Rate limit: maks 5x gagal → lockout 15 menit (REQ-07 bisnis)
     * - Cek status akun (pending/nonaktif)
     * - Buat Sanctum token
     * - Regenerate session (REQ-06 keamanan)
     * - Redirect berdasarkan peran
     */
    public function login(LoginRequest $request): JsonResponse|RedirectResponse
    {
        $cacheKey = 'login_attempts_' . $request->ip();
        $lockKey  = 'login_locked_' . $request->ip();

        // REQ-07: Cek lockout (5x gagal → 15 menit)
        if (Cache::has($lockKey)) {
            $remainingSeconds = Cache::get($lockKey . '_until', now()->timestamp) - now()->timestamp;
            $remainingMenit   = max(1, (int) ceil($remainingSeconds / 60));

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Akun dikunci {$remainingMenit} menit lagi karena terlalu banyak percobaan gagal.",
                    'locked'  => true,
                    'remaining_seconds' => max(0, $remainingSeconds),
                ], 429);
            }

            return back()->withErrors([
                'email' => "Akun dikunci {$remainingMenit} menit lagi karena terlalu banyak percobaan gagal.",
            ])->with('locked', true)
              ->with('remaining_seconds', max(0, $remainingSeconds));
        }

        // Coba login
        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            // Tambah counter gagal
            $attempts = Cache::get($cacheKey, 0) + 1;
            Cache::put($cacheKey, $attempts, now()->addMinutes(15));

            if ($attempts >= 5) {
                // Aktifkan lockout 15 menit
                $unlockAt = now()->addMinutes(15)->timestamp;
                Cache::put($lockKey, true, now()->addMinutes(15));
                Cache::put($lockKey . '_until', $unlockAt, now()->addMinutes(15));
                Cache::forget($cacheKey);

                if ($request->wantsJson()) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Akun dikunci 15 menit karena terlalu banyak percobaan gagal.',
                        'locked'  => true,
                        'remaining_seconds' => 900,
                    ], 429);
                }

                return back()->withErrors([
                    'email' => 'Akun dikunci 15 menit karena terlalu banyak percobaan gagal.',
                ])->with('locked', true)->with('remaining_seconds', 900);
            }

            $sisaPercobaan = 5 - $attempts;

            if ($request->wantsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Email atau password salah. Sisa percobaan: {$sisaPercobaan}.",
                ], 401);
            }

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => "Email atau password salah. Sisa percobaan: {$sisaPercobaan}."]);
        }

        // Login berhasil → reset counter
        Cache::forget($cacheKey);
        Cache::forget($lockKey);
        Cache::forget($lockKey . '_until');

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cek status akun
        if ($user->status_akun === 'pending') {
            Auth::logout();

            $message = 'Akun Anda belum aktif. Silakan tunggu verifikasi/persetujuan dari admin.';

            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 403);
            }

            return back()->withErrors(['email' => $message])->withInput($request->only('email'));
        }

        if ($user->status_akun === 'nonaktif') {
            Auth::logout();

            $message = 'Akun Anda telah dinonaktifkan. Hubungi administrator.';

            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => $message], 403);
            }

            return back()->withErrors(['email' => $message])->withInput($request->only('email'));
        }

        // Regenerate session (REQ-06 keamanan)
        $request->session()->regenerate();

        // Buat Sanctum token
        $token = $user->createToken('auth_token')->plainTextToken;
        session(['sanctum_token' => $token]);

        if ($request->wantsJson()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Login berhasil.',
                'token'   => $token,
                'user'    => [
                    'id'           => $user->id,
                    'nama_lengkap' => $user->nama_lengkap,
                    'email'        => $user->email,
                    'peran'        => $user->peran,
                ],
            ]);
        }

        return $this->redirectByRole($user);
    }

    /* ═══════════════════════════════════════════════════════════════
     │  LOGOUT
     ══════════════════════════════════════════════════════════════ */

    /**
     * Proses logout.
     *
     * - Hapus Sanctum token aktif
     * - Invalidate session
     * - Regenerate CSRF token
     */
    public function logout(Request $request): RedirectResponse
    {
        // Hapus token Sanctum saat ini
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar.');
    }

    /* ═══════════════════════════════════════════════════════════════
     │  HELPER
     ══════════════════════════════════════════════════════════════ */

    /**
     * Redirect berdasarkan peran pengguna.
     */
    private function redirectByRole(User $user): RedirectResponse
    {
        return match ($user->peran) {
            'admin'   => redirect()->route('admin.dashboard'),
            'relawan' => redirect()->route('relawan.dashboard'),
            'donatur' => redirect()->route('donatur.dashboard'),
            default   => redirect('/'),
        };
    }
}
