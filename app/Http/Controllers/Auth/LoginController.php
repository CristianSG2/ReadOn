<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // GET /login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // POST /login
    public function login(Request $request)
    {
        // 1) Validar entrada
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // 2) Intentar login (sin 'remember me' por ahora)
        if (Auth::attempt($credentials, false)) {
            // 3) Regenerar ID de sesión (evitar fijación)
            $request->session()->regenerate();

            // 4) Redirigir a la ruta pretendida o /me
            return redirect()->intended(route('me'));
        }

        // 5) Error genérico (no revelar si el email existe)
        return back()->withErrors([
            'email' => 'Credenciales inválidas.',
        ])->onlyInput('email');
    }

    // POST /logout
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidar sesión y regenerar token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
