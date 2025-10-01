<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends Controller
{
    // GET /register
    public function showRegisterForm()
    {
        // Más adelante devolveremos la vista Blade
        return response("Register form (controller) — pendiente de vista", 200);
    }

    // POST /register
    public function register(Request $request)
    {
        // 1) Validar datos
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'min:2', 'max:100'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            // requiere input 'password_confirmation'
        ]);

        // 2) Crear usuario (hasheando contraseña explícitamente)
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 3) Loguear automáticamente y regenerar sesión
        Auth::login($user);
        $request->session()->regenerate();

        // 4) Ir a /me
        return redirect()->route('me');
    }
}
