<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id_categoria';
    
    protected $fillable = ['nombre', 'estado'];

    public $timestamps = false;

    protected $casts = [
        'estado' => 'boolean'
    ];
}