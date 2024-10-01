<?php
ob_start();

date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
$imagen_error = "this.src='dist/svg/404-v2.svg'";
$toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

require_once "../modelos/Usuario_cliente.php";
$usuarioC = new Usuario_cliente();

switch ($_GET["op"]) {
  // ══════════════════════════════════════ VALIDAR SESION CLIENTE ══════════════════════════════════════
  case 'verificarC':
    $loginc   = $_POST['loginc'];
    $clavec   = $_POST['clavec'];
    $st       = $_POST['st'];

    $clavehash = hash("SHA256", $clavec);

    $rspta  = $usuarioC->verificar($loginc, $clavehash); 

    if (!empty($rspta['data']['usuario_cliente'])) {

      //Declaramos las variables de sesión
      $_SESSION['idpersona_cliente']    = $rspta['data']['usuario_cliente']['idpersona_cliente'];
      $_SESSION['idpersona']            = $rspta['data']['usuario_cliente']['idpersona'];
      $_SESSION['cliente_nombre']       = $rspta['data']['usuario_cliente']['nombre_razonsocial'];
      $_SESSION['cliente_apellido']     = $rspta['data']['usuario_cliente']['apellidos_nombrecomercial'];
      $_SESSION['cliente_tipo_doc']     = $rspta['data']['usuario_cliente']['tipo_documento'];
      $_SESSION['cliente_num_doc']      = $rspta['data']['usuario_cliente']['numero_documento'];
      $_SESSION['cliente_imagen']       = $rspta['data']['usuario_cliente']['foto_perfil'];
      $_SESSION['cliente_login']        = $rspta['data']['usuario_cliente']['landing_user'];

      $data = [ 'status'=>true, 'message'=>'todo okey','data'=> $rspta['data']  ];
      echo json_encode($data, true);
    }else{
      $data = [ 'status'=>true, 'message'=>'todo okey','data'=>[]   ];
      echo json_encode($data, true);
    }
  break;
}

ob_end_flush();