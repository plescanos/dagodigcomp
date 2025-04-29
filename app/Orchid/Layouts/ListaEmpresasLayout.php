<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;

class ListaEmpresasLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'empresas';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('nombre_empresa', 'Nombre'),
            TD::make('email', 'Email'),
            TD::make('descripcion', 'Descripción'),
            TD::make('poblacion', 'Población'),
            TD::make('muestra', 'Muestra'),
            TD::make('email', 'ICD')
                ->render(
                    fn($empresa) => Link::make('Ver')->route('digcomp.charts', $empresa->email)
                    
                )
        ];
    }
}
