<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoAcademico extends Model
{
    protected $table = 'estados_academicos';
    protected $primaryKey = 'id';
    
    protected $fillable = ['nombre', 'orden', 'estado'];
    
    public $timestamps = false;
    
    protected $casts = [
        'estado' => 'boolean',
    ];
}