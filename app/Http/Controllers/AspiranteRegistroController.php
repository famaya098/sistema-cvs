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

class AspiranteRegistroController extends Controller
{
    /**
     * Muestra el formulario de registro extendido
     */
    public function create()
    {
        // Obtener niveles académicos, estados y experiencia
        $nivelesAcademicos = NivelAcademico::where('estado', true)->get();
        $estadosAcademicos = EstadoAcademico::where('estado', true)->get();
        $nivelesExperiencia = NivelExperiencia::where('estado', true)->get();
        
        // Log para depuración
        Log::info('Niveles académicos: ' . $nivelesAcademicos->count());
        Log::info('Estados académicos: ' . $estadosAcademicos->count());
        Log::info('Niveles experiencia: ' . $nivelesExperiencia->count());
        
        return Inertia::render('Auth/RegisterExtended', [
            'nivelesAcademicos' => $nivelesAcademicos,
            'estadosAcademicos' => $estadosAcademicos,
            'nivelesExperiencia' => $nivelesExperiencia,
        ]);
    }
    
    /**
     * Procesa el formulario de registro extendido
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users',
            'password' => [
                'required', 
                'confirmed', 
                'min:8',
                'regex:/[a-z]/',    // Al menos una letra minúscula
                'regex:/[A-Z]/',    // Al menos una letra mayúscula
                'regex:/[0-9]/',    // Al menos un número
            ],
            'dui_aspirante' => [
                'required',
                'string',
                'max:10',
                'unique:usuario_perfil',
                'regex:/^\d{8}-\d{1}$/'  // Formato 00000000-0
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
            'cv' => 'required|file|mimes:pdf|max:5120', // 5MB máximo
            'g-recaptcha-response' => 'required|captcha',
        ]);
        
        try {
            // Iniciar transacción para asegurar que todos los datos se guarden o ninguno
            DB::beginTransaction();
            
            // Crear el usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'aspirante',
            ]);
            
            // Crear el perfil del aspirante
            $perfil = UsuarioPerfil::create([
                'user_id' => $user->id,
                'dui_aspirante' => $request->dui_aspirante,
                'nombre_completo' => $request->name,
                'genero' => $request->genero,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'carrera' => $request->carrera,
                'especialidad' => $request->especialidad,
                'id_nivel_academico' => $request->id_nivel_academico,
                'id_estado_academico' => $request->id_estado_academico,
                'id_experiencia' => $request->id_experiencia,
                'estado' => 1,
            ]);
            
            // Manejar la subida del CV
            if ($request->hasFile('cv')) {
                // Obtener el tipo de documento para CV
                $tipoDocumentoCV = TipoDocumento::where('nombre', 'Curriculum Vitae')->first();
                if (!$tipoDocumentoCV) {
                    $tipoDocumentoCV = TipoDocumento::create([
                        'nombre' => 'Curriculum Vitae',
                        'descripcion' => 'Documento de Curriculum Vitae',
                        'estado' => 1
                    ]);
                }
                
                $file = $request->file('cv');
                $fileName = 'cv_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                
                // Crear un nombre de archivo único para S3
                $filePath = 'curriculums/' . Str::uuid() . '.' . $file->getClientOriginalExtension();
                
                // Subir archivo a S3
                $path = Storage::disk('s3')->put($filePath, file_get_contents($file));
                
                // Obtener la URL completa del archivo
                $url = Storage::disk('s3')->url($filePath);
                
                Log::info('Archivo subido a S3: ' . $url);
                
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
            }
            
            // Confirmar transacción
            DB::commit();
            
            // Iniciar sesión automáticamente
            Auth::login($user);
            
            return redirect()->route('dashboard')->with('message', 'Registro completado exitosamente');
            
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            Log::error('Error en registro de aspirante: ' . $e->getMessage());
            
            return redirect()->back()->withErrors([
                'general' => 'Ocurrió un error durante el registro. Por favor, intente nuevamente más tarde.'
            ])->withInput();
        }
    }
}