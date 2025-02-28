<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    protected $table = 'secciones';
    protected $primaryKey = 'id_seccion';
    
    protected $fillable = ['nombre', 'estado'];

    public $timestamps = false;

    protected $casts = [
        'estado' => 'boolean'
    ];
}
