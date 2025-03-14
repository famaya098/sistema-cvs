<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aplicacion;
use App\Models\EstadoAplicacion;
use App\Models\Plaza;
use App\Models\NivelAcademico;
use App\Models\EstadoAcademico;
use App\Models\NivelExperiencia;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AplicacionesAdminController extends Controller
{
    /**
     * Mostrar lista de aplicaciones
     */
    public function index(Request $request)
    {
        $query = Aplicacion::with([
            'aspirante.nivelAcademico', 
            'aspirante.estadoAcademico',
            'aspirante.experiencia',
            'plaza', 
            'estado',
            'estadoAdmin',
            'documento.tipoDocumento'
        ])
        ->join('usuario_perfil', 'aplicaciones.id_aspirante', '=', 'usuario_perfil.id_aspirante')
        ->join('plazas', 'aplicaciones.id_plaza', '=', 'plazas.id_plaza')
        ->select('aplicaciones.*');
        
        // Aplicar filtros si existen
        if ($request->filled('search')) {
            $query->where('usuario_perfil.nombre_completo', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('plaza')) {
            $query->where('aplicaciones.id_plaza', $request->plaza);
        }
        
        if ($request->filled('estado')) {
            $query->where('aplicaciones.id_estado_aplicacion', $request->estado);
        }
        
        if ($request->filled('estado_admin')) {
            if ($request->estado_admin === 'null') {
                $query->whereNull('aplicaciones.id_estado_admin_aplicacion');
            } else {
                $query->where('aplicaciones.id_estado_admin_aplicacion', $request->estado_admin);
            }
        }
        
        if ($request->filled('nivel_academico')) {
            $query->where('usuario_perfil.id_nivel_academico', $request->nivel_academico);
        }
        
        if ($request->filled('nivel_experiencia')) {
            $query->where('usuario_perfil.id_experiencia', $request->nivel_experiencia);
        }
        
        if ($request->filled('estado_academico')) {
            $query->where('usuario_perfil.id_estado_academico', $request->estado_academico);
        }
        
        // Ordenamiento
        $sortField = $request->input('sort_field', 'fecha_aplicacion');
        $sortDirection = $request->input('sort_direction', 'desc');
        
        $query->orderBy($sortField, $sortDirection);
        
        $aplicaciones = $query->paginate(10);
        
        // Obtener datos para filtros
        $plazas = Plaza::where('estado', true)->get(['id_plaza', 'titulo']);
        $estados = EstadoAplicacion::where('estado', true)->get();
        $nivelesAcademicos = NivelAcademico::where('estado', true)->get();
        $estadosAcademicos = EstadoAcademico::where('estado', true)->get();
        $nivelesExperiencia = NivelExperiencia::where('estado', true)->get();
        
        return Inertia::render('Admin/Aplicaciones/Index', [
            'aplicaciones' => $aplicaciones,
            'plazas' => $plazas,
            'estados' => $estados,
            'nivelesAcademicos' => $nivelesAcademicos,
            'estadosAcademicos' => $estadosAcademicos,
            'nivelesExperiencia' => $nivelesExperiencia,
            'filters' => $request->all([
                'search', 'plaza', 'estado', 'estado_admin', 
                'nivel_academico', 'nivel_experiencia', 'estado_academico',
                'sort_field', 'sort_direction'
            ]),
        ]);
    }
    
    /**
     * Actualizar el estado administrativo de una aplicación
     */
    public function updateEstadoAdmin(Request $request, $id)
    {
        $request->validate([
            'id_estado_admin_aplicacion' => 'required|exists:estados_aplicacion,id',
        ]);
        
        $aplicacion = Aplicacion::findOrFail($id);
        $aplicacion->id_estado_admin_aplicacion = $request->id_estado_admin_aplicacion;
        $aplicacion->save();
        
        return redirect()->back()->with('message', 'Estado de aplicación actualizado correctamente');
    }
    
    /**
 * Ver CV de un aspirante
 */
public function downloadCV($id)
{
    $aplicacion = Aplicacion::with('documento')->findOrFail($id);
    
    if (!$aplicacion->documento) {
        return redirect()->back()->with('error', 'No se encontró el CV para visualizar');
    }
    
    // Ruta del archivo
    $filePath = $aplicacion->documento->ruta;
    
    try {
        // Obtener el contenido del archivo desde S3
        $fileContents = \Storage::disk('s3')->get($filePath);
        
        // Devolver el contenido como respuesta inline PDF
        return response($fileContents, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $aplicacion->aspirante->nombre_completo . '_CV.pdf"',
        ]);
    } catch (\Exception $e) {
        \Log::error('Error al obtener CV: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error al visualizar el CV. Por favor, intente nuevamente más tarde.');
    }
}
}