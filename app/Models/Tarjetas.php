<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarjetas extends Model
{
    use HasFactory;
    protected $table = 'tarjetabb';

    protected $fillable = [

        'usuario_id',
        'numero_tarjeta',
        'expiracion',
        'cvv',
        'fecha_registro',
        'banco',
    ];
    
      public $timestamps = false;
}
