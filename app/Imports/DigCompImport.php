<?php

namespace App\Imports;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


use App\Models\Empresa;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;


class DigCompImport implements ToCollection
{
    /**
    * @param Collection $collection
    */

    public $array_keywords_preguntas;

    protected $nombre_empresa;
    protected $descripcion;
    protected $poblacion;
    protected $muestra;
    protected $json_data;

    public function __construct(Request $request) {

        $this->nombre_empresa = $request->nombre_empresa;
        $this->descripcion = $request->descripcion;
        $this->poblacion = $request->poblacion;
        $this->muestra = $request->muestra;

        $this->array_keywords_preguntas = collect([
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
            'pregunta_15' => ['agregar', 'canción', 'vídeo'],
            'pregunta_16' => ['limitaciones', 'legales', 'compartir'],
            'pregunta_17' => ['tipos', 'licencias', 'software'],
            'pregunta_18' => ['identificar', 'seleccionar', 'contenidos'],
            'pregunta_19' => ['contenido', 'digital', 'protegido'],
            'pregunta_20' => ['bancos', 'imágenes', 'gratuitas'],
            'pregunta_21' => ['símbolo', 'licencia', 'Creative'],
            'pregunta_22' => ['utilizar', 'recurso', 'diagrama'],
            'pregunta_23' => ['Licencia', 'Creative', 'Commons'],
            'pregunta_24' => ['crear', 'lista', 'instrucciones'],
            'pregunta_25' => ['lenguaje', 'programación', 'desarrollar'],
            'pregunta_26' => ['interfaz', 'programación', 'Scratch'],
            'pregunta_27' => ['depurar', 'programa', 'soluciono'],
            'pregunta_28' => ['detectar', 'errores', 'secuencia'],
            'pregunta_29' => ['lenguaje', 'programación', 'crear'],
            'pregunta_30' => ['lenguajes', 'programación', 'elaborar']
        ]);
    
    }
    public function collection(Collection $collection)
    {

        // Aqui procesamos solo las preguntas, para convertirlas en formato campo
        $new_preguntas = $this->procesadorPreguntas($this->array_keywords_preguntas, $collection->first());
        
        $nu_collection = $collection->put(0, $new_preguntas);
        
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

        //$this->check_duplicates(collect($contados)->slice(0,1));
/*         foreach ($contados as $key => $value) {
            # code...
        } */

        $this->json_data = collect($contados)->slice(6)->toJson();

        $row = [
            $this->nombre_empresa,
            $this->descripcion,
            $this->poblacion,
            $this->muestra,
            $this->json_data
        ];


        Empresa::create([
            'nombre_empresa' => $row[0],
            'descripcion' => $row[1],
            'poblacion' => $row[2],
            'muestra' => $row[3],
            'json_data' => $row[4]
        ]);


        
        dd(response()->json(['message' => 'Datos guardados con éxito'], 201));
        //dd(collect($contados)->toJson());


    }


    protected function check_duplicates ($array) {

        foreach ($array as $value) {
            
            if (collect($value)->duplicates()->isNotEmpty()) {
                dd('Hay duplicados en el archivo');
            }

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
}
