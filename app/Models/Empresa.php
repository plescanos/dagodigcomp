<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;


use Illuminate\Support\Facades\DB;

class Empresa extends Model
{
    use Searchable, Filterable, AsSource;

    protected $table = 'empresas';
    
    protected $fillable = ['nombre_empresa', 'email', 'descripcion', 'poblacion', 'muestra', 'json_data'];



    //
}
