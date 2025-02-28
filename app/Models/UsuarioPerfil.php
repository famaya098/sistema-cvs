<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsuarioPerfil extends Model
{
    use SoftDeletes;
    
    protected $table = 'usuario_perfil';
    protected $primaryKey = 'id_aspirante';
    
    protected $fillable = [
        'user_id',
        'dui_aspirante',
        'nombre_completo',
        'genero',
        'email',
        'telefono',
        'carrera',
        'especialidad',
        'id_nivel_academico',
        'id_estado_academico',
        'id_experiencia',
        'estado',
    ];
    
    protected $dates = ['deleted_at'];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function nivelAcademico()
    {
        return $this->belongsTo(NivelAcademico::class, 'id_nivel_academico', 'id');
    }
    
    public function estadoAcademico()
    {
        return $this->belongsTo(EstadoAcademico::class, 'id_estado_academico', 'id');
    }
    
    public function experiencia()
    {
        return $this->belongsTo(NivelExperiencia::class, 'id_experiencia', 'id');
    }
    
    public function documentos()
    {
        return $this->hasMany(UsuarioDocumento::class, 'id_aspirante');
    }
}