<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

class AdminAuthController extends Controller
{
    /**
     * Mostrar la vista de login de administradores
     */
    public function showLoginForm()
    {
        // Si ya hay un administrador autenticado, redirigir al dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('plazas.index');
        }
        
        return Inertia::render('Admin/Login', [
            'canResetPassword' => Route::has('admin.password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Manejar una solicitud de autenticación de administrador
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('plazas.index', absolute: false));
        }

        return back()->withErrors([
            'email' => trans('auth.failed'),
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Cerrar la sesión del administrador
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}