<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si hay un usuario autenticado en la guarda 'admin'
        if (!Auth::guard('admin')->check()) {
            // Si es una solicitud AJAX o espera una respuesta JSON, devolver un error 403
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized.'], 403);
            }
            
            // Redirigir SIEMPRE a welcome con un mensaje de error, independientemente del estado de autenticación
            return redirect()->route('welcome')->with('error', 'No tienes acceso a esta página.');
        }

        return $next($request);
    }
}