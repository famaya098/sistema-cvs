<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plaza extends Model
{
    use SoftDeletes;
    
    protected $table = 'plazas';
    protected $primaryKey = 'id_plaza';
    
    protected $fillable = [
        'titulo',
        'alias_plaza',
        'id_seccion',
        'id_categoria',
        'publicado',
        'estado',
        'pagina_principal',
        'user_id',            
        'alias_autor',
        'id_nivel_acceso',
        'fecha_inicio_publicacion',
        'fecha_fin_publicacion',
        'contenido_html',
        'accesos',
        'id_nivel_academico_requerido', 
        'id_estado_academico_requerido',
        'id_experiencia_requerido'    
    ];

    protected $casts = [
        'publicado' => 'boolean',
        'estado' => 'boolean',
        'pagina_principal' => 'boolean',
        'fecha_inicio_publicacion' => 'date:Y-m-d', 
        'fecha_fin_publicacion' => 'date:Y-m-d', 
        'accesos' => 'integer'
    ];

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'id_seccion');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    

    public function aplicaciones()
    {
        return $this->hasMany(Aplicacion::class, 'id_plaza');
    }

    public function nivelAcademicoRequerido()
    {
        return $this->belongsTo(NivelAcademico::class, 'id_nivel_academico_requerido');
    }

    public function estadoAcademicoRequerido()
    {
        return $this->belongsTo(EstadoAcademico::class, 'id_estado_academico_requerido');
    }

    public function experienciaRequerida()
    {
        return $this->belongsTo(NivelExperiencia::class, 'id_experiencia_requerido');
    }
}