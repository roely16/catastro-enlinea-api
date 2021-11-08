<?php 

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;
    
    use App\Servicio;
    use App\HistorialDocumento;

    class ServicioController extends Controller{

        public function servicios_validacion(){

            $servicios = Servicio::where('validacion', 'S')->get();

            return response()->json($servicios);

        }        

        public function validar_documento(Request $request){

            $documento_split = explode("-", $request->numero);

            if(count($documento_split) <= 1){
                
                $response = [
                    "status" => 100,
                    "type" => "error",
                    "message" => "El número de documento ingresado no ha sido encontrado.  Por favor verifique la información ingresada."
                ];

                return response()->json($response);;

            }

            $numero = $documento_split[0];
            $year = $documento_split[1];

            $historial_documento = HistorialDocumento::where('numero', $numero)->where('year', $year)->where('servicio_id', $request->tipo)->first();

            if(!$historial_documento){

                $response = [
                    "status" => 100,
                    "type" => "error",
                    "message" => "El número de documento ingresado no ha sido encontrado.  Por favor verifique la información ingresada."
                ];

                return response()->json($response);

            }

            $response = [
                "status" => 200,
                "type" => "success",
                "message" => "El número de documento ingresado es válido y a continuación se muestra la información que deberá de estar reflejada en el mismo.",
                "file_path" => $_SERVER['SERVER_NAME'] . "/apis/catastro-enlinea-api/public/" . $historial_documento->path
            ];

            return response()->json($response);

        }

        public function registrar_documento(Request $request){

            /*
                Obtener el número correlativo del documento en base al tipo
            */

            $year = date('Y');

            $historial = HistorialDocumento::where('servicio_id', $request->servicio_id)->orderBy('id', 'desc')->first();

            $numero = !$historial ? 1 : $historial->numero + 1;

            date_default_timezone_set('America/Guatemala');

            $created_at = date('Y-m-d H:i:s');
            $finish_at = date('Y-m-d', strtotime('+2 months'));

            $result = app('db')->table('SERV_HISTORIAL_DOCUMENTO')->insert([
                "numero" => $numero,
                "year" => $year,
                "usuario" => $request->usuario,
                "path" => $request->path,
                "servicio_id" => $request->servicio_id,
                "created_at" => $created_at,
                "finish_at" => $finish_at
            ]);

            if (!$result) {
                
            }

            $response = [
                "status" => 200,
                "numero" => $numero . '-' . $year
            ];

            return response()->json($response);

        }

    }

?>