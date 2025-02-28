<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoAcademico extends Model
{
    protected $table = 'estados_academicos';
    protected $primaryKey = 'id';
    
    protected $fillable = ['nombre', 'estado'];
    
    // No timestamps
    public $timestamps = false;
    
    protected $casts = [
        'estado' => 'boolean',
    ];
}