<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {

    return $router->app->version();
    
});

// Se obtienen los tipos de usuario que se pueden registrar
$router->get('/obtener_tipos_usuario', 'RegistroController@obtener_tipos_usuario');

// Se obtienen los pasos necesarios para completar el registro
// Dependiendo del tipo de usuario
$router->post('/pasos_registro', 'RegistroController@pasos_registro');

// Obtener los campos especiales para el formulario de registro
// Estos dependen del tipo de usuario
$router->post('/obtener_campos_especiales', 'RegistroController@obtener_campos_especiales');

// Se registra la solicitud de usuario
$router->post('/registrar_solicitud', 'RegistroController@registrar_solicitud');

// Obtener todas las solicitudes pendientes de atender 
$router->post('/obtener_solicitudes', 'SolicitudController@obtener_solicitudes');

// Obtener el detalle de la solicitud pendiente
$router->post('/detalle_solicitud', 'SolicitudController@detalle_solicitud');

// Cambiar el estado de una matricula, incluida en una solicitud de usuario
$router->post('/cambiar_estado_matricula', 'SolicitudController@cambiar_estado_matricula');

// Cambiar el estado de una solicitud de usuario
$router->post('/cambiar_estado_solicitud', 'SolicitudController@cambiar_estado_solicitud');

// Probar el envio de correo 
$router->get('/test_mail', 'MailController@test_mail');

// Actualizar el detalle del usuario
$router->post('/actualizar_detalle_usuario', 'SolicitudController@actualizar_detalle_usuario');

// Obtener la lista de documentos que se deberán de adjuntar
$router->post('/obtener_adjuntos_registro', 'RegistroController@obtener_adjuntos');

// Subir archivos en la solicitud de creación de usuario
$router->post('/upload_file_registro', 'RegistroController@upload_file');

// Obtener los documentos adjuntos de la solicitud
$router->post('/obtener_adjuntos', 'SolicitudController@obtener_adjuntos');

// Enviar email desde solicitud
$router->post('/enviar_email', 'SolicitudController@enviar_email');

// Iniciar sesión
$router->post('/login', 'LoginController@login');

// Obtener técnicos
$router->post('/obtener_tecnicos', 'SolicitudController@obtener_tecnicos');

// Asignar técnico
$router->post('/asignar_tecnico', 'SolicitudController@asignar_tecnico');

// Registrar comentario en la bitácora
$router->post('/registrar_comentario', 'SolicitudController@registrar_historial');

// Obtener los motivos de rechazo
$router->post('/motivos_rechazo', 'SolicitudController@motivos_rechazo');

// Login para página municipal
$router->post('/public_login', 'LoginController@public_login');

// Obtener las matrículas registradas de un contribuyente
$router->post('/matriculas_contribuyente', 'ContribuyenteController@obtener_matriculas');

// Adjuntar archivos a una solicitud
$router->post('/adjuntar_archivos_solicitud', 'SolicitudController@adjuntar_archivos');

// Obtener el interlocutor de una matricula
$router->post('/matricula_interlocutor', 'ContribuyenteController@matricula_interlocutor');