<?php

namespace App\Observers;

use App\Models\Plaza;
use App\Models\Aplicacion;
use App\Models\UsuarioPerfil;
use App\Models\NivelAcademico;
use App\Models\EstadoAcademico;
use App\Models\NivelExperiencia;
use Illuminate\Support\Facades\Log;

class PlazaObserver
{
    /**
     * Maneja el evento "updated" del modelo Plaza.
     *
     * @param  \App\Models\Plaza  $plaza
     * @return void
     */
    public function updated(Plaza $plaza)
    {
        // Verificar si los campos de requisitos han cambiado
        if ($plaza->isDirty('id_nivel_academico_requerido') || 
            $plaza->isDirty('id_estado_academico_requerido') || 
            $plaza->isDirty('id_experiencia_requerido')) {
            
            // Registrar el cambio
            Log::info('Requisitos de plaza modificados', [
                'plaza_id' => $plaza->id_plaza,
                'titulo' => $plaza->titulo,
                'cambios' => $plaza->getDirty()
            ]);
            
            // Obtener todas las aplicaciones para esta plaza
            $aplicaciones = Aplicacion::where('id_plaza', $plaza->id_plaza)->get();
            
            // Si no hay aplicaciones, no es necesario continuar
            if ($aplicaciones->isEmpty()) {
                return;
            }
            
            Log::info('Reevaluando ' . $aplicaciones->count() . ' aplicaciones para la plaza ' . $plaza->id_plaza);
            
            // Reevaluar cada aplicación
            foreach ($aplicaciones as $aplicacion) {
                // No reevaluar aplicaciones que ya han sido revisadas por un administrador
                if (!is_null($aplicacion->id_estado_admin_aplicacion) && $aplicacion->id_estado_admin_aplicacion != 5) {
                    Log::info('Omitiendo aplicación ' . $aplicacion->id_aplicacion . ' porque ya fue revisada por un administrador');
                    continue;
                }
                
                // Obtener el perfil del aspirante
                $aspirante = UsuarioPerfil::find($aplicacion->id_aspirante);
                
                if (!$aspirante) {
                    Log::warning('No se encontró el aspirante para la aplicación ' . $aplicacion->id_aplicacion);
                    continue;
                }
                
                // Evaluar si cumple con los nuevos requisitos
                $cumpleRequisitos = true;
                
                // Verificar nivel académico (orden jerárquico)
                if ($plaza->id_nivel_academico_requerido) {
                    $nivelRequerido = NivelAcademico::find($plaza->id_nivel_academico_requerido);
                    $nivelAspiranteObj = NivelAcademico::find($aspirante->id_nivel_academico);
                    
                    if (!$nivelAspiranteObj || $nivelAspiranteObj->orden < $nivelRequerido->orden) {
                        $cumpleRequisitos = false;
                        Log::info('Aspirante no cumple nuevo nivel académico: ' . $aspirante->id_aspirante);
                    }
                }
                
                // Verificar estado académico (orden jerárquico)
                if ($plaza->id_estado_academico_requerido && $cumpleRequisitos) {
                    $estadoRequerido = EstadoAcademico::find($plaza->id_estado_academico_requerido);
                    $estadoAspiranteObj = EstadoAcademico::find($aspirante->id_estado_academico);
                    
                    if (!$estadoAspiranteObj || $estadoAspiranteObj->orden < $estadoRequerido->orden) {
                        $cumpleRequisitos = false;
                        Log::info('Aspirante no cumple nuevo estado académico: ' . $aspirante->id_aspirante);
                    }
                }
                
                // Verificar experiencia (orden jerárquico)
                if ($plaza->id_experiencia_requerido && $cumpleRequisitos) {
                    $experienciaRequerida = NivelExperiencia::find($plaza->id_experiencia_requerido);
                    $experienciaAspiranteObj = NivelExperiencia::find($aspirante->id_experiencia);
                    
                    if (!$experienciaAspiranteObj || $experienciaAspiranteObj->orden < $experienciaRequerida->orden) {
                        $cumpleRequisitos = false;
                        Log::info('Aspirante no cumple nueva experiencia requerida: ' . $aspirante->id_aspirante);
                    }
                }
                
                // Actualizar el estado de la aplicación
                $nuevoEstado = $cumpleRequisitos ? 3 : 4; // 3: Cumple, 4: No Cumple
                
                // Solo actualizar si el estado ha cambiado
                if ($aplicacion->id_estado_aplicacion != $nuevoEstado) {
                    Log::info('Actualizando estado de aplicación ' . $aplicacion->id_aplicacion . 
                             ' de ' . $aplicacion->id_estado_aplicacion . ' a ' . $nuevoEstado);
                    
                    $aplicacion->id_estado_aplicacion = $nuevoEstado;
                    
                    // Si el administrador no ha revisado la aplicación, también actualizar estado admin a pendiente
                    if (is_null($aplicacion->id_estado_admin_aplicacion)) {
                        $aplicacion->id_estado_admin_aplicacion = 5; // Pendiente
                    }
                    
                    $aplicacion->save();
                }
            }
            
            Log::info('Finalizada la reevaluación de aplicaciones para la plaza ' . $plaza->id_plaza);
        }
    }
}