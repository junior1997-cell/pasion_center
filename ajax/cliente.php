<?php
ob_start();
if (strlen(session_id()) < 1) {
  session_start();
} //Validamos si existe o no la sesión

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status' => 'login', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['cliente'] == 1) {

    require_once "../modelos/Cliente.php";

    $_cliente = new Cliente();    


    date_default_timezone_set('America/Lima');
    $date_now = date("d_m_Y__h_i_s_A");
    $toltip = '<script> $(function() { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idpersona                  = isset($_POST["idpersona"]) ? limpiarCadena($_POST["idpersona"]) : "";
    $idtipo_persona             = isset($_POST["idtipo_persona"]) ? limpiarCadena($_POST["idtipo_persona"]) : "";
    $idbancos                   = isset($_POST["idbancos"]) ? limpiarCadena($_POST["idbancos"]) : "";
    $idcargo_trabajador         = isset($_POST["idcargo_trabajador"]) ? limpiarCadena($_POST["idcargo_trabajador"]) : "";
    $idpersona_cliente          = isset($_POST["idpersona_cliente"]) ? limpiarCadena($_POST["idpersona_cliente"]) : "";
    $tipo_persona_sunat         = isset($_POST["tipo_persona_sunat"]) ? limpiarCadena($_POST["tipo_persona_sunat"]) : "";
    $tipo_documento             = isset($_POST["tipo_documento"]) ? limpiarCadena($_POST["tipo_documento"]) : "";
    $numero_documento           = isset($_POST["numero_documento"]) ? limpiarCadena($_POST["numero_documento"]) : "";
    $nombre_razonsocial         = isset($_POST["nombre_razonsocial"]) ? limpiarCadena($_POST["nombre_razonsocial"]) : "";
    $apellidos_nombrecomercial  = isset($_POST["apellidos_nombrecomercial"]) ? limpiarCadena($_POST["apellidos_nombrecomercial"]) : "";
    $fecha_nacimiento           = isset($_POST["fecha_nacimiento"]) ? limpiarCadena($_POST["fecha_nacimiento"]) : "";

    $celular                    = isset($_POST["celular"]) ? limpiarCadena($_POST["celular"]) : "";
    $direccion                  = isset($_POST["direccion"]) ? limpiarCadena($_POST["direccion"]) : "";
    $distrito                   = isset($_POST["distrito"]) ? limpiarCadena($_POST["distrito"]) : "";
    $departamento               = isset($_POST["departamento"]) ? limpiarCadena($_POST["departamento"]) : "";
    $provincia                  = isset($_POST["provincia"]) ? limpiarCadena($_POST["provincia"]) : "";
    $ubigeo                     = isset($_POST["ubigeo"]) ? limpiarCadena($_POST["ubigeo"]) : "";
    $correo                     = isset($_POST["correo"]) ? limpiarCadena($_POST["correo"]) : "";  
    $idselec_centroProbl        = isset($_POST["idselec_centroProbl"]) ? limpiarCadena($_POST["idselec_centroProbl"]) : "";   
    $fecha_afiliacion           = isset($_POST["fecha_afiliacion"]) ? limpiarCadena($_POST["fecha_afiliacion"]) : "";
    $nota                       = isset($_POST["nota"]) ? limpiarCadena($_POST["nota"]) : "";
    
    switch ($_GET["op"]) {

      case 'guardar_y_editar_cliente':

        //guardar f_img_fondo fondo
        if (!file_exists($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
          $img_perfil = $_POST["imagenactual"];
          $flat_img1 = false;
        } else {
          $ext1 = explode(".", $_FILES["imagen"]["name"]);
          $flat_img1 = true;
          $img_perfil = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext1);
          move_uploaded_file($_FILES["imagen"]["tmp_name"], "../assets/modulo/persona/perfil/" . $img_perfil);
        }


        if (empty($idpersona_cliente)) {
          $rspta = $_cliente->insertar_cliente(
            $idtipo_persona,
            $idbancos,
            $idcargo_trabajador,
            $idpersona_cliente,
            $tipo_persona_sunat,
            $tipo_documento,
            $numero_documento,
            $nombre_razonsocial,
            $apellidos_nombrecomercial,
            $fecha_nacimiento,
            $celular,
            $direccion,
            $distrito,
            $departamento,
            $provincia,
            $ubigeo,
            $correo,  
            $idselec_centroProbl,   
            $fecha_afiliacion,
            $nota,          
            $img_perfil
          );
          echo json_encode($rspta, true);
        } else {

          if ($flat_img1 == true || empty($img_perfil)) {
            $datos_f1 = $_cliente->perfil_trabajador($idpersona);
            $img1_ant = $datos_f1['data']['foto_perfil'];
            if (!empty($img1_ant)) { unlink("../assets/modulo/persona/perfil/" . $img1_ant); }
          }

          $rspta = $_cliente->editar_cliente(
            $idpersona,
            $idtipo_persona,
            $idbancos,
            $idcargo_trabajador,
            $idpersona_cliente,
            $tipo_persona_sunat,
            $tipo_documento,
            $numero_documento,
            $nombre_razonsocial,
            $apellidos_nombrecomercial,
            $fecha_nacimiento,
            $celular,
            $direccion,
            $distrito,
            $departamento,
            $provincia,
            $ubigeo,
            $correo,  
            $idselec_centroProbl,   
            $fecha_afiliacion,
            $nota,        
            $img_perfil
          );
          echo json_encode($rspta, true);
        }
      break;

      case 'desactivar_cliente':
        $rspta = $_cliente->desactivar_cliente($_GET["id_tabla"], $_GET["descripcion"]);
        echo json_encode($rspta, true);
      break;

      case 'activar_cliente':
        $rspta = $_cliente->activar_cliente($_GET["id_tabla"], $_GET["descripcion"]);
        echo json_encode($rspta, true);
      break;

      case 'eliminar_cliente':
        $rspta = $_cliente->eliminar_cliente($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_cliente':
        $rspta = $_cliente->mostrar_cliente($idpersona_cliente);
        //Codificar el resultado utilizando json
        echo json_encode($rspta, true);
      break;      

      case 'tabla_principal_cliente':
        $rspta = $_cliente->tabla_principal_cliente($_GET["filtro_mes_afiliacion"],$_GET["filtro_distrito"]);
        //Vamos a declarar un array
        $data = [];
        $cont = 1;         
        $class_dia = "";        

        if ($rspta['status'] == true) {
          
          foreach ($rspta['data'] as $key => $value) { 

            $imagen_perfil = empty($value['foto_perfil']) ? 'no-perfil.jpg' :   $value['foto_perfil'];

            $data[] = array(
              "0" => $cont++,
              "1" => '<button class="btn btn-icon btn-sm border-warning btn-warning-light" onclick="mostrar_cliente(' . $value['idpersona_cliente'] . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>                
              <div class="btn-group ">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle py-1" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-settings-4-line"></i></button>
                <ul class="dropdown-menu">
                  
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="realizar_pago(' . $value['idpersona_cliente'] . ');" ><i class="ti ti-coin"></i> Realizar Pago</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_pagos_x_cliente(' . $value['idpersona_cliente'] . ');" ><i class="ti ti-checkup-list"></i> Listar pagos</a></li> '.
                  ( $value['estado_pc'] == '1' ? '<li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminar_cliente(' . $value['idpersona_cliente'] . ', \'' . encodeCadenaHtml($value['cliente_nombre_completo']) . '\')"><i class="ri-delete-bin-line"></i> Dar de baja o Eliminar</a></li>': 
                  '<li><a class="dropdown-item text-success" href="javascript:void(0);" onclick="activar(' . $value['idpersona_cliente'] . ', \'' . encodeCadenaHtml($value['cliente_nombre_completo']) . '\')"><i class="ri-check-line"></i> Reactivar</a></li>'
                  ).
                '</ul>
              </div>',
              "2" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                  <span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/persona/perfil/' . $imagen_perfil . '" alt="" onclick="ver_img(\'' . $imagen_perfil . '\', \'' . encodeCadenaHtml($value['cliente_nombre_completo']) . '\')"> </span>
                </div>
                <div>
                  <span class="d-block fw-semibold fs-12 text-primary">' . $value['cliente_nombre_completo'] . '</span>
                  <span class="text-muted fs-10 text-nowrap">' . $value['tipo_documento_abrev_nombre'] . ' : ' . $value['numero_documento'] . '</span> |
                  <span class="text-muted fs-10 text-nowrap">Cel.: ' . '<a href="tel:+51'.$value['celular'].'" data-bs-toggle="tooltip" title="Clic para hacer llamada">'.$value['celular'].'</a>' . '</span> |
                  <span class="text-muted fs-10 text-nowrap"><i class="ti ti-fingerprint fs-15"></i> ' . $value['idpersona_cliente_v2'] . '</span> 
                </div>
              </div>',
              "3" => '<textarea cols="30" rows="2" class="textarea_datatable bg-light fs-10" readonly="">' .  $value['direccion'] . '</textarea>',
              "4" => $value['centro_poblado'],
              "5" => $value['distrito'],
              "6" => $value['provincia'],
              "7" => $value['departamento'],
              "8" => '<textarea cols="30" rows="2" class="textarea_datatable bg-light " readonly="">' . $value['nota'] . '</textarea>',
              
              "9" => $value['cliente_nombre_completo'],
              "10" => $value['tipo_documento_abrev_nombre'],
              "11" => $value['numero_documento'],

            );
          }
          $results = [
            'status'=> true,
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }

      break;


      case 'mostrar_datos_cliente':
        $rspta = $_cliente->mostrar_cliente($_GET["idpersona_cliente"]);
        //Codificar el resultado utilizando json
        echo json_encode($rspta, true);
      break;

      // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 

      
      case 'select2_filtro_mes_afiliacion':

        $rspta = $_cliente->select2_filtro_mes_afiliacion();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {            
            $data .= '<option  value="' . $value['fecha_mes_anio']  . '"> ' . $value['mes_afiliacion'] . ' ('.$value['cant_cliente'].')'.'</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_filtro_anio_pago':

        $rspta = $_cliente->select2_filtro_anio_pago();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $cant_cliente   = $value['cant_cliente'];
            $data .= '<option  value="' . $value['anio_cancelacion']  . '">' . $value['anio_cancelacion'] . ' ('.$cant_cliente.')'. '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_filtro_distrito':

        $rspta = $_cliente->select2_filtro_distrito();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $cant_cliente   = $value['cant_cliente'];
            $data .= '<option  value="' . $value['distrito']  . '">' . $value['distrito'] .' ('.$cant_cliente.')'. '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_filtro_zona_antena':

        $rspta = $_cliente->select2_filtro_zona_antena();        
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $idzona_antena  = $value['idzona_antena'];
            $nombre         = $value['nombre'];
            $ip_antena      = $value['ip_antena'];
            $cant_cliente   = $value['cant_cliente'];
            $data .= '<option value="' . $idzona_antena . '" cant_cliente="' . $cant_cliente . '">' . $nombre . ' - IP: ' . $ip_antena .' ('.$cant_cliente.')'. '</option>';
          }

          $retorno = array( 'status' => true, 'message' => 'Salió todo ok', 'data' => $data,  );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_plan':

        $rspta = $_cliente->select2_plan();
        $cont = 1;
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idplan']  . '>' . $value['nombre'] . ' - Costo: ' . $value['costo'] . '</option>';
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'select2_zona_antena':

        $rspta = $_cliente->select2_zona_antena();
        $cont = 1;
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idzona_antena']  . '>' . $value['nombre'] . ' - IP: ' . $value['ip_antena'] . '</option>';
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_trabajador':

        $rspta = $_cliente->select2_trabajador();
        $cont = 1;
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idpersona_trabajador']  . '>' . $value['nombre_completo'] . ' ' . $value['numero_documento'] . '</option>';
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;

      case 'selec_centroProbl':

        $rspta = $_cliente->selec_centroProbl();
        $cont = 1;
        $data = "";
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value=' . $value['idcentro_poblado']  . '>' . $value['nombre'] . '</option>';
          }

          $retorno = array(
            'status' => true,
            'message' => 'Salió todo ok',
            'data' => $data,
          );
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }

      break;


      case 'salir':
        //Limpiamos las variables de sesión
        session_unset();
        //Destruìmos la sesión
        session_destroy();
        //Redireccionamos al login
        header("Location: ../index.php");
      break;

      default:
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => [], 'aaData' => []];
        echo json_encode($rspta, true);
      break;
    }
  } else {
    $retorno = ['status' => 'nopermiso', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
    echo json_encode($retorno);
  }
}

ob_end_flush();
