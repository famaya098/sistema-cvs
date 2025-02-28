<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioDocumento extends Model
{
    protected $table = 'usuario_documentos';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'id_aspirante',
        'id_documento',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    public function aspirante()
    {
        return $this->belongsTo(UsuarioPerfil::class, 'id_aspirante');
    }
    
    public function documento()
    {
        return $this->belongsTo(Documento::class, 'id_documento');
    }
}