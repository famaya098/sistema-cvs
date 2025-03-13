<?php

namespace App\Http\Controllers;

use App\Models\Aplicacion;
use App\Models\Plaza;
use App\Models\UsuarioPerfil;
use App\Models\UsuarioDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AplicacionController extends Controller
{
    /**
     * Aplicar a una plaza
     */
    public function aplicar(Request $request, $idPlaza)
    {
        $user = Auth::user();
        $perfil = UsuarioPerfil::where('user_id', $user->id)->first();
        
        if (!$perfil) {
            return redirect()->back()->with('error', 'Debe completar su perfil antes de aplicar a una plaza');
        }
        
        // Verificar si tiene CV
        $cv = UsuarioDocumento::with('documento')
            ->where('id_aspirante', $perfil->id_aspirante)
            ->whereHas('documento.tipoDocumento', function ($query) {
                $query->where('nombre', 'Curriculum Vitae');
            })
            ->where('activo', true)
            ->first();
            
        if (!$cv) {
            return redirect()->back()->with('error', 'Debe subir su Curriculum Vitae antes de aplicar a una plaza');
        }
        
        // Verificar si ya aplicó a esta plaza
        $aplicacionExistente = Aplicacion::where('id_aspirante', $perfil->id_aspirante)
            ->where('id_plaza', $idPlaza)
            ->first();
            
        if ($aplicacionExistente) {
            return redirect()->back()->with('info', 'Ya ha aplicado a esta plaza anteriormente');
        }
        
        // Verificar si la plaza existe y está vigente
        $plaza = Plaza::where('id_plaza', $idPlaza)
            ->where('publicado', true)
            ->where('estado', true)
            ->where('fecha_inicio_publicacion', '<=', now())
            ->where('fecha_fin_publicacion', '>=', now())
            ->first();
            
        if (!$plaza) {
            return redirect()->back()->with('error', 'La plaza no está disponible');
        }
        
        DB::beginTransaction();
        
        try {
            // Crear la aplicación
            $aplicacion = new Aplicacion();
            $aplicacion->id_aspirante = $perfil->id_aspirante;
            $aplicacion->id_plaza = $idPlaza;
            $aplicacion->fecha_aplicacion = now();
            $aplicacion->id_documento = $cv->id_documento;
            // Estado "Cumple Según Sistema"
            $aplicacion->id_estado_aplicacion = 3;
            $aplicacion->save();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Ha aplicado exitosamente a esta plaza');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al aplicar a esta plaza. Por favor, intente nuevamente.');
        }
    }
    
    /**
     * Verificar si un usuario ya aplicó a una plaza
     */
    public function verificarAplicacion($idPlaza)
    {
        $user = Auth::user();
        $perfil = UsuarioPerfil::where('user_id', $user->id)->first();
        
        if (!$perfil) {
            return response()->json(['aplicado' => false]);
        }
        
        $aplicacion = Aplicacion::where('id_aspirante', $perfil->id_aspirante)
            ->where('id_plaza', $idPlaza)
            ->first();
            
        return response()->json([
            'aplicado' => !!$aplicacion,
            'aplicacion' => $aplicacion
        ]);
    }
}