<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Illuminate\Support\Facades\DB;
use App\Orchid\Layouts\ListaEmpresasLayout;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use App\Models\Empresa;



class ListaEmpresasScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */

    /**
     * @var Empresa
     */
    public $empresa;

    public function query(): iterable
    {
        
        
        return [
            'empresas' => Empresa::all(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Lista de Empresas';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [

        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            ListaEmpresasLayout::class
            
        ];
    }
}
