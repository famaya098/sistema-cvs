<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Documento;

class CleanupS3Storage extends Command
{
    protected $signature = 'cleanup:s3 {--force : Forzar la eliminación sin confirmación}';
    protected $description = 'Elimina todos los archivos de CV almacenados en S3';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('¿Estás seguro de querer eliminar TODOS los archivos CV de S3? Esta acción no se puede deshacer.')) {
            $this->info('Operación cancelada.');
            return;
        }

        $this->info('Iniciando limpieza de archivos en S3...');

        // Opción 1: Borrar todos los archivos en la carpeta curriculums
        $prefix = 'curriculums/';
        $files = Storage::disk('s3')->allFiles($prefix);
        
        $count = count($files);
        $this->info("Se encontraron {$count} archivos para eliminar.");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        foreach ($files as $file) {
            Storage::disk('s3')->delete($file);
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Todos los archivos han sido eliminados de S3.');
        
        // Opción alternativa: Borrar solo archivos registrados en la base de datos
        /*
        $documentos = Documento::whereHas('tipoDocumento', function($query) {
            $query->where('nombre', 'Curriculum Vitae');
        })->get();
        
        $count = $documentos->count();
        $this->info("Se encontraron {$count} documentos CV en la base de datos.");
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        foreach ($documentos as $documento) {
            if (Storage::disk('s3')->exists($documento->ruta)) {
                Storage::disk('s3')->delete($documento->ruta);
            }
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Todos los documentos CV han sido eliminados de S3.');
        */
        
        $this->info('Limpieza completada.');
    }
}