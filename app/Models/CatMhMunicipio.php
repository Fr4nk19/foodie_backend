<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatMhMunicipio extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cat_mh_municipio';

    protected $fillable = [
        'cat_mh_departamento_id',
        'codigo',
        'descripcion',
    ];

    public function departamento()
    {
        return $this->belongsTo(CatMhDepartamento::class, 'cat_mh_departamento_id');
    }
}
