<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cursoreg extends Model
{
    use HasFactory;

    protected $table = 'cursos_reg';

    protected $fillable = ['id_curso', 'id_user'];

    public $timestamps = false;
}
