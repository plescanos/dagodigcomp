<?php

namespace App\Orchid\Screens\Upload;

//require_once 'vendor/autoload.php';




use Orchid\Screen\Screen;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DigCompImport;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Attach;
use Illuminate\Database\QueryException;

use App\Models\Empresa;

use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Layout;

class UploadScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [

        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Crear empresa';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Input::make('nombre_empresa')
                    ->type('text')
                    ->title('Nombre de la empresa')
                    ->placeholder('Nombre de la empresa'),
                   /*  ->required(), */
                Input::make('email')
                   ->type('email')
                   ->title('Email')
                   ->placeholder('Correo electrónico'),
                  /*  ->required(), */
                Input::make('descripcion')
                    ->type('text')
                    ->title('Breve descripción de la empresa'),
                /*     ->required(), */
                Input::make('poblacion')
                    ->type('number')
                    ->title('Población (Universo)'),
                   /*  ->required(), */
                Input::make('muestra')
                    ->type('number')
                    ->title('Tamaño de muestra'),
                 /*    ->required(), */
                Input::make('datos_digcomp')
                    ->type('file')
                    ->title('Subir archivo')
                    ->maxCount(1)
                    ->maxSize(2048)
                    ->accept('.xlsx, .csv')
                    ->errorMaxSizeMessage("File size is too large")
                    ->errorTypeMessage("Invalid file type")
                    ->path('/storage'),
                Button::make('Cargar')
                    ->method('upload')
                    ->icon('cloud-upload')


            ])

        ];
    }

    public function upload(Request $request)
    {

        $archivo = $request->file('datos_digcomp');

        $array_keywords_preguntas = collect([
            'ObjectID' => ['ObjectID'],
            'GlobalID' => ['GlobalID'],
            'CreationDate' => ['CreationDate'],
            'Creator' => ['Creator'],
            'EditDate' => ['EditDate'],
            'Editor' => ['Editor'],
            'Genero' => ['Género'],
            'Edad' => ['Edad'],
            'Estudios' => ['mayor', 'nivel', 'educación', ],
            'pregunta_1' =>  ['editar', 'contenido', 'digital'],
            'pregunta_2' =>  ['guardar', 'documento', 'formato'],
            'pregunta_3' =>  ['crear', 'contenidos', 'Smartphones'],
            'pregunta_4' =>  ['vídeo', 'tutorial', 'YouTube'],
            'pregunta_5' =>  ['presentación', 'digital', 'animada'],
            'pregunta_6' =>  ['herramientas', 'crear', 'encuestas'],
            'pregunta_7' =>  ['aplicaciones', 'publicar', 'videos'],
            'pregunta_8' =>  ['actualizar', 'presentación', 'añadiendo'],
            'pregunta_9' =>  ['crear', 'infografías', 'carteles'],
            'pregunta_10' => ['herramientas', 'mejorar', 'accesibilidad'],
            'pregunta_11' => ['programas', 'realizar', 'publicaciones'],
            'pregunta_12' => ['crear', 'blog', 'WordPress'],
            'pregunta_13' => ['herramienta', 'crear', 'contenido'],
            'pregunta_14' => ['edición', 'imágenes', 'fotografías'],
            'pregunta_15' => ['limitaciones', 'legales', 'compartir'],
            'pregunta_16' => ['tipos', 'licencias', 'software'],
            'pregunta_17' => ['identificar', 'seleccionar', 'contenidos'],
            'pregunta_18' => ['contenido', 'digital', 'protegido'],
            'pregunta_19' => ['símbolo', 'licencia', 'Creative'],
            'pregunta_20' => ['utilizar', 'recurso', 'diagrama'],
            'pregunta_21' => ['Licencia', 'Creative', 'Commons'],
            'pregunta_22' => ['crear', 'lista', 'instrucciones'],
            'pregunta_23' => ['lenguaje', 'programación', 'desarrollar'],
            'pregunta_24' => ['interfaz', 'programación', 'Scratch'],
            'pregunta_25' => ['depurar', 'programa', 'soluciono'],
            'pregunta_26' => ['detectar', 'errores', 'secuencia'],
            'pregunta_27' => ['lenguaje', 'programación', 'crear'],
            'pregunta_28' => ['lenguajes', 'programación', 'elaborar']
        ]);


        if ($request->hasFile('datos_digcomp')) {
            
            try {
                
                $collection = Excel::toCollection(new DigCompImport($request), $archivo);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }
            
            
            $new_preguntas = $this->procesadorPreguntas($array_keywords_preguntas, $collection[0]->first());
            $nu_collection = $collection[0]->put(0, $new_preguntas);
            
            $nombresPreguntas = $nu_collection->first();
          
        //procesamos la estructura de datos
        foreach ($nu_collection->slice(1) as $key => $evaluacion) {
            
            // Agrupa todas las rspustas a su respectiva pregutna   
            foreach ($evaluacion as $index => $respuestas) {

                $pregunta = $nombresPreguntas[$index];
              
                $respuestasGrouped[$pregunta][] = $respuestas;
                
            }
            
        }

        //Realizamos el conteo por respuesta de todo el universo
        foreach ($respuestasGrouped as $preguntas => $value) {
            
            $contados[$preguntas] = collect($value)->countBy();

        }

    
        $json_data = collect($contados)->slice(6)->toJson();

        $data_fields = [
            'nombre_empresa' => $request->nombre_empresa,
            'email' => $request->email,
            'descripcion' => $request->descripcion,
            'poblacion' => $request->poblacion,
            'muestra' => $request->muestra,
            'json_data' => $json_data,

        ];


        $email_validated = $request->validate([
            'email' => 'required|unique:empresas'
        ], 
        ['email.unique' => 'El correo electrónico ya está en uso. Por favor, elige otro.',]);



            if ($this->checkPoblacion($request->muestra, (count($collection[0]) - 1))) {

                try {
                    $this->pushToDB($data_fields);
                } catch (\QueryException $q) {
                    if ($q->getCode == 23000) {
                        
                        //Alert::warning('El email ya está en uso. Es posible que la empresa ya haya sido agregada o que el correo deba corregirse.');
                        return back()->with('error', 'El correo electrónico ya está en uso.');
                    }
                }
                

            } else {
                Alert::warning('La población introducida no coincide con las encuestas que se van a cargar.');
            };


        } else {
            Alert::warning('No se ha seleccionado un archivo');
        }

    }

    protected function procesadorPreguntas ($agujas, $pajar) {
        
        $items_procesados = $pajar->map(function($pregunta) use ($agujas) {
            foreach ($agujas as $clave => $palabras) {
                        // Verificar si la pregunta contiene todas las palabras clave
                if (collect($palabras)->every(function ($palabra) use ($pregunta) {
                        return stripos($pregunta, $palabra) !== false;
                    })) 
                    {
                        // Si coincide, reemplazar la pregunta con la clave
                        return $clave;
                    }
            }
        });




        //dd(vars: $items_procesados);

        return $items_procesados;
    }

    public function pushToDB ($data) {
        
        $empresa = Empresa::create([
            'nombre_empresa' => $data['nombre_empresa'],
            'email' => $data['email'],
            'descripcion' => $data['descripcion'],
            'poblacion' => $data['poblacion'],
            'muestra' => $data['muestra'],
            'json_data' => $data['json_data'],
        ]);
    }

    protected function checkPoblacion ($poblacion, $encuestas) {
        if ($poblacion == $encuestas) {
            return true;
        } else {
            return false;
            
        }
    }
}
