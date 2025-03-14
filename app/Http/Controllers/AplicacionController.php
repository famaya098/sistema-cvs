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
        
        // NUEVO: Verificar si cumple con los requisitos
        $cumpleRequisitos = true;
        $razonesIncumplimiento = [];
        
        // Verificar nivel académico
        if ($plaza->id_nivel_academico_requerido && $perfil->id_nivel_academico != $plaza->id_nivel_academico_requerido) {
            $cumpleRequisitos = false;
            $nivelRequerido = \App\Models\NivelAcademico::find($plaza->id_nivel_academico_requerido);
            $razonesIncumplimiento[] = "Nivel académico requerido: " . ($nivelRequerido ? $nivelRequerido->nombre : 'No especificado');
        }
        
        // Verificar estado académico
        if ($plaza->id_estado_academico_requerido && $perfil->id_estado_academico != $plaza->id_estado_academico_requerido) {
            $cumpleRequisitos = false;
            $estadoRequerido = \App\Models\EstadoAcademico::find($plaza->id_estado_academico_requerido);
            $razonesIncumplimiento[] = "Estado académico requerido: " . ($estadoRequerido ? $estadoRequerido->nombre : 'No especificado');
        }
        
        // Verificar experiencia
        if ($plaza->id_experiencia_requerido && $perfil->id_experiencia != $plaza->id_experiencia_requerido) {
            $cumpleRequisitos = false;
            $experienciaRequerida = \App\Models\NivelExperiencia::find($plaza->id_experiencia_requerido);
            $razonesIncumplimiento[] = "Experiencia requerida: " . ($experienciaRequerida ? $experienciaRequerida->nombre : 'No especificada');
        }
        
        DB::beginTransaction();
        
        try {
            // Crear la aplicación
            $aplicacion = new Aplicacion();
            $aplicacion->id_aspirante = $perfil->id_aspirante;
            $aplicacion->id_plaza = $idPlaza;
            $aplicacion->fecha_aplicacion = now();
            $aplicacion->id_documento = $cv->id_documento;
            
            // Asignar estado según si cumple o no con los requisitos
            $aplicacion->id_estado_aplicacion = $cumpleRequisitos ? 3 : 4; // 3: Cumple, 4: No Cumple
            $aplicacion->save();
            
            DB::commit();
            
            // Mensaje de éxito
            if ($cumpleRequisitos) {
                return redirect()->back()->with('success', '¡Has aplicado exitosamente a esta plaza!');
            } else {
                // Aún permitimos la aplicación pero notificamos que no cumple algunos requisitos
                return redirect()->back()->with('success', 'Has aplicado a esta plaza, aunque no cumples con algunos requisitos: ' . implode(', ', $razonesIncumplimiento));
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ocurrió un error al aplicar a esta plaza. Por favor, intente nuevamente: ' . $e->getMessage());
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