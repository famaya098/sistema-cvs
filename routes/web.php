<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PlazaController;
use App\Http\Controllers\Admin\AdminAuthController;
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
        'scroll_top' => session('scroll_top', false),
    ]);
})->name('welcome');

// Rutas específicas para administradores (login, logout)
// Nota: La URL del login de admin está oculta con un nombre secreto
Route::middleware('guest:admin')->group(function () {
    Route::get('/bcr-admin', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/bcr-admin', [AdminAuthController::class, 'login']);
});

// Rutas para administradores (panel de administración)
Route::middleware('admin')->group(function () {
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    // Dashboard 
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Ruta para la gestión de plazas
    Route::resource('plazas', PlazaController::class);

    // Rutas para gestionar aplicaciones
    Route::get('/admin/aplicaciones', [App\Http\Controllers\Admin\AplicacionesAdminController::class, 'index'])
        ->name('admin.aplicaciones.index');
    Route::patch('/admin/aplicaciones/{id}/estado', [App\Http\Controllers\Admin\AplicacionesAdminController::class, 'updateEstadoAdmin'])
        ->name('admin.aplicaciones.updateEstado');
    Route::get('/admin/aplicaciones/{id}/cv', [App\Http\Controllers\Admin\AplicacionesAdminController::class, 'downloadCV'])
        ->name('admin.aplicaciones.download.cv');

    // Rutas para gestionar aspirantes
    Route::get('/admin/aspirantes', [App\Http\Controllers\Admin\AplicacionesAdminController::class, 'aspirantes'])
    ->name('admin.aspirantes.index');
    Route::get('/admin/aspirantes/{id}/cv', [App\Http\Controllers\Admin\AplicacionesAdminController::class, 'verCV'])
    ->name('admin.aspirantes.cv');
});

// Ruta pública para ver detalles de una plaza
Route::get('/plazas/{plaza}/show', [PlazaController::class, 'showPublic'])
    ->name('plazas.show.public');

// Rutas para aspirantes autenticados
Route::middleware(['auth:web'])->group(function () {
    
    
    // Rutas para el perfil del aspirante
    Route::get('/aspirante/perfil', [App\Http\Controllers\AspirantePerfilController::class, 'show'])
        ->name('aspirante.perfil');
    Route::post('/aspirante/perfil/info', [App\Http\Controllers\AspirantePerfilController::class, 'updateInfo'])
        ->name('aspirante.perfil.info');
    Route::post('/aspirante/perfil/password', [App\Http\Controllers\AspirantePerfilController::class, 'updatePassword'])
        ->name('aspirante.perfil.password');
    Route::get('/aspirante/perfil/cv/download', [App\Http\Controllers\AspirantePerfilController::class, 'downloadCV'])
        ->name('aspirante.perfil.cv.download');
    Route::post('/aspirante/perfil/cv/update', [App\Http\Controllers\AspirantePerfilController::class, 'updateCV'])
        ->name('aspirante.perfil.cv.update');
    
    // Rutas para aplicaciones
    Route::post('/plazas/{plaza}/aplicar', [App\Http\Controllers\AplicacionController::class, 'aplicar'])
        ->name('aplicaciones.aplicar');
    Route::get('/plazas/{plaza}/verificar-aplicacion', [App\Http\Controllers\AplicacionController::class, 'verificarAplicacion'])
        ->name('aplicaciones.verificar');

    Route::get('/mis-aplicaciones', [App\Http\Controllers\AplicacionController::class, 'misAplicaciones'])
    ->name('aspirante.aplicaciones');
});

// Rutas para el registro extendido de aspirantes
Route::middleware('guest:web')->group(function () {
    Route::get('/registro-aspirante', [App\Http\Controllers\AspiranteRegistroController::class, 'create'])
        ->name('registro.aspirante');

    Route::post('/registro-aspirante', [App\Http\Controllers\AspiranteRegistroController::class, 'store'])
        ->name('registro.aspirante.store');
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

require __DIR__.'/auth.php';