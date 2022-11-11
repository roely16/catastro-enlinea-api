<?php 

namespace App\Http\Controllers\ProductosCatastrales;
    
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

require base_path() . '/vendor/PHPMailer_old/PHPMailerAutoload.php';

use App\Jobs\MailJob;
use Illuminate\Support\Facades\Mail;

class ProCasController extends Controller{

    public function login(Request $request){

        try {
            
            $result = app('db')->select("   SELECT  COUNT(*) AS EXISTE
                                            FROM    CATASTRO.SERV_USUARIO
                                            WHERE   UPPER(EMAIL) = UPPER('$request->email')
                                            AND     ESTATUS = 'A'");

            if ($result) {
                
                if ($result[0]->existe < 1) {
                    
                    $response = [
                        "status" => 0,
                        "message" => "No existe"
                    ];

                    return response()->json($response);

                } 

                $response = [
                    "status" => 1,
                    "message" => "Si existe"
                ];

                return response()->json($response);

            }

            $response = [
                "status" => 0,
                "message" => "No existe"
            ];

            return response()->json($response);

        } catch (\Throwable $th) {
            
            return response()->json($th->getMessage());

        }
        
        return response()->json($result);

    }

    public function obtener_matriculas(Request $request){

        $matriculas = app('db')->select("   SELECT  MU.ID,
                                                    MU.MATRICULA
                                            FROM    CATASTRO.SERV_MATRICULA_USUARIO MU,
                                                    CATASTRO.SERV_USUARIO US
                                            WHERE   MU.USUARIO_ID = US.ID
                                                    AND UPPER(US.EMAIL) = UPPER('$request->email')");

        if (count($matriculas) > 0){
            
            $response = [
                "status" => 1,
                "matriculas" => $matriculas,
                "cantidad" => count($matriculas)
            ];

            return response()->json($response);

        } else {

            $response = [
                "status" => 0,
                "matriculas" => [],
                "cantidad" => count($matriculas)
            ];

            return response()->json($response);

        }                                                   

    }

    public function validar_requisitos(Request $request){

        $catastral = 'X';
        $nomenclatura = 'X';
        $saldo = 0.00;
        $cumple = 'X';

        $result = app('db')->select("   SELECT  CASE 
                                                    WHEN (NUMERO_MANZANA IS NULL) THEN 'N' ELSE 'S' 
                                                END AS CATASTRADO,
                                                CASE
                                                    WHEN (DIRECCION_OFICIAL_PREDIO IS NULL) THEN 'N' ELSE 'S'
                                                END AS NOMENCLATURA
                                        FROM    MCA_INMUEBLES_ACTIVOS_VW
                                        WHERE   MATRICULA = '$request->matricula'");

        if ($result) {

            $catastral = $result[0]->catastrado;
            $nomenclatura = $result[0]->nomenclatura;

        }

        $data = [
            'NAME_FUNCTION'=>'ZIUSI_SALDOTRIMESTRE_RFC',
            'PARAM' => [
                ["IMPORT","IDENTIFICAR",$request->matricula],
                ["IMPORT","DATE","20171231"],
                ["EXPORT","IC"],
                ["EXPORT","SALDO"],
                ["EXPORT","ZONA"],
                ["EXPORT","SUJETO"],
                ["EXPORT","IC_ALTERNATIVO"],
                ["EXPORT","TRIMESTRE"],
                ["EXPORT","DIRECCION"],
                ["EXPORT","NIT"],
                ["EXPORT","IC_NAME"],
                ["EXPORT","IC_DIRECCION"],
                ["EXPORT","NO_REGISTRO"]
            ]
        ];
    
        $url = 'http://172.23.25.36/funciones-rfc/RFC_GLOBAL.php';
    
        $post = http_build_query([
            'data' => $data,
        ]);

        $options = [
            'http' => [
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $post
                        ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $saldo = json_decode($result,true)['SALDO'];

        if ($request->opcion == 1){
            if ($saldo < 0.01 && $catastral == 'S' && $nomenclatura == 'N'){
                $cumple = 'S';
            } else {
                $cumple = 'N';
            }
        }

        if ($request->opcion == 2){
            if ($saldo < 0.01 && $catastral == 'S' && $nomenclatura == 'S'){
                $cumple = 'S';
            } else {
                $cumple = 'N';
            }
        }

        $result = [
            "saldo" => $saldo,
            "catastral" => $catastral,
            "nomenclatura" => $nomenclatura,
            "cumple_requisitos" => $cumple
        ];

        return $result;

    }

    public function notificar_finalizado(Request $request){

        if ($request->opcion == 1){
            $producto = 'NOMENCLATURA';
        }

        if ($request->opcion == 2){
            $producto = 'CERTIFICACION';
        }

        $result = app('db')->select("   INSERT INTO CATASTRO.SERV_RECIBOS ( 
                                                ID,
                                                MATRICULA,
                                                ID_SERVICIO,
                                                NO_RECIBO,
                                                USUARIO,
                                                FECHA)
                                        VALUES (
                                                SQ_SERV_RECIBOS.NEXTVAL,
                                                '$request->matricula',
                                                $request->opcion,
                                                '$request->recibo',
                                                '$request->usuario',
                                                SYSDATE
                                                )");
        if ($result) {

            $datos_correo = [
                [
                    "solicitud" => $request->matricula,
                    "email" => 'gmartinez@muniguate.com',
                    "body" => '<p>Se ha generado una nueva solicitud de: ' . $producto . ' para la matricula: ' . $request->matricula . '</p><p>Por favor ingrese a la siguiente dirección para verificar la información</p><p> <a href="https://udicat.muniguate.com/apps/catastro-enlinea-app/#/admin">Ingresar</a> </p>',
                    "subject" => 'Solicitud de ' . $producto . ' '
                ]
            ];

            \Queue::push(new MailJob($datos_correo));

            $response = [
                "status" => 1,
                "message" => "Grabado"
            ];

            return response()->json($response);

        }

        $response = [
            "status" => 0,
            "message" => "Error"
        ];

        return response()->json($response);

    }

}

?>