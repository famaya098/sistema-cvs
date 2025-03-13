<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoAplicacion extends Model
{
    protected $table = 'estados_aplicacion';
    protected $primaryKey = 'id';
    
    public $timestamps = false;
    
    protected $fillable = [
        'nombre',
        'estado',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
    ];
}