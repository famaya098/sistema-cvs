<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlazaController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;

// Ruta pública (página de inicio)
Route::get('/', function (Request $request) {
    $plazaController = new PlazaController();
    $plazasData = $plazaController->getPublicPlazas($request);
    
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'plazas' => $plazasData['plazas'],
        'secciones' => $plazasData['secciones'],
        'categorias' => $plazasData['categorias'],
        'filters' => $plazasData['filters'],
    ]);
})->name('welcome');

// Ruta pública para ver detalles de una plaza
Route::get('/plazas/{plaza}/show', [PlazaController::class, 'showPublic'])
    ->name('plazas.show.public');

// Rutas que requieren autenticación
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Rutas para el perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para la gestión de plazas 
    Route::resource('plazas', PlazaController::class);

    // Ruta para aplicar a una plaza
    Route::post('/plazas/{plaza}/apply', [PlazaController::class, 'apply'])
        ->name('plazas.apply');
});

Route::get('/test-s3-connection', function() {
    try {
        //  listar los archivos en el bucket
        $files = Storage::disk('s3')->allFiles();
        
        return [
            'success' => true,
            'message' => 'Conexión establecida correctamente con S3',
            'files_count' => count($files),
            'bucket' => config('filesystems.disks.s3.bucket'),
            'region' => config('filesystems.disks.s3.region')
        ];
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Error al conectar con S3: ' . $e->getMessage()
        ];
    }
});

// Rutas para el registro extendido de aspirantes
Route::middleware('guest')->group(function () {
    Route::get('/registro-aspirante', [App\Http\Controllers\AspiranteRegistroController::class, 'create'])
        ->name('registro.aspirante');

    Route::post('/registro-aspirante', [App\Http\Controllers\AspiranteRegistroController::class, 'store'])
        ->name('registro.aspirante.store');
});

Route::get('/registro-test', function () {
    return Inertia::render('Auth/RegisterExtended', [
        'nivelesAcademicos' => [],
        'estadosAcademicos' => [],
        'nivelesExperiencia' => [],
    ]);
})->name('registro.test');

require __DIR__.'/auth.php';