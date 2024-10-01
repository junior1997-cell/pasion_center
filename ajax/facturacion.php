<?php


ob_start();
if (strlen(session_id()) < 1) { session_start(); }

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status'=>'login', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['facturacion'] == 1) {

    require_once "../modelos/Facturacion.php";
    require_once "../modelos/Producto.php";
    require_once "../modelos/Avance_cobro.php";

    require '../vendor/autoload.php';                   // CONEXION A COMPOSER
    $see = require '../sunat/SunatCertificado.php';   // EMISION DE COMPROBANTES

    $facturacion        = new Facturacion();      
    $productos          = new Producto(); 
    $avance_cobro        = new Avance_cobro();  

    date_default_timezone_set('America/Lima');  $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../assets/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    // ══════════════════════════════════════  DATOS DE FACTURACION ══════════════════════════════════════

    $idventa                = isset($_POST["f_idventa"]) ? limpiarCadena($_POST["f_idventa"]) : "";   
    $impuesto               = isset($_POST["f_impuesto"]) ? limpiarCadena($_POST["f_impuesto"]) : "";   
    $crear_y_emitir         = isset($_POST["f_crear_y_emitir"]) ? ( empty($_POST["f_crear_y_emitir"]) ? 'NO' : 'SI' ) : ""; 

    $idsunat_c01            = isset($_POST["f_idsunat_c01"]) ? limpiarCadena($_POST["f_idsunat_c01"]) : "";    
    $tipo_comprobante       = isset($_POST["f_tipo_comprobante"]) ? limpiarCadena($_POST["f_tipo_comprobante"]) : "";    
    $serie_comprobante      = isset($_POST["f_serie_comprobante"]) ? limpiarCadena($_POST["f_serie_comprobante"]) : "";    
    $idpersona_cliente      = isset($_POST["f_idpersona_cliente"]) ? limpiarCadena($_POST["f_idpersona_cliente"]) : "";         
    $observacion_documento  = isset($_POST["f_observacion_documento"]) ? limpiarCadena($_POST["f_observacion_documento"]) : "";    
    // $es_cobro               = isset($_POST["f_es_cobro_inp"]) ? limpiarCadena($_POST["f_es_cobro_inp"]) : "";    
    // $periodo_pago           = isset($_POST["f_periodo_pago"]) ? limpiarCadena($_POST["f_periodo_pago"]) : "";    
    
    $metodo_pago            = isset($_POST["f_metodo_pago"]) ? limpiarCadena($_POST["f_metodo_pago"]) : "";  
    $total_recibido         = isset($_POST["f_total_recibido"]) ? limpiarCadena($_POST["f_total_recibido"]) : "";  
    $mp_monto               = isset($_POST["f_mp_monto"]) ? limpiarCadena($_POST["f_mp_monto"]) : "";  
    $total_vuelto           = isset($_POST["f_total_vuelto"]) ? limpiarCadena($_POST["f_total_vuelto"]) : "";  

    $usar_anticipo          = isset($_POST["f_usar_anticipo"]) ? limpiarCadena($_POST["f_usar_anticipo"]) : "";  
    $ua_monto_disponible    = isset($_POST["f_ua_monto_disponible"]) ? limpiarCadena($_POST["f_ua_monto_disponible"]) : "";  
    $ua_monto_usado         = isset($_POST["f_ua_monto_usado"]) ? limpiarCadena($_POST["f_ua_monto_usado"]) : "";  

    $mp_serie_comprobante   = isset($_POST["f_mp_serie_comprobante"]) ? limpiarCadena($_POST["f_mp_serie_comprobante"]) : "";       

    $venta_subtotal         = isset($_POST["f_venta_subtotal"]) ? limpiarCadena($_POST["f_venta_subtotal"]) : "";    
    $tipo_gravada           = isset($_POST["f_tipo_gravada"]) ? limpiarCadena($_POST["f_tipo_gravada"]) : "";
    $venta_descuento        = isset($_POST["f_venta_descuento"]) ? limpiarCadena($_POST["f_venta_descuento"]) : "";    
    $venta_igv              = isset($_POST["f_venta_igv"]) ? limpiarCadena($_POST["f_venta_igv"]) : "";            
    $venta_total            = isset($_POST["f_venta_total"]) ? limpiarCadena($_POST["f_venta_total"]) : "";   

    $nc_idventa             = isset($_POST["f_nc_idventa"]) ? limpiarCadena($_POST["f_nc_idventa"]) : "";    
    $nc_tipo_comprobante    = isset($_POST["f_nc_tipo_comprobante"]) ? limpiarCadena($_POST["f_nc_tipo_comprobante"]) : "";    
    $nc_serie_y_numero      = isset($_POST["f_nc_serie_y_numero"]) ? limpiarCadena($_POST["f_nc_serie_y_numero"]) : "";    
    $nc_motivo_anulacion    = isset($_POST["f_nc_motivo_anulacion"]) ? limpiarCadena($_POST["f_nc_motivo_anulacion"]) : "";    

    $tiempo_entrega         = isset($_POST["f_tiempo_entrega"]) ? limpiarCadena($_POST["f_tiempo_entrega"]) : "";    
    $validez_cotizacion     = isset($_POST["f_validez_cotizacion"]) ? limpiarCadena($_POST["f_validez_cotizacion"]) : "";    
     
    $mp_comprobante_old     = isset($_POST["f_mp_comprobante_old"]) ? limpiarCadena($_POST["f_mp_comprobante_old"]) : "";     

    switch ($_GET["op"]){

      // :::::::::::: S E C C I O N  FACTURACION ::::::::::::      

      case 'guardar_editar_facturacion':

        $rspta = ""; $mp_comprobante = ""; 
        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 

        if ( floatval($venta_total) > 699 ) {
          # code...
        } else {
          # code...
        }        

        if ($metodo_pago == 'EFECTIVO' ) {
          # code...
        } else {
          
          if ( empty($_POST["mp_comprobante"]) || isset($_FILES['mp_comprobante']) && $_FILES['mp_comprobante']['name'] ) {
            # code...
          } else {          
            $mp_comprobante = json_decode($_POST["mp_comprobante"], true);
            $decoded_data = base64_decode($mp_comprobante['data']);  // Decodificar el archivo base64
            $ext = explode(".", $mp_comprobante["name"]);          
            $mp_comprobante = $metodo_pago . '__' . $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);          
            $ruta_destino = '../assets/modulo/facturacion/ticket/'.$mp_comprobante; // Ruta donde deseas guardar el archivo en el servidor
            
            if (file_put_contents($ruta_destino, $decoded_data) !== false) { // Guardar el archivo decodificado en el servidor
              # El archivo se ha guardado correctamente en el servidor
            } else {
              $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Archivo no aceptado!!', 'message' => 'El archivo de baucher de pago esta corroido, porfavor cambie de archivo', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
              echo json_encode($retorno, true); die();
            }
          }      
        }          

        if (empty($idventa)) {
          
          $rspta = $facturacion->insertar( $impuesto, $crear_y_emitir,$idsunat_c01  ,$tipo_comprobante, $serie_comprobante, $idpersona_cliente, $observacion_documento,
          $metodo_pago, $total_recibido, $mp_monto, $total_vuelto, $usar_anticipo, $ua_monto_disponible, $ua_monto_usado,  $mp_serie_comprobante,$mp_comprobante, $venta_subtotal, $tipo_gravada, $venta_descuento, $venta_igv, $venta_total,
          $nc_idventa, $nc_tipo_comprobante, $nc_serie_y_numero, $nc_motivo_anulacion, $tiempo_entrega, $validez_cotizacion,
          $_POST["idproducto"], $_POST["pr_marca"], $_POST["pr_categoria"],$_POST["pr_nombre"], $_POST["um_nombre"],$_POST["um_abreviatura"], $_POST["es_cobro"], $_POST["periodo_pago"], $_POST["cantidad"], $_POST["precio_compra"], $_POST["precio_sin_igv"], $_POST["precio_igv"], $_POST["precio_con_igv"],  $_POST["precio_venta_descuento"], 
          $_POST["descuento"], $_POST["descuento_porcentaje"], $_POST["subtotal_producto"], $_POST["subtotal_no_descuento_producto"]); 
          // echo json_encode($rspta, true); die();
          $idventa = $rspta['id_tabla'];

          if ($rspta['status'] == true) {             // validacion de creacion de documento
        
            if ($tipo_comprobante == '12') {          // SUNAT TICKET     
              $update_sunat = $facturacion->actualizar_respuesta_sunat( $idventa, 'ACEPTADA' , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
              echo json_encode($rspta, true);             

            } else if ($tipo_comprobante == '01') {   // SUNAT FACTURA
              
              include( '../modelos/SunatFactura.php');
              $update_sunat = $facturacion->actualizar_respuesta_sunat( $idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);            
              if ( empty($sunat_observacion) && empty($sunat_error) ) {
                echo json_encode($rspta, true); 
              } else {              
                $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                echo json_encode($retorno, true); 
              }                
              
            } else if ($tipo_comprobante == '03') {   // SUNAT BOLETA 
              
              include( '../modelos/SunatBoleta.php');
              $update_sunat = $facturacion->actualizar_respuesta_sunat( $idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
              if ( empty($sunat_observacion) && empty($sunat_error) ) {
                echo json_encode($rspta, true); 
              } else {              
                $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                echo json_encode($retorno, true);
              } 
              
            } else if ($tipo_comprobante == '07') {   // SUNAT NOTA DE CREDITO 
              
              include( '../modelos/SunatNotaCredito.php');
              $update_sunat = $facturacion->actualizar_respuesta_sunat( $idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
              if ( empty($sunat_observacion) && empty($sunat_error) ) {
                $update_sunat = $facturacion->actualizar_doc_anulado_x_nota_credito( $nc_idventa); // CAMBIAMOS DE ESTADO EL DOC ANULADO
                echo json_encode($rspta, true); 
              } else {              
                $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
                echo json_encode($retorno, true);
              }
                  
            } else {
              $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'SUNAT en mantenimiento!!', 'message' => 'El sistema de sunat esta mantenimiento, esperamos su comprención, sea paciente', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
              echo json_encode($retorno, true);
            }
          } else{
            echo json_encode($rspta, true);
          }

        } else {

          $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Datos incompletos', 'message' => 'No se enviaron los datos completos: idventa', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
          echo json_encode($retorno, true);
        }
    
      break; 

      case 'reenviar_sunat':

        $idventa          = $_GET["idventa"];
        $tipo_comprobante = $_GET["tipo_comprobante"];
        $sunat_estado = ""; $sunat_observacion= ""; $sunat_code= ""; $sunat_hash= ""; $sunat_mensaje= ""; $sunat_error= ""; 

        if ($tipo_comprobante == '12') {          // SUNAT TICKET     
          $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Sin respuesta!!', 'message' => 'Este documento no tiene una respuesta de sunat, teniendo en cuenta que es un documento interno de control de la empresa.', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
          echo json_encode($retorno, true);
        } else if ($tipo_comprobante == '01') {   // SUNAT FACTURA         

          include( '../modelos/SunatFactura.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          
          if ( empty($sunat_observacion) && empty($sunat_error) ) {
            echo json_encode($update_sunat, true); 
          } else {              
            $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error . '<br>' . $sunat_observacion , 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
            echo json_encode($retorno, true); 
          }              
          
        } else if ($tipo_comprobante == '03') {   // SUNAT BOLETA 
          
          include( '../modelos/SunatBoleta.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          if ( empty($sunat_observacion) && empty($sunat_error) ) {
            echo json_encode($update_sunat, true); 
          } else {              
            $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error. '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
            echo json_encode($retorno, true);
          }            
          
        } else if ($tipo_comprobante == '07') {   // SUNAT NOTA DE CREDITO 
          include( '../modelos/SunatNotaCredito.php');
          $update_sunat = $facturacion->actualizar_respuesta_sunat( $idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error);
          if ( empty($sunat_observacion) && empty($sunat_error)  ) {
            $update_sunat = $facturacion->actualizar_doc_anulado_x_nota_credito( $nc_idventa); // CAMBIAMOS DE ESTADO EL DOC ANULADO
            echo json_encode($update_sunat, true); 
          } else {              
            $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'Hubo un error en la emisión', 'message' => $sunat_error. '<br>' . $sunat_observacion, 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
            echo json_encode($retorno, true);
          }
        } else {
          $retorno = array( 'status' => 'error_personalizado', 'titulo' => 'SUNAT en mantenimiento!!', 'message' => 'El sistema de sunat esta mantenimiento, esperamos su comprención, sea paciente', 'user' =>  $_SESSION['user_nombre'], 'data' => [], 'id_tabla' => '' );
          echo json_encode($retorno, true);
        }
      break;  

      // :::::::::::: S E C C I O N   V E N T A S ::::::::::::

      case 'listar_tabla_facturacion':

        $rspta = $facturacion->listar_tabla_facturacion($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_cliente"], $_GET["filtro_comprobante"], $_GET["filtro_estado_sunat"] );
        $data = []; $count = 1; #echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){

            $img_proveedor = empty($value['foto_perfil']) ? 'no-perfil.jpg' : $value['foto_perfil'];

            $url_xml = ""; $url_cdr = "";

            if ($value['tipo_comprobante'] == '12') {          // SUNAT TICKET           
            } else if ($value['tipo_comprobante'] == '01') {   // SUNAT FACTURA             
              $url_xml = '../assets/modulo/facturacion/factura/'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.xml'; 
              $url_cdr = '../assets/modulo/facturacion/factura/R-'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.zip';
            } else if ($value['tipo_comprobante'] == '03') {   // SUNAT BOLETA              
              $url_xml = '../assets/modulo/facturacion/boleta/'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.xml'; 
              $url_cdr = '../assets/modulo/facturacion/boleta/R-'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.zip';
            } else if ($value['tipo_comprobante'] == '07') {   // SUNAT NOTA DE CREDITO 
              $url_xml = '../assets/modulo/facturacion/nota_credito/'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.xml'; 
              $url_cdr = '../assets/modulo/facturacion/nota_credito/R-'.$_SESSION['empresa_nd'].'-'.$value['tipo_comprobante'].'-'.$value['serie_comprobante'].'-'.$value['numero_comprobante'].'.zip';
            } else {            
            }

            $data[] = [
              "0" => $count++,
              "1" => '<div class="btn-group ">
                <button type="button" class="btn btn-info btn-sm dropdown-toggle py-1" data-bs-toggle="dropdown" aria-expanded="false"> <i class="ri-settings-4-line"></i></button>
                <ul class="dropdown-menu">                
                <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_editar_venta(' . $value['idventa'] . ');" ><i class="bi bi-eye"></i> Ver</a></li>
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_formato_ticket(' . $value['idventa'] .', \''.$value['tipo_comprobante'] . '\');" ><i class="ti ti-checkup-list"></i> Formato Ticket</a></li>                
                  <li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_formato_a4_completo(' . $value['idventa'] .', \''.$value['tipo_comprobante'] . '\');" ><i class="ti ti-checkup-list"></i> Formato A4 completo</a></li>                
                  <!--<li><a class="dropdown-item" href="javascript:void(0);" onclick="ver_formato_a4_comprimido(' . $value['idventa'] .', \''.$value['tipo_comprobante'] . '\');" ><i class="ti ti-checkup-list"></i> Formato A4 comprimido</a></li>-->
                  '.( $value['tipo_comprobante'] == '12' ? '<li><a class="dropdown-item text-danger" href="javascript:void(0);" onclick="eliminar_papelera_venta(' . $value['idventa'] .', \''.$value['tipo_comprobante'] . '\');" ><i class="bx bx-trash"></i> Eliminar o papelera </a></li>' : '').'  
                </ul>
              </div>',
              "2" =>  $value['idventa_v2'],
              "3" =>  $value['fecha_emision_format'],
              "4" =>  $value['periodo_pago_mes_anio'] ,
              "5" => '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen">
                  <span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/persona/perfil/' . $img_proveedor . '" alt="" onclick="ver_img_pefil(' .$value['idpersona_cliente'] . ')" onerror="'.$imagen_error.'"> </span>
                </div>
                <div>
                  <span class="d-block fw-semibold text-primary" data-bs-toggle="tooltip" title="'.$value['cliente_nombre_completo'] .'">'.$value['cliente_nombre_recortado'] .'</span>
                  <span class="text-muted"><b>'.$value['tipo_documento'] .'</b>: '. $value['numero_documento'].'</span>
                </div>
              </div>',
              "6" =>  '<b>'.$value['tp_comprobante_v2'].'</b>' . ' <br> ' . $value['serie_comprobante'] . '-' . $value['numero_comprobante'],
              "7" =>  $value['venta_total_v2'] , 
              "8" => $value['tipo_comprobante'] == '01' || $value['tipo_comprobante'] == '03' || $value['tipo_comprobante'] == '07' ?
                (
                  $value['sunat_estado'] == 'ACEPTADA' ? 
                  '<a class="badge bg-outline-info fs-13 cursor-pointer m-r-5px" href="'.$url_xml.'" download data-bs-toggle="tooltip" title="Descargar XML" ><i class="bi bi-filetype-xml"></i></a>' . 
                  '<a class="badge bg-outline-info fs-13 cursor-pointer m-r-5px" href="'.$url_cdr.'" download data-bs-toggle="tooltip" title="Descargar CDR" ><i class="bi bi-journal-code"></i></a>' :
                  (
                    $value['sunat_estado'] == 'ANULADO' ? '' :                  
                    '<span class="badge bg-outline-info fs-13 cursor-pointer m-r-5px" data-bs-toggle="tooltip" title="Enviar" onclick="reenviar_doc_a_sunat('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')"><i class="bi bi-upload"></i></span>'
                  )
                )
                : '' , 
              "9" => empty($value['mp_comprobante']) ? '' :  '<center><div class="svg-icon-background bg-warning-transparent cursor-pointer" onclick="ver_comprobante_pago('. $value['idventa'] .');" data-bs-toggle="tooltip" title="Baucher: '. $value['metodo_pago'] .'">                      
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="svg-warning">
                  <path d="M11.5,20h-6a1,1,0,0,1-1-1V5a1,1,0,0,1,1-1h5V7a3,3,0,0,0,3,3h3v5a1,1,0,0,0,2,0V9s0,0,0-.06a1.31,1.31,0,0,0-.06-.27l0-.09a1.07,1.07,0,0,0-.19-.28h0l-6-6h0a1.07,1.07,0,0,0-.28-.19.29.29,0,0,0-.1,0A1.1,1.1,0,0,0,11.56,2H5.5a3,3,0,0,0-3,3V19a3,3,0,0,0,3,3h6a1,1,0,0,0,0-2Zm1-14.59L15.09,8H13.5a1,1,0,0,1-1-1ZM7.5,14h6a1,1,0,0,0,0-2h-6a1,1,0,0,0,0,2Zm4,2h-4a1,1,0,0,0,0,2h4a1,1,0,0,0,0-2Zm-4-6h1a1,1,0,0,0,0-2h-1a1,1,0,0,0,0,2Zm13.71,6.29a1,1,0,0,0-1.42,0l-3.29,3.3-1.29-1.3a1,1,0,0,0-1.42,1.42l2,2a1,1,0,0,0,1.42,0l4-4A1,1,0,0,0,21.21,16.29Z" />
                </svg>
              </div></center>' , 
              "10" =>  ($value['sunat_estado'] == 'ACEPTADA' ? 
                '<span class="badge bg-success-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-check-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' :                    
                '<span class="badge bg-danger-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-close-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>'                              
              ),              
            ];
          }
          $results =[
            'status'=> true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results);

        } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
      break;

      case 'listar_tabla_ver_mas_detalle_facturacion':

        $rspta = $facturacion->listar_tabla_facturacion($_GET["filtro_fecha_i"], $_GET["filtro_fecha_f"], $_GET["filtro_cliente"], $_GET["filtro_comprobante"], $_GET["filtro_estado_sunat"] );
        $data = []; $count = 1; #echo json_encode($rspta); die();

        if($rspta['status'] == true){

          foreach($rspta['data'] as $key => $value){           

            $data[] = [
              "0" => '<span class="text-nowrap fs-11">'. $value['idventa_v2'].'</span>',
              "1" => '<span class="text-nowrap fs-11">'. $value['es_cobro'].'</span>',
              "2" =>  $value['fecha_emision_format'],
              "3" => '<span class="text-nowrap fs-11">'. $value['periodo_pago_month_v2'] .'-'. $value['periodo_pago_year'].'</span>',
              "4" => '<span class="text-nowrap fs-11">'. $value['cliente_nombre_completo'].'</span>',
              "5" => '<span class="text-nowrap fs-11">'. $value['tipo_documento'] .'</span>',
              "6" => '<span class="text-nowrap fs-11">'. $value['numero_documento'].'</span>',
              "7" => '<span class="text-nowrap fs-11">'. $value['tp_comprobante_v2'].'</span>',
              "8" => '<span class="text-nowrap fs-11">'. $value['serie_comprobante'] . '-' . $value['numero_comprobante'].'</span>',
              "9" =>  $value['venta_total_v2'] ,
              "10" =>  $value['total_recibido'] ,
              "11" =>  $value['total_vuelto'] ,
              "12" => '<span class="text-nowrap fs-11">'. $value['metodo_pago'] .'</span>',
              "13" => '<span class="text-nowrap fs-11">'. $value['user_created_v2'] .' '.$value['user_en_atencion'] .'</span>',
              "14" =>  ($value['sunat_estado'] == 'ACEPTADA' ? 
                '<span class="badge bg-success-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-check-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>' :                    
                '<span class="badge bg-danger-transparent cursor-pointer" onclick="ver_estado_documento('. $value['idventa'] .', \''. $value['tipo_comprobante'] .'\')" data-bs-toggle="tooltip" title="Ver estado"><i class="ri-close-fill align-middle me-1"></i>'.$value['sunat_estado'].'</span>'                              
              ),              
            ];
          }
          $results =[
            'status'=> true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results);

        } else { echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data']; }
      break;

      case 'mostrar_detalle_venta':
        $rspta=$facturacion->mostrar_detalle_venta($idventa);
        

        echo '<div class="tab-pane fade active show" id="rol-venta-pane" role="tabpanel" tabindex="0">';
        echo '<div class="table-responsive p-0">
          <table class="table table-hover table-bordered  mt-4">          
            <tbody>
              <tr> <th>Proveedor</th>        <td>'.$rspta['data']['venta']['nombre_razonsocial'].'  '.$rspta['data']['venta']['apellidos_nombrecomercial'].'
              <div class="font-size-12px" >Cel: <a href="tel:+51'.$rspta['data']['venta']['celular'].'">'.$rspta['data']['venta']['celular'].'</a></div> 
              <div class="font-size-12px" >E-mail: <a href="mailto:'.$rspta['data']['venta']['correo'].'">'.$rspta['data']['venta']['correo'].'</a></div> </td> </tr>            
              <tr> <th>Total venta</th>      <td>'.$rspta['data']['venta']['total'].'</td> </tr>             
              <tr> <th>Fecha</th>         <td>'.$rspta['data']['venta']['fecha_venta'].'</td> </tr>                
              <tr> <th>Comprobante</th>   <td>'.$rspta['data']['venta']['tp_comprobante']. ' | '.$rspta['data']['venta']['serie_comprobante'].'</td> </tr>
              <tr> <th>Observacion</th>   <td>'.$rspta['data']['venta']['descripcion'].'</td> </tr>         
            </tbody>
          </table>
        </div>';
        echo '</div>'; # div-content

        echo'<div class="tab-pane fade" id="rol-detalle-pane" role="tabpanel" tabindex="0">';
        echo '<div class="table-responsive p-0">
          <table class="table table-hover table-bordered  mt-4">  
            <thead>
              <tr> <th>#</th> <th>Nombre</th> <th>Cantidad</th> <th>P/U</th> <th>Dcto.</th>  <th>Subtotal</th> </tr>
            </thead>        
            <tbody>';
            foreach ($rspta['data']['detalle'] as $key => $val) {
              echo '<tr> <td>'. $key + 1 .'</td> <td>'.$val['nombre'].'</td> <td class="text-center">'.$val['cantidad'].'</td> <td class="text-right">'.$val['precio_con_igv'].'</td> <td class="text-right">'.$val['descuento'].'</td> <td class="text-right" >'.$val['subtotal'].'</td> </tr>';
            }
        echo '</tbody>
            <tfoot>
              <td colspan="4"></td>

              <th class="text-right">
                <h6 class="tipo_gravada">SUBTOTAL</h6>
                <h6 class="val_igv">IGV (18%)</h6>
                <h5 class="font-weight-bold">TOTAL</h5>
              </th>
              <th class="text-right text-nowrap"> 
                <h6 class="font-weight-bold venta_subtotal">S/ '.$rspta['data']['venta']['subtotal'].'</h6> 
                <h6 class="font-weight-bold venta_igv">S/ '.$rspta['data']['venta']['igv'].'</h6>                 
                <h5 class="font-weight-bold venta_total">S/ '.$rspta['data']['venta']['total'].'</h5>                 
              </th>              
            </tfoot>
          </table>
        </div>';
        echo'</div>';# div-content
      break; 

      case 'mostrar_venta':
        $rspta=$facturacion->mostrar_venta($_POST["idventa"]);
        echo json_encode($rspta, true);
      break; 

      case 'mostrar_cliente':
        $rspta=$facturacion->mostrar_cliente($_POST["idcliente"]);
        echo json_encode($rspta, true);
      break; 

      case 'mostrar_editar_detalles_venta':
        $rspta=$facturacion->mostrar_detalle_venta($_POST["idventa"]);
        echo json_encode($rspta, true);
      break;      

      case 'eliminar':
        $rspta = $facturacion->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'papelera':
        $rspta = $facturacion->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_producto':
        $rspta=$facturacion->mostrar_producto($_POST["idproducto"]);
        echo json_encode($rspta, true);
      break; 

      case 'mini_reporte':
        $rspta=$facturacion->mini_reporte($_GET["periodo_facturado"]);
        echo json_encode($rspta, true);
      break; 

      case 'mini_reporte_v2':
        $rspta = $facturacion->mini_reporte_v2($_GET["filtro_periodo"], $_GET["filtro_trabajador"]);
        echo json_encode($rspta, true);
      break; 

      case 'ver_estado_documento':
        $rspta=$facturacion->mostrar_venta($_GET["idventa"]);
        echo json_encode($rspta, true);
      break; 

      case 'listar_producto_x_codigo':
        $rspta=$facturacion->listar_producto_x_codigo($_POST["codigo"]);
        echo json_encode($rspta, true);
      break;

      case 'listar_tabla_producto':
          
        $rspta = $facturacion->listar_tabla_producto($_GET["tipo_producto"]); 

        $datas = []; 

        if ($rspta['status'] == true) {

          foreach($rspta['data'] as $key => $value){

            $img = empty($value['imagen']) ? 'no-producto.png' : $value['imagen'];
            $data_btn_1 = 'btn-add-producto-1-'.$value['idproducto']; $data_btn_2 = 'btn-add-producto-2-'.$value['idproducto'];
            $datas[] = [
              "0" => '<button class="btn btn-warning '.$data_btn_1.' mr-1 px-2 py-1" onclick="agregarDetalleComprobante(' . $value['idproducto'] .', \''.$_GET["tipo_producto"]. '\', '.($_GET["tipo_producto"] == 'PR' ? 'false': 'true' ).')" data-bs-toggle="tooltip" title="Agregar"><span class="fa fa-plus"></span></button>' ,
              "1" => '<span class="fs-12"> <i class="bi bi-upc"></i> '.$value['codigo'] .'<br> <i class="bi bi-person"></i> '.$value['codigo_alterno'] .'</span>' ,
              "2" =>  '<div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img class="w-35px h-auto" src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre'])) . '\')"> </span></div>
                <div>
                  <span class="d-block fs-12 fw-semibold text-primary nombre_producto_' . $value['idproducto'] . '">'.$value['nombre'] .'</span>
                  <span class="d-block fs-10 text-muted">Marca: <b>'.$value['marca'].'</b> | Categoría: <b>'.$value['categoria'].'</b></span> 
                </div>
              </div>',             
              "3" => ($value['precio_venta']),
              "4" => '<textarea class="textarea_datatable bg-light"  readonly>' .($value['descripcion']). '</textarea>' . $toltip
            ];
          }
  
          $results = [
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($datas), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($datas), //enviamos el total registros a visualizar
            "aaData" => $datas,
          ];
          echo json_encode($results, true);
        } else {
          echo $rspta['code_error'] .' - '. $rspta['message'] .' '. $rspta['data'];
        }
    
      break;

      // ══════════════════════════════════════ COMPROBANTE ══════════════════════════════════════
      

      // ══════════════════════════════════════ U S A R   A N T I C I P O S ══════════════════════════════════════
      case 'mostrar_anticipos':
        $rspta=$facturacion->mostrar_anticipos($_GET["id_cliente"]);
        echo json_encode($rspta, true);
      break; 

      // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
      case 'select2_cliente':
        $rspta = $facturacion->select2_cliente(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $tipo_documento   = $value['tipo_documento'];
            $numero_documento = $value['numero_documento'];
            $direccion        = $value['direccion'];
            $dia_cancelacion= $value['dia_cancelacion_v2'];
            $data .= '<option tipo_documento="'.$tipo_documento.'" dia_cancelacion="'.$dia_cancelacion.'" numero_documento="'.$numero_documento.'" direccion="'.$direccion.'" value="' . $value['idpersona_cliente']  . '">' . $value['cliente_nombre_completo']  . ' - '. $value['nombre_tipo_documento'].': '. $value['numero_documento'] . ' - '. $value['plan_pago'].': '. $value['plan_costo'] . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => '<option tipo_documento="0" dia_cancelacion="" numero_documento="00000000" direccion="" value="1" >CLIENTES VARIOS - 0000000</option>'.$data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break;
      
      case 'select2_comprobantes_anular':
        $rspta = $facturacion->select2_comprobantes_anular($_GET["tipo_comprobante"]); $cont = 1; $data = ""; #echo $rspta; die();
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $idventa            = $value['idventa'];
            $tipo_comprobante   = $value['tipo_comprobante'];
            $serie_comprobante  = $value['serie_comprobante'];
            $numero_comprobante = $value['numero_comprobante'];
            $tp_comprobante_v2  = $value['nombre_tipo_comprobante_v2'];
            $fecha_emision_dif  = $value['fecha_emision_dif'];
            $data .= '<option idventa="'.$idventa.'" tipo_comprobante="'.$tipo_comprobante.'" title="'.$fecha_emision_dif.'"  value="' . $serie_comprobante.'-'. $numero_comprobante  . '">'  . $serie_comprobante.'-'. $numero_comprobante . '</option>';
          }

          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break;

      case 'select2_series_comprobante':
        $rspta = $facturacion->select2_series_comprobante($_GET["tipo_comprobante"], $_GET["nc_tp"]); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option title="' . $value['abreviatura'] . '" value="' . $value['serie']  . '">' . $value['serie']  . '</option>';
          }

          $retorno = array(
            'status'  => true, 
            'message' => 'Salió todo ok', 
            'data'    => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break; 

      case 'select2_codigo_x_anulacion_comprobante':
        $rspta = $facturacion->select2_codigo_x_anulacion_comprobante(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['codigo']  . '">' . $value['codigo'].' - '. $value['nombre']  . '</option>';
          }

          $retorno = array(
            'status'  => true, 
            'message' => 'Salió todo ok', 
            'data'    => $data, 
          );
          echo json_encode($retorno, true);

        } else { echo json_encode($rspta, true); }      
      break; 

      case 'select2_filtro_tipo_comprobante':
        $rspta = $facturacion->select2_filtro_tipo_comprobante($_GET["tipos"]); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idtipo_comprobante']  . '" >' . $value['nombre_tipo_comprobante_v2'] . '</option>';
          }
  
          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);
  
        } else { echo json_encode($rspta, true); }
      break;

      case 'select2_filtro_cliente':
        $rspta = $facturacion->select2_filtro_cliente(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['idpersona_cliente']  . '">' . $cont. '. '. $value['cliente_nombre_completo'] .' - '. $value['nombre_tipo_documento'] .': '. $value['numero_documento'] .' (' .$value['cantidad'].')'. '</option>';
            $cont++;
          }
  
          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);
  
        } else { echo json_encode($rspta, true); }
      break;

      case 'select_categoria':
        $rspta = $productos->select_categoria();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idcategoria'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] . '</option>';
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

      case 'select_u_medida':
        $rspta = $productos->select_u_medida();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idsunat_unidad_medida'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] .' - '. $value['abreviatura'] . '</option>';
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

      case 'select_marca':
        $rspta = $productos->select_marca();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idmarca'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] . '</option>';
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

      case 'select2_banco':
        $rspta = $facturacion->select2_banco();
        $data = "";
  
        if ($rspta['status']) {
  
          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['nombre'] . '" title ="' . $value['icono'] . '" >' . $value['nombre'] . '</option>';
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

      case 'select2_periodo_contable':
        $rspta = $facturacion->select2_periodo_contable(); $cont = 1; $data = "";
        if($rspta['status'] == true){
          foreach ($rspta['data'] as $key => $value) {
            $data .= '<option  value="' . $value['periodo'] . '"> '. $value['periodo_year'] .'-' .$value['periodo_month']. ' ('.$value['cant_comprobante']. ')'. '</option>';
            $cont++;
          }
  
          $retorno = array(
            'status' => true, 
            'message' => 'Salió todo ok', 
            'data' => $data, 
          );
          echo json_encode($retorno, true);
  
        } else { echo json_encode($rspta, true); }
      break;

      default: 
        $rspta = ['status'=>'error_code', 'message'=>'Te has confundido en escribir en el <b>swich.</b>', 'data'=>[]]; echo json_encode($rspta, true); 
      break;

    }

  }else {
    $retorno = ['status'=>'nopermiso', 'message'=>'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => [] ];
    echo json_encode($retorno);
  }
}
ob_end_flush();

?>