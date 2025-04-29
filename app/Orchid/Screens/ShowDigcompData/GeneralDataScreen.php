<?php

namespace App\Orchid\Screens\ShowDigcompData;

use App\Orchid\Layouts\Digcomp\ChartBar;
use App\Orchid\Layouts\Digcomp\ChartLine;
use App\Orchid\Layouts\Digcomp\PieEdadLayout;
use App\Orchid\Layouts\Digcomp\PieGeneroLayout;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\Currency;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;


use Illuminate\Support\Facades\DB;

use App\Models\Empresa;

class GeneralDataScreen extends Screen
{
    /**
     * Fish text for the table.
     */
    public const TEXT_EXAMPLE = 'Lorem ipsum at sed ad fusce faucibus primis, potenti inceptos ad taciti nisi tristique
    urna etiam, primis ut lacus habitasse malesuada ut. Lectus aptent malesuada mattis ut etiam fusce nec sed viverra,
    semper mattis viverra malesuada quam metus vulputate torquent magna, lobortis nec nostra nibh sollicitudin
    erat in luctus.';

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */

    protected $email;
    public function query($email): iterable
    {
        $this->email = $email;
        $datos_empresa = DB::table('empresas')->where('email', $email)->first();
        
        $edades_array = $this->getEdades($datos_empresa);
        $generos_array = $this->getGeneros($datos_empresa);

       
        return [
            'gen' => [
                [
                    'values' => $generos_array['values'],
                    'labels' => $generos_array['labels'],
                    
                ]
            ],

            'edades'  => [
                [
                    'values' => $edades_array['values'],
                    'labels' => $edades_array['labels'],
                    
                    
                ]
            ],

            'educacion'  => [
                [
                    'values' => $edades_array['values'],
                    'labels' => $edades_array['labels']
                ]
            ],

/*           





                [
                    'name'   => 'Género',
                    'values' => [25],
                    'labels' => ['12am-3am'],
                ],
                [
                    'name'   => 'Nivel de Estudios',
                    'values' => [15],
                    'labels' => ['12am-3am'],
                ],
            ],
            [
                'genero' => [
                    'name'   => 'Género',
                    'values' => [21, 13],
                    'labels' => ['ee', 'uu'],
                ],
            ],
                   





            'table'   => [
                new Repository(['id' => 100, 'name' => self::TEXT_EXAMPLE, 'price' => 10.24, 'created_at' => '01.01.2020']),
                new Repository(['id' => 200, 'name' => self::TEXT_EXAMPLE, 'price' => 65.9, 'created_at' => '01.01.2020']),
                new Repository(['id' => 300, 'name' => self::TEXT_EXAMPLE, 'price' => 754.2, 'created_at' => '01.01.2020']),
                new Repository(['id' => 400, 'name' => self::TEXT_EXAMPLE, 'price' => 0.1, 'created_at' => '01.01.2020']),
                new Repository(['id' => 500, 'name' => self::TEXT_EXAMPLE, 'price' => 0.15, 'created_at' => '01.01.2020']),

            ],

 */
            'metrics' => [
                'poblacion'    => $datos_empresa->poblacion,
                'muestra' => $datos_empresa->muestra,
                'nombre_empresa'   => $datos_empresa->nombre_empresa,
            ],


        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Datos Demográficos de la Empresa';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Edad, género y nivel educativo';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Datos DigComp')
                ->icon('bar-chart')
                ->route('digcomp.charts', $this->email)
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
            Layout::metrics([
                'Empresa'    => 'metrics.nombre_empresa',
                'Población'    => 'metrics.poblacion',
                'Muestra' => 'metrics.muestra',
            ]),

            Layout::columns([
                PieEdadLayout::make('edades', 'Edades')
                ->description('Comparativa de edades'),

                PieEdadLayout::make('gen', 'Género')
                ->description('Comparativa de género'),

                PieEdadLayout::make('educacion', 'Educación')
                ->description('Nivel educativo'),


                    


/*                 ChartLine::make('genero', 'Género')
                    ->description('Visualización de las frecuencias de edades por rangos.'),

                ChartBar::make('questions', 'Bar Chart')
                    ->description('Compare data sets with colorful bar graphs.'), */
            ]),

 /*            Layout::table('table', [
                TD::make('id', 'ID')
                    ->width('100')
                    ->render(fn (Repository $model) => // Please use view('path')
                    "<img src='https://loremflickr.com/500/300?random={$model->get('id')}'
                              alt='sample'
                              class='mw-100 d-block img-fluid rounded-1 w-100'>
                            <span class='small text-muted mt-1 mb-0'># {$model->get('id')}</span>"),

                TD::make('name', 'Name')
                    ->width('450')
                    ->render(fn (Repository $model) => Str::limit($model->get('name'), 200)),

                TD::make('price', 'Price')
                    ->width('100')
                    ->usingComponent(Currency::class, before: '$')
                    ->align(TD::ALIGN_RIGHT),

                TD::make('created_at', 'Created')
                    ->width('100')
                    ->usingComponent(DateTimeSplit::class)
                    ->align(TD::ALIGN_RIGHT),
            ]), */

            Layout::modal('exampleModal', Layout::rows([
                Input::make('toast')
                    ->title('Messages to display')
                    ->placeholder('Hello world!')
                    ->help('The entered text will be displayed on the right side as a toast.')
                    ->required(),
            ]))->title('Create your own toast message'),
        ];
    }

    public function showToast(Request $request): void
    {
        Toast::warning($request->get('toast', 'Hello, world! This is a toast message.'));
    }

    protected function getEdades ($datos_empresa) {
        
        $json_data = json_decode($datos_empresa->json_data)->Edad;
        
        $e_down_30 = 0;
        $e_31_40 = 0;
        $e_41_50 = 0;
        $e_over_50 = 0;

        foreach ($json_data as $key => $value) {
            if ($key <= 30) {
                $e_down_30 += $value;
            }
            if ($key > 30 && $key <= 40) {
                $e_31_40 += $value;
            }
            if ($key > 40 && $key <= 50) {
                $e_41_50 += $value;
            }
            if ($key > 50) {
                $e_over_50 += $value;
            }
        }

        return [
            'labels' => ['< 30', '31 - 40', '41 - 50', '> 50'],
            'values' => [$e_down_30, $e_31_40, $e_41_50, $e_over_50]
        ];

    } 

    protected function getGeneros ($datos_empresa) {
        $json_data = json_decode($datos_empresa->json_data)->Genero;

        
        foreach ($json_data as $key => $value) {
            $labels[] = $key;
            $values[] = $value;
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];

     }


}
