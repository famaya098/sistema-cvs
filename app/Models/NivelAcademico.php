<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelAcademico extends Model
{
    protected $table = 'niveles_academicos';
    protected $primaryKey = 'id';
    
    protected $fillable = ['nombre', 'estado', 'requiere_especialidad'];
    
    // No timestamps
    public $timestamps = false;
    
    protected $casts = [
        'estado' => 'boolean',
        'requiere_especialidad' => 'boolean',
    ];
}