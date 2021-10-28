<?php 

    namespace App\Http\Controllers;
            
    use Illuminate\Http\Request;

    class ContribuyenteController extends Controller{

        public function obtener_matriculas(Request $request){

            $matriculas = app('db')->select("   SELECT 
                                                    ID,
                                                    MATRICULA
                                                FROM CATASTRO.SERV_MATRICULA_USUARIO
                                                WHERE USUARIO_ID = $request->id");

            return response()->json($matriculas);

        }

        public function matricula_interlocutor(Request $request){

            $interlocutor = app('db')->select(" SELECT *
                                                FROM CIERRES_IUSI_SAP.SALDOTRIMESTRE
                                                WHERE MATRICULA = '$request->matricula'");

            if(!$interlocutor){

                $response = [
                    "status" => 100
                ];

                return response()->json($response);

            }

            $response = [
                "status" => 200,
                "interlocutor" => $interlocutor[0]->interlocutor
            ];

            return response()->json($response);

        }

    }

?>