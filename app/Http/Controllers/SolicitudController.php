<?php 

    namespace App\Http\Controllers;
        
    use Illuminate\Http\Request;
    use App\TipoUsuario;
    use App\Usuario;
    use App\SolicitudUsuario;
    use App\MatriculaSolicitud;
    use App\MatriculaUsuario;
    use App\HistorialSolicitud;
    use App\ArchivoSolicitud;

    use App\Jobs\MailJob;

    class SolicitudController extends Controller{

        public function obtener_solicitudes(Request $request){

            $estado = $request->estado;

            if(!$estado){
                
                $estado = 'P';

            }

            $nit = $request->nit;

            $empleado = app('db')->select(" SELECT 
                                                USUARIO,
                                                JEFE
                                            FROM RH_EMPLEADOS
                                            WHERE NIT = '$nit'");

            if($empleado){

                $empleado = $empleado[0];

            }else{

                $empleado = null;

            }

            if ($empleado->jefe == '1') {
                
                $result = app('db')->select("   SELECT 
                                                    COUNT(*) AS TOTAL
                                                FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
                                                INNER JOIN CATASTRO.SERV_USUARIO T2
                                                ON T1.USUARIO_ID = T2.ID
                                                WHERE T1.ESTATUS = 'A'");

                $aceptadas = $result ? $result[0]->total : 0;

                $result = app('db')->select("   SELECT 
                                                    COUNT(*) AS TOTAL
                                                FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
                                                INNER JOIN CATASTRO.SERV_USUARIO T2
                                                ON T1.USUARIO_ID = T2.ID
                                                WHERE T1.ESTATUS = 'R'");

                $rechazadas = $result ? $result[0]->total : 0;

                $result = app('db')->select("   SELECT 
                                                    COUNT(*) AS TOTAL
                                                FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
                                                INNER JOIN CATASTRO.SERV_USUARIO T2
                                                ON T1.USUARIO_ID = T2.ID
                                                WHERE T1.ESTATUS = 'T'");

                $enproceso = $result ? $result[0]->total : 0;

                $solicitudes = app('db')->select("  SELECT 
                                                        T1.ID,
                                                        TO_CHAR(T1.CREATED_AT, 'DD/MM/YYYY HH24:MI:SS') AS CREATED_AT,
                                                        CONCAT(T2.NOMBRES, CONCAT(' ', T2.APELLIDOS)) AS SOLICITANTE,
                                                        T2.EMAIL,
                                                        T2.TELEFONO,
                                                        T2.DIRECCION,
                                                        T1.ESTATUS AS ESTADO, 
                                                        T1.USUARIO_OPERA AS USUARIO
                                                    FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
                                                    INNER JOIN CATASTRO.SERV_USUARIO T2
                                                    ON T1.USUARIO_ID = T2.ID
                                                    WHERE T1.ESTATUS = '$estado'
                                                    ORDER BY T1.ID DESC");

            }else{

                $result = app('db')->select("   SELECT 
                                                    COUNT(*) AS TOTAL
                                                FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
                                                INNER JOIN CATASTRO.SERV_USUARIO T2
                                                ON T1.USUARIO_ID = T2.ID
                                                WHERE T1.ESTATUS = 'A'
                                                AND T1.USUARIO_OPERA = '$empleado->usuario'");

                $aceptadas = $result ? $result[0]->total : 0;

                $result = app('db')->select("   SELECT 
                                                    COUNT(*) AS TOTAL
                                                FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
                                                INNER JOIN CATASTRO.SERV_USUARIO T2
                                                ON T1.USUARIO_ID = T2.ID
                                                WHERE T1.ESTATUS = 'R'
                                                AND T1.USUARIO_OPERA = '$empleado->usuario'");

                $rechazadas = $result ? $result[0]->total : 0;

                $result = app('db')->select("   SELECT 
                                                    COUNT(*) AS TOTAL
                                                FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
                                                INNER JOIN CATASTRO.SERV_USUARIO T2
                                                ON T1.USUARIO_ID = T2.ID
                                                WHERE T1.ESTATUS = 'T'
                                                AND T1.USUARIO_OPERA = '$empleado->usuario'");

                $enproceso = $result ? $result[0]->total : 0;

                $solicitudes = app('db')->select("  SELECT 
                                                        T1.ID,
                                                        TO_CHAR(T1.CREATED_AT, 'DD/MM/YYYY HH24:MI:SS') AS CREATED_AT,
                                                        CONCAT(T2.NOMBRES, CONCAT(' ', T2.APELLIDOS)) AS SOLICITANTE,
                                                        T2.EMAIL,
                                                        T2.TELEFONO,
                                                        T2.DIRECCION,
                                                        T1.ESTATUS AS ESTADO, 
                                                        T1.USUARIO_OPERA AS USUARIO
                                                    FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
                                                    INNER JOIN CATASTRO.SERV_USUARIO T2
                                                    ON T1.USUARIO_ID = T2.ID
                                                    WHERE T1.ESTATUS = '$estado'
                                                    AND T1.USUARIO_OPERA = '$empleado->usuario'
                                                    ORDER BY T1.ID DESC");

            }

            

            // $result = app('db')->select("   SELECT 
            //                                     COUNT(*) AS TOTAL
            //                                 FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
            //                                 INNER JOIN CATASTRO.SERV_USUARIO T2
            //                                 ON T1.USUARIO_ID = T2.ID
            //                                 WHERE T1.ESTATUS = 'A'");

            // $aceptadas = $result ? $result[0]->total : 0;

            // $result = app('db')->select("   SELECT 
            //                                     COUNT(*) AS TOTAL
            //                                 FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
            //                                 INNER JOIN CATASTRO.SERV_USUARIO T2
            //                                 ON T1.USUARIO_ID = T2.ID
            //                                 WHERE T1.ESTATUS = 'R'");

            // $rechazadas = $result ? $result[0]->total : 0;

            // $solicitudes = app('db')->select("  SELECT 
            //                                         T1.ID,
            //                                         TO_CHAR(T1.CREATED_AT, 'DD/MM/YYYY HH24:MI:SS') AS CREATED_AT,
            //                                         CONCAT(T2.NOMBRES, CONCAT(' ', T2.APELLIDOS)) AS SOLICITANTE,
            //                                         T2.EMAIL,
            //                                         T2.TELEFONO,
            //                                         T2.DIRECCION,
            //                                         T1.ESTATUS AS ESTADO, 
            //                                         T1.USUARIO_OPERA AS USUARIO
            //                                     FROM CATASTRO.SERV_SOLICITUD_USUARIO T1
            //                                     INNER JOIN CATASTRO.SERV_USUARIO T2
            //                                     ON T1.USUARIO_ID = T2.ID
            //                                     WHERE T1.ESTATUS = '$estado'
            //                                     ORDER BY T1.ID DESC");

            foreach ($solicitudes as &$solicitud) {
                
                if ($solicitud->estado == 'P') {
                    
                    $solicitud->estado = 'PENDIENTE';
                    $solicitud->color = "secondary";

                }elseif($solicitud->estado == 'A'){

                    $solicitud->estado = 'ACEPTADA';
                    $solicitud->color = "success";

                }elseif($solicitud->estado == 'T'){

                    $solicitud->estado = 'EN PROCESO';
                    $solicitud->color = "primary";

                }else{

                    $solicitud->estado = 'RECHAZADA';
                    $solicitud->color = "error";

                }

            }

            $headers = [
                [
                    "text" => "No. ",
                    "value" => "id",
                    "width" => "7%",
                    "sortable" => false
                ],
                [
                    "text" => "Fecha",
                    "value" => "created_at",
                    "width" => "20%",
                    "sortable" => false
                ],
                [
                    "text" => "Solicitante",
                    "value" => "solicitante",
                    "width" => "20%",
                    "sortable" => false
                ],
                [
                    "text" => "Email",
                    "value" => "email",
                    "width" => "15%",
                    "sortable" => false
                ],
                [
                    "text" => "Usuario",
                    "value" => "usuario",
                    "width" => "10%"
                ],
                [
                    "text" => "Estado",
                    "value" => "estado",
                    "width" => "10%",
                    "sortable" => false
                ],
                [
                    "text" => "Acción",
                    "value" => "action",
                    "width" => "10%",
                    "align" => "right",
                    "sortable" => false
                ],

            ];

            $data = [
                "items" => $solicitudes,
                "headers" => $headers,
                "aceptadas" => $aceptadas,
                "rechazadas" => $rechazadas,
                "enproceso" => $enproceso
            ];

            return response()->json($data);

        }

        public function detalle_solicitud(Request $request){

            $solicitud = SolicitudUsuario::find($request->id);

            $usuario = Usuario::find($solicitud->usuario_id);

            /*Adjuntos de la solicitud */
            $adjuntos = ArchivoSolicitud::where('solicitud_id', $solicitud->id)->get();

            $usuario->adjuntos = count($adjuntos);

            $tipo_usuario = TipoUsuario::find($usuario->tipo_usuario_id);

            $usuario->tipo = $tipo_usuario->nombre;

            $matriculas = MatriculaSolicitud::where('solicitud_id', $solicitud->id)->get();

            $historial = app('db')->select("    SELECT 
                                                    ID,
                                                    COMENTARIO,
                                                    USUARIO_OPERA,
                                                    SOLICITUD_ID,
                                                    TO_CHAR(CREATED_AT, 'DD/MM/YYYY HH24:MI:SS') AS CREATED_AT
                                                FROM CATASTRO.SERV_HISTORIAL_SOLICITUD
                                                WHERE SOLICITUD_ID = '$solicitud->id'
                                                ORDER BY ID DESC");

            // Campos especiales 

            $campos_especiales = app('db')->select("   SELECT CAMPO, ETIQUETA
                                            FROM CATASTRO.SERV_TIPO_USER_CAMPO_ESP
                                            WHERE TIPO_USUARIO_ID = $usuario->tipo_usuario_id");


            $data = [
                "solicitud" => $solicitud,
                "usuario" => $usuario,
                "matriculas" => $matriculas,
                "historial" => $historial,
                "campos_especiales" => $campos_especiales
            ];

            return response()->json($data);

        }

        public function cambiar_estado_matricula(Request $request){

            $nit = $request->nit;

            $empleado = app('db')->select(" SELECT 
                                                USUARIO
                                            FROM RH_EMPLEADOS
                                            WHERE NIT = '$nit'");

            if ($empleado) {
                
                $empleado = $empleado[0];

            }else{

                $empleado->nit = null;

            }

            $matricula = MatriculaSolicitud::find($request->id);

            $matricula->estado = $request->estado;
            $matricula->save();

            // Registrar en el historial 
            $comentario = $request->estado == 'R' ? 'La Matricula ' . $matricula->matricula . ' a sido RECHAZADA' : 'La Matricula ' . $matricula->matricula . ' a sido ACEPTADA';

            $historial = new HistorialSolicitud();
            $historial->comentario = $comentario;
            $historial->solicitud_id = $matricula->solicitud_id;
            $historial->usuario_opera = $empleado->usuario;
            $historial->save();

            return response()->json($request);

        }

        public function cambiar_estado_solicitud(Request $request){

            $nit = $request->nit;

            $empleado = app('db')->select(" SELECT 
                                                USUARIO
                                            FROM RH_EMPLEADOS
                                            WHERE NIT = '$nit'");

            if ($empleado) {
                
                $empleado = $empleado[0];

            }else{

                $empleado->nit = null;

            }

            $solicitud = SolicitudUsuario::find($request->id);
            $solicitud->estatus = $request->estado;
            $solicitud->motivo_rechazo_id = $request->estado == 'R' ? $request->motivo_rechazo : null;
            $solicitud->save();

            $usuario = Usuario::find($solicitud->usuario_id);
            $usuario->estatus = $request->estado;
            $usuario->save();

            // Si se acepta, copiar las matriculas
            if ($request->estado == 'A') {
                
                $matriculas = MatriculaSolicitud::where('solicitud_id', $solicitud->id)->where('estado', 'A')->get();

                foreach ($matriculas as $matricula) {
                    
                    $matricula_usuario = new MatriculaUsuario();
                    $matricula_usuario->matricula = $matricula->matricula;
                    $matricula_usuario->usuario_id = $usuario->id;
                    $matricula_usuario->solicitud_id = $solicitud->id;
                    $matricula_usuario->save();

                }

            }

            /* Dependiendo del estado enviar correo electrónico */

            if ($request->estado == 'A') {
                
                $comentario = 'Solicitud ACEPTADA';
                $subject = 'Solicitud No. ' . $solicitud->id . ' ACEPTADA';
                $body = "<p>Estimado(a): " . $usuario->nombres . " " . $usuario->apellidos . "</p>" .
                        '<p>Su gestión para la habilitación de usuario para acceder a los servicios catastrales en línea a sido APROBADA.</p>' . 
                        '<p>Para acceder a los servicios en línea debera de dirigirse a la página <a href="www.muniguate.com">www.muniguate.com</a> y acceder con las credenciales previamente registradas.</p>' .
                        '<p>Atentamente, </p>' .
                        '<p><strong>Dirección de Catastro y Administración del IUSI</strong></p>';

            }else{

                $comentario = 'Solicitud RECHAZADA';
                $subject = 'Solicitud No. ' . $solicitud->id . ' RECHAZADA';
                $body = "<p>Estimado(a): " . $usuario->nombres . " " . $usuario->apellidos . "</p>" .
                        '<p>Su gestión para la habilitación de usuario para acceder a los servicios catastrales en línea a sido RECHAZADA.</p>' . 
                        '<p>Atentamente, </p>' .
                        '<p><strong>Dirección de Catastro y Administración del IUSI</strong></p>';

            }
            
            $datos_correo = [
                // Correo para el solicitante
                [
                    "email" => $usuario->email,
                    "body" =>   $body,
                    "subject" => $subject 
                ],
            ];

            \Queue::push(new MailJob($datos_correo));

            /* Registrar en el historial */
            $historial = new HistorialSolicitud();
            $historial->solicitud_id = $solicitud->id;
            $historial->comentario = $comentario;
            $historial->usuario_opera = $empleado->usuario;
            $historial->save();

            $data = [
                "status" => 200,
                "message" => "El estado de la solicitud ha sido actualizado." ,
                "title" => "Excelente!",
                "icon" => "success"
            ];

            return response()->json($data);

        }

        public function actualizar_detalle_usuario(Request $request){
            
            $nit = $request->nit;

            $empleado = app('db')->select(" SELECT 
                                                USUARIO
                                            FROM RH_EMPLEADOS
                                            WHERE NIT = '$nit'");

            if ($empleado) {
                
                $empleado = $empleado[0];

            }else{

                $empleado->nit = null;

            }

            $data = (object) $request->data;

            $usuario = (object) $data->usuario;
            $solicitud = (object) $data->solicitud;

            $user = Usuario::find($usuario->id);
            $user->nombres = $usuario->nombres;
            $user->apellidos = $usuario->apellidos;
            $user->sexo = $usuario->sexo;
            $user->telefono = $usuario->telefono;
            $user->direccion = $usuario->direccion;
            $user->email = $usuario->email;
            $user->dpi = $usuario->dpi;
            $user->representacion_legal = $usuario->representacion_legal;
            $user->carne_abogado = $usuario->carne_abogado;
            $user->carne_valuador = $usuario->carne_valuador;

            $result = $user->save();

            // Registrar en el historial 
            $comentario = "Se han actualizado los datos del usuario.";

            $historial = new HistorialSolicitud();
            $historial->comentario = $comentario;
            $historial->solicitud_id = $solicitud->id;
            $historial->usuario_opera = $empleado->usuario;
            $historial->save();

            return response()->json($result);

        }

        public function obtener_adjuntos(Request $request){

            $archivos = ArchivoSolicitud::where('solicitud_id', $request->solicitud_id)->orderBy('id', 'desc')->get();

            return response()->json($archivos);

        }

        public function enviar_email(Request $request){

            $nit = $request->nit;

            $empleado = app('db')->select(" SELECT 
                                                USUARIO
                                            FROM RH_EMPLEADOS
                                            WHERE NIT = '$nit'");

            if ($empleado) {
                
                $empleado = $empleado[0];

            }else{

                $empleado->nit = null;

            }

            $solicitud = SolicitudUsuario::find($request->solicitud_id);

            $usuario = Usuario::find($solicitud->usuario_id);

            $datos_correo = [
                // Correo para el solicitante
                [
                    "email" => $usuario->email,
                    "body" =>   $request->mensaje,
                    "subject" => $request->asunto
                ],
            ];

            try {

                \Queue::push(new MailJob($datos_correo));
                
            } catch (\Throwable $th) {

                return response()->json("Error...");
                
            }


            /* Registrar en la bitácora */
            $historial = new HistorialSolicitud();
            $historial->comentario = 'Se envia correo electrónico a la dirección '. $usuario->email;
            $historial->solicitud_id = $request->solicitud_id;
            $historial->usuario_opera = $empleado->usuario;
            $historial->contenido = $request->mensaje;
            $historial->save();

            $response = [
                "status" => 200,
                "title" => "Excelente!",
                "message" => "El correo electrónico a sido enviado exitosamente.",
                "icon" => "success"
            ];

            return response()->json($response);

        }

        public function obtener_tecnicos(Request $request){

            $tecnicos = app('db')->select(" SELECT 
                                                NIT, 
                                                USUARIO,
                                                CONCAT(NOMBRE, CONCAT(' ', APELLIDO)) AS NOMBRE
                                            FROM RH_EMPLEADOS
                                            WHERE DEPENDE = '$request->nit'
                                            AND STATUS = 'A'");

            return response()->json($tecnicos);

        }

        public function asignar_tecnico(Request $request){

            $solicitud = SolicitudUsuario::find($request->solicitud_id);

            $solicitud->usuario_opera = $request->tecnico;
            $solicitud->estatus = 'T';

            $solicitud->save();

            $response = [
                "status" => 200,
                "title" => "Excelente!",
                "message" => "La solicitud a sido asignada exitosamente.",
                "icon" => "success"
            ];

            return response()->json($response);

        }

        public function registrar_historial(Request $request){

            $solicitud = SolicitudUsuario::find($request->solicitud_id);

            $nit = $request->nit;

            $empleado = app('db')->select(" SELECT 
                                                USUARIO
                                            FROM RH_EMPLEADOS
                                            WHERE NIT = '$nit'");

            if ($empleado) {
                
                $empleado = $empleado[0];

            }else{

                $empleado->nit = null;

            }

            $historial = new HistorialSolicitud();
            $historial->comentario = $request->comentario;
            $historial->solicitud_id = $request->solicitud_id;
            $historial->usuario_opera = $empleado->usuario;
            $historial->save();

            return response()->json($request);

        }

        public function motivos_rechazo(){

            $motivos = app('db')->select("  SELECT *
                                            FROM CATASTRO.SERV_MOTIVO_RECHAZO");

            return response()->json($motivos);

        }

        public function adjuntar_archivos(Request $request){

            return response()->json($request);

        }

    }

?>