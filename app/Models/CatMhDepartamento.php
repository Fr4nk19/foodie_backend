<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatMhDepartamento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cat_mh_departamento';

    protected $fillable = [
        'codigo',
        'descripcion',
    ];
}
