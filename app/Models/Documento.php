<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_tipo_documento',
        'ruta',
        'nombre_archivo',
        'estado',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
    ];
    
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'id_tipo_documento');
    }
}