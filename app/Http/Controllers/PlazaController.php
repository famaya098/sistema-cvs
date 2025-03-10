<?php

namespace App\Http\Controllers;

use App\Models\Plaza;
use App\Models\Seccion;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlazaController extends Controller
{
    /**
     * Reglas de validación comunes para plaza
     */
    private function getValidationRules()
    {
        return [
            'titulo' => 'required|max:200',
            'id_seccion' => 'required|exists:secciones,id_seccion',
            'id_categoria' => 'required|exists:categorias,id_categoria',
            'contenido_html' => 'required',
            'fecha_inicio_publicacion' => 'required|date',
            'fecha_fin_publicacion' => 'required|date|after:fecha_inicio_publicacion',
            'publicado' => 'boolean',
            'pagina_principal' => 'boolean',
            'estado' => 'required|boolean'
        ];
    }

    /**
     * Prepara los datos de la plaza para guardar
     */
    private function preparePlazaData($validatedData)
    {
        return [
            'titulo' => $validatedData['titulo'],
            'alias_plaza' => \Str::slug($validatedData['titulo']),
            'id_seccion' => $validatedData['id_seccion'],
            'id_categoria' => $validatedData['id_categoria'],
            'publicado' => $validatedData['publicado'] ?? false,
            'estado' => $validatedData['estado'] ?? true,
            'pagina_principal' => $validatedData['pagina_principal'] ?? false,
            'fecha_inicio_publicacion' => $validatedData['fecha_inicio_publicacion'],
            'fecha_fin_publicacion' => $validatedData['fecha_fin_publicacion'],
            'contenido_html' => $validatedData['contenido_html'],
        ];
    }

    public function index(Request $request)
    {
        $query = Plaza::with(['seccion', 'categoria'])
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($request->filled('search')) {
            $query->where('titulo', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        if ($request->filled('seccion')) {
            $query->where('id_seccion', $request->seccion);
        }

        if ($request->filled('estado')) {
            $query->where('publicado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $plazas = $query->paginate(10);
        $secciones = Seccion::where('estado', true)->get();
        $categorias = Categoria::where('estado', true)->get();

        return Inertia::render('Plazas/Index', [
            'plazas' => $plazas,
            'secciones' => $secciones,
            'categorias' => $categorias,
            'filters' => $request->all(['search', 'categoria', 'seccion', 'fecha_desde', 'fecha_hasta', 'estado']),
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->getValidationRules());
        
        $plaza = new Plaza();
        $plaza->fill($this->preparePlazaData($validatedData));
        
        // Verificar si el usuario autenticado es un administrador
        if (auth()->guard('admin')->check()) {
            $admin = auth()->guard('admin')->user();
            $plaza->user_id = $admin->id;
            $plaza->alias_autor = $admin->name;
        } else {
            $user = auth()->user();
            $plaza->user_id = $user->id;
            $plaza->alias_autor = $user->name;
        }
        
        $plaza->save();
    
        return redirect()->back()
            ->with('message', 'Plaza creada exitosamente');
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate($this->getValidationRules());
        
        $plaza = Plaza::findOrFail($id);
        $plaza->fill($this->preparePlazaData($validatedData));
        
        
        $plaza->save();
    
        return redirect()->back()
            ->with('message', 'Plaza actualizada exitosamente');
    }

    public function destroy($id)
    {
        $plaza = Plaza::findOrFail($id);
        $plaza->delete();

        return redirect()->back()
            ->with('message', 'Plaza eliminada exitosamente');
    }

    public function getPublicPlazas(Request $request)
    {
        $query = Plaza::with(['seccion', 'categoria'])
            ->where('publicado', true)
            ->where('estado', true)
            ->where('fecha_inicio_publicacion', '<=', now())
            ->where('fecha_fin_publicacion', '>=', now())
            ->orderBy('created_at', 'desc');

        // Aplicar filtros
        if ($request->filled('search')) {
            $query->where('titulo', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }

        if ($request->filled('seccion')) {
            $query->where('id_seccion', $request->seccion);
        }

        $plazas = $query->paginate(12);
        $secciones = Seccion::where('estado', true)->get();
        $categorias = Categoria::where('estado', true)->get();

        return [
            'plazas' => $plazas,
            'secciones' => $secciones,
            'categorias' => $categorias,
            'filters' => $request->all(['search', 'categoria', 'seccion'])
        ];
    }
    public function showPublic($id)
{
    $plaza = Plaza::with(['seccion', 'categoria'])
        ->where('publicado', true)
        ->where('estado', true)
        ->where('fecha_inicio_publicacion', '<=', now())
        ->where('fecha_fin_publicacion', '>=', now())
        ->findOrFail($id);

    // Incrementar el contador de accesos
    $plaza->increment('accesos');

    return Inertia::render('Public/Plazas/Show', [
        'plaza' => $plaza
    ]);
}

    
}