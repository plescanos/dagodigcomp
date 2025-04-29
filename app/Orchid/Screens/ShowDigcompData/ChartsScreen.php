<?php

namespace App\Orchid\Screens\ShowDigcompData;

use App\Orchid\Layouts\Examples\ChartBarExample;
use App\Orchid\Layouts\Examples\ChartLineExample;
use App\Orchid\Layouts\Examples\ChartPercentageExample;
use App\Orchid\Layouts\Examples\ChartPieExample;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;

use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

class ChartsScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */

     protected $muestra;

     protected $email;
    protected $frecuencia_respuestas;



    public function query($email): iterable
    {
        $this->email = $email;

        $json_data = DB::table('empresas')->where('email', $email)->first();
        $data_preguntas_encuestas = collect(json_decode($json_data->json_data))->slice(4);
        $preguntas_db = DB::table('preguntas')->get();

        $this->muestra = $json_data->muestra;
     

        $preguntas_para_barras = $this->buildPreguntasData($preguntas_db, $data_preguntas_encuestas);
        //dd(collect($preguntas_para_barras['frecuencia_respuestas'])->slice(0, 5));

        $competencia_1 = $this->calculaIndiceCompetencia(collect($preguntas_para_barras['frecuencia_respuestas'])->slice(0, 5));
        $competencia_2 = $this->calculaIndiceCompetencia(collect($preguntas_para_barras['frecuencia_respuestas'])->slice(5, 5));
        $competencia_3 = $this->calculaIndiceCompetencia(collect($preguntas_para_barras['frecuencia_respuestas'])->slice(10, 5));
        $competencia_4 = $this->calculaIndiceCompetencia(collect($preguntas_para_barras['frecuencia_respuestas'])->slice(15, 5));

        return [
            'charts' => [
                [
                    'name'   => 'Competencia: Contenido Digital',
                    'values' => [$competencia_1['nivel_neto'], $competencia_2['nivel_neto'], $competencia_3['nivel_neto'], $competencia_4['nivel_neto']],
                    'labels' => ['Desarrollo', 'Integración y reelaboración', 'Propiedad Intelectual', 'Programación'],
                ]
/*                 [
                    'name'   => 'Another Set',
                    'values' => [25, 50, -10, 15, 18, 32, 27],
                    'labels' => ['12am-3am', '3am-6am', '6am-9am', '9am-12pm', '12pm-3pm', '3pm-6pm', '6pm-9pm'],
                ],
                [
                    'name'   => 'Yet Another',
                    'values' => [15, 20, -3, -15, 58, 12, -17],
                    'labels' => ['12am-3am', '3am-6am', '6am-9am', '9am-12pm', '12pm-3pm', '3pm-6pm', '6pm-9pm'],
                ],
                [
                    'name'   => 'And Last',
                    'values' => [10, 33, -8, -3, 70, 20, -34],
                    'labels' => ['12am-3am', '3am-6am', '6am-9am', '9am-12pm', '12pm-3pm', '3pm-6pm', '6pm-9pm'],
                ], */
            ],
            'enteros' => [
                [
                    'name'   => 'Competencia: Contenido Digital',
                    'values' => [$competencia_1['nivel_decimal'], $competencia_2['nivel_decimal'], $competencia_3['nivel_decimal'], $competencia_4['nivel_decimal']],
                    'labels' => ['Desarrollo', 'Integración y reelaboración', 'Propiedad Intelectual', 'Programación'],    
                ]
]
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): ?string
    {
        return 'Datos DigComp';
    }

    /**
     * Display header description.
     */
    public function description(): ?string
    {
        return 'Para el área cmpetencias correspondientes a "Creación de Contenidos Digitales"';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make('Datos Demográficos')
                ->icon('people')
                ->route('digcomp.general.data', $this->email)
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @throws \Throwable
     *
     * @return string[]|\Orchid\Screen\Layout[]
     */
    public function layout(): iterable
    {
        return [
/*             ChartLineExample::make('charts', 'Actions with a Tweet')
                ->description('The total number of interactions a user has with a tweet. This includes all clicks on any links in the tweet (including hashtags, links, avatar, username, and expand button), retweets, replies, likes, and additions to the read list.'), */

            Layout::columns([
/*                 ChartLineExample::make('charts', 'Line Chart')
                    ->description('Visualize data trends with multi-colored line graphs.'), */
                ChartBarExample::make('charts', 'Niveles por competencia')
                    ->description('Comparativo de niveles por competencia (valores enteros)'),
            ]),

            Layout::columns([
                ChartPercentageExample::make('enteros', 'Barra de porcentajes')
                ->description('Valores con dos decimales'),

                ChartPieExample::make('enteros', 'Pastel')
                ->description('Valores con dos decimales'),
                
            ]),
        ];
    }

    protected function buildPreguntasData ($preguntas, $respuestas_encuestas) {
        //dd($preguntas);


        
        foreach ($preguntas as $key => $pregunta) {
            $numero[] = $pregunta->numero_pregunta;
            $titulos_preguntas[] = $pregunta->pregunta;
            $slug[] = $pregunta->slug;
            $competencia[] = $pregunta->competencia_id;
            $tipo_pregunta[] = $pregunta->tipo_pregunta_id;

            $count = 0;
            foreach ($respuestas_encuestas as $key_2 => $respuesta) {

                if ($pregunta->slug == $key_2) {
                    
                        
                        $frecuencia_respuestas[] = $respuesta;
                    
                    
                }
            }



        }
        //dd($frecuencia_respuestas);
        $datos_reordenados = [
            'numero_pregunta' => $numero, 
            'competencia' => $competencia,
            'tipo_pregunta' => $tipo_pregunta,
            'titulo_pregunta' => $titulos_preguntas,
            'slug' => $slug,
            'frecuencia_respuestas' => $frecuencia_respuestas
        ];

        return $datos_reordenados;

    }

    protected function calculaIndiceCompetencia ($respuestas_array) {

        $keywords_respuestas = [
            'keywords' => [
                ['no', 'se'],
                ['con', 'ayuda'],
                ['mi', 'cuenta'],
                ['ayudo', 'otros'],
            ],

            'puntos' => [1, 2, 3, 4]
        ];

        //Convierte a todas las respuestas en un solo array asociativo con su valor
        $pajar_respuestas = collect([]);
        foreach ($respuestas_array as $respuesta => $frecuencia) {
            $multiplo = 0;
            foreach ($frecuencia as $clave => $valor) {



                if (str_contains($clave, 'No sé')) {
                    $multiplo = 1;
                } elseif (str_contains($clave, 'con ayuda')) {
                    $multiplo = 2;
                } elseif (str_contains($clave, 'mi cuenta')) {
                    $multiplo = 3;
                } elseif (str_contains($clave, 'a otros')) {
                    $multiplo = 4;
                } else {
                    $multiplo = 0;
                }

                $pajar_respuestas->push(['clave' => $clave, 'valor' => $valor, 'multiplo' => $multiplo, 'producto' => ($valor * $multiplo)]);
                
            }
        }

        $suma = 0;
        foreach ($pajar_respuestas as $key => $value) {
            $suma += $value['producto'];
        }

        $idc = $suma / ($this->muestra * 5);

        
        return ['nivel_neto' => intval(round($idc, 0)), 'nivel_decimal' => round($idc, 2)];

    }
}
