<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelExperiencia extends Model
{
    protected $table = 'niveles_experiencia';
    protected $primaryKey = 'id';
    
    protected $fillable = ['nombre', 'estado'];
    
    // No timestamps
    public $timestamps = false;
    
    protected $casts = [
        'estado' => 'boolean',
    ];
}