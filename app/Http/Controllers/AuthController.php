<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'identifier' => ['required_without:email', 'nullable', 'string'],
            'email' => ['required_without:identifier', 'nullable', 'string'],
            'password' => ['required', 'string'],
        ], [
            'identifier.required_without' => 'Email atau Username wajib diisi.',
            'email.required_without' => 'Email atau Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $identifier = trim($request->input('identifier') ?: $request->input('email') ?: '');
        $password = (string) $request->input('password');
        $remember = $request->boolean('remember');

        // Allow login by email, phone, or username
        $user = User::where('email', $identifier)
            ->orWhere('username', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return back()->withInput($request->only('identifier', 'email', 'remember'))->withErrors([
                'identifier' => 'Email/Username tidak valid atau kata sandi salah.',
            ]);
        }

        if ($user->status === 'nonaktif') {
            return back()->withInput($request->only('identifier', 'email'))->withErrors([
                'identifier' => 'Akun Anda dinonaktifkan oleh administrator. Silakan hubungi pihak kampus.',
            ]);
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'username.unique' => 'Username sudah digunakan atau data belum lengkap.',
            'phone.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $username = $request->input('username') ?: explode('@', $request->input('email'))[0];

        // Ensure unique username
        $baseUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername.$counter++;
        }

        $user = User::create([
            'name' => $request->input('name'),
            'username' => $username,
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'role' => 'pembeli',
            'status' => 'aktif',
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran akun berhasil! Silakan masuk dengan email/username dan kata sandi Anda.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }

    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'penjual' => redirect()->intended(route('merchant.dashboard')),
            default => redirect()->intended(route('menu.index')),
        };
    }
}
