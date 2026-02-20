<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatMhTipoEstablecimiento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cat_mh_tipo_establecimiento';

    protected $fillable = [
        'codigo',
        'descripcion',
    ];
}
