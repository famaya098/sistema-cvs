<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipos_documentos';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
    ];
    
    // No timestamps
    public $timestamps = false;
}