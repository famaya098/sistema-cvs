<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelAcademico extends Model
{
    protected $table = 'niveles_academicos';
    protected $primaryKey = 'id';
    
    protected $fillable = ['nombre', 'orden', 'estado', 'requiere_especialidad'];
    
    public $timestamps = false;
    
    protected $casts = [
        'estado' => 'boolean',
        'requiere_especialidad' => 'boolean',
    ];
}