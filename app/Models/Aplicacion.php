<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aplicacion extends Model
{
    protected $table = 'aplicaciones';
    protected $primaryKey = 'id_aplicacion';
    
    public $timestamps = false;
    
    protected $fillable = [
        'id_aspirante',
        'id_plaza',
        'fecha_aplicacion',
        'id_cv',
        'id_documento',
        'id_estado_aplicacion',
    ];
    
    // Relaciones
    public function aspirante()
    {
        return $this->belongsTo(UsuarioPerfil::class, 'id_aspirante');
    }
    
    public function plaza()
    {
        return $this->belongsTo(Plaza::class, 'id_plaza');
    }
    
    public function documento()
    {
        return $this->belongsTo(Documento::class, 'id_documento');
    }
    
    public function estado()
    {
        return $this->belongsTo(EstadoAplicacion::class, 'id_estado_aplicacion');
    }
}