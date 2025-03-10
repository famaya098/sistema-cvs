<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UsuarioPerfil;
use App\Models\NivelAcademico;
use App\Models\EstadoAcademico;
use App\Models\NivelExperiencia;
use App\Models\Documento;
use App\Models\UsuarioDocumento;
use App\Models\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class AspirantePerfilController extends Controller
{
    /**
     * Mostrar el perfil del aspirante
     */
    public function show()
    {
        $user = Auth::user();
        $perfil = UsuarioPerfil::with(['nivelAcademico', 'estadoAcademico', 'experiencia', 'documentos.documento.tipoDocumento'])
            ->where('user_id', $user->id)
            ->first();
            
        // Obtener el CV actual
        $cv = null;
        if ($perfil) {
            $cv = UsuarioDocumento::with('documento')
                ->where('id_aspirante', $perfil->id_aspirante)
                ->whereHas('documento.tipoDocumento', function ($query) {
                    $query->where('nombre', 'Curriculum Vitae');
                })
                ->where('activo', true)
                ->first();
        }
        
        // Obtener los datos para los selectores
        $nivelesAcademicos = NivelAcademico::where('estado', true)->get();
        $estadosAcademicos = EstadoAcademico::where('estado', true)->get();
        $nivelesExperiencia = NivelExperiencia::where('estado', true)->get();
        
        return Inertia::render('Aspirante/Perfil', [
            'perfil' => $perfil,
            'curriculumActual' => $cv ? $cv->documento : null,
            'nivelesAcademicos' => $nivelesAcademicos,
            'estadosAcademicos' => $estadosAcademicos,
            'nivelesExperiencia' => $nivelesExperiencia,
        ]);
    }
    
    /**
     * Actualizar la información del perfil
     */
    public function updateInfo(Request $request)
    {
        $user = Auth::user();
        $perfil = UsuarioPerfil::where('user_id', $user->id)->firstOrFail();
        
        // Validar datos
        $validated = $request->validate([
            'nombre_completo' => 'required|string|max:200',
            'dui_aspirante' => [
                'required',
                'string',
                'max:10',
                'regex:/^\d{8}-\d{1}$/',  // Formato 00000000-0
                // Ignorar el DUI actual al verificar unicidad
                Rule::unique('usuario_perfil')->ignore($perfil->id_aspirante, 'id_aspirante'),
            ],
            'genero' => 'required|in:M,F',
            'telefono' => [
                'required',
                'string',
                'max:9',
                'regex:/^\d{4}-\d{4}$/'  // Formato 0000-0000
            ],
            'id_nivel_academico' => 'required|exists:niveles_academicos,id',
            'carrera' => 'required|string|max:150',
            'especialidad' => 'nullable|string|max:150',
            'id_estado_academico' => 'required|exists:estados_academicos,id',
            'id_experiencia' => 'required|exists:niveles_experiencia,id',
        ]);
        
        // Actualizar perfil
        $perfil->update($validated);
        
        // Actualizar el nombre del usuario también
        $user->name = $validated['nombre_completo'];
        $user->save();
        
        return redirect()->back()->with('success', 'Información actualizada correctamente');
    }
    
    /**
     * Actualizar la contraseña
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'confirmed', 
                'min:8',
                'regex:/[a-z]/',    // Al menos una letra minúscula
                'regex:/[A-Z]/',    // Al menos una letra mayúscula
                'regex:/[0-9]/',    // Al menos un número
            ],
        ]);
        
        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);
        
        return redirect()->back()->with('success', 'Contraseña actualizada correctamente');
    }
    
    /**
     * Descargar el CV actual
     */
    public function downloadCV()
    {
        $user = Auth::user();
        $perfil = UsuarioPerfil::where('user_id', $user->id)->firstOrFail();
        
        $cv = UsuarioDocumento::with('documento')
            ->where('id_aspirante', $perfil->id_aspirante)
            ->whereHas('documento.tipoDocumento', function ($query) {
                $query->where('nombre', 'Curriculum Vitae');
            })
            ->where('activo', true)
            ->first();
            
        if (!$cv) {
            return redirect()->back()->with('error', 'No se encontró el CV para descargar');
        }
        
        // Obtener ruta del archivo
        $filePath = $cv->documento->ruta;
        
        try {
            // Crear un nombre para descargar
            $downloadName = 'CV_' . $user->name . '_' . date('YmdHis') . '.pdf';
            
            // Descargar archivo desde S3
            return Storage::disk('s3')->download($filePath, $downloadName);
        } catch (\Exception $e) {
            Log::error('Error al descargar CV: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al descargar el CV. Por favor, intente nuevamente más tarde.');
        }
    }
    
    /**
     * Actualizar el CV
     */
    public function updateCV(Request $request)
    {
        $request->validate([
            'new_cv' => 'required|file|mimes:pdf|max:5120', // 5MB máximo
        ]);
        
        $user = Auth::user();
        $perfil = UsuarioPerfil::where('user_id', $user->id)->firstOrFail();
        
        // Iniciar transacción
        DB::beginTransaction();
        
        try {
            // Obtener el CV actual
            $currentCV = UsuarioDocumento::with('documento')
                ->where('id_aspirante', $perfil->id_aspirante)
                ->whereHas('documento.tipoDocumento', function ($query) {
                    $query->where('nombre', 'Curriculum Vitae');
                })
                ->where('activo', true)
                ->first();
                
            // Desactivar el CV actual
            if ($currentCV) {
                $currentCV->activo = false;
                $currentCV->save();
            }
            
            // Obtener el tipo de documento para CV
            $tipoDocumentoCV = TipoDocumento::where('nombre', 'Curriculum Vitae')->first();
            if (!$tipoDocumentoCV) {
                $tipoDocumentoCV = TipoDocumento::create([
                    'nombre' => 'Curriculum Vitae',
                    'descripcion' => 'Documento de Curriculum Vitae',
                    'estado' => 1
                ]);
            }
            
            // Subir el nuevo CV
            $file = $request->file('new_cv');
            $fileName = 'cv_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Crear un nombre de archivo único para S3
            $filePath = 'curriculums/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Subir archivo a S3
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            
            // Crear registro de documento
            $documento = Documento::create([
                'id_tipo_documento' => $tipoDocumentoCV->id,
                'ruta' => $filePath,
                'nombre_archivo' => $fileName,
                'estado' => 1,
            ]);
            
            // Asociar documento al usuario
            UsuarioDocumento::create([
                'id_aspirante' => $perfil->id_aspirante,
                'id_documento' => $documento->id,
                'activo' => 1,
            ]);
            
            DB::commit();
            return redirect()->back()->with('success', 'CV actualizado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar CV: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al actualizar el CV. Por favor, intente nuevamente más tarde.');
        }
    }
}