<?php
ob_start();
if (strlen(session_id()) < 1) {
  session_start();
}

if (!isset($_SESSION["user_nombre"])) {
  $retorno = ['status' => 'login', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
  echo json_encode($retorno);  //Validamos el acceso solo a los usuarios logueados al sistema.
} else {

  if ($_SESSION['producto'] == 1) {

    require_once "../modelos/Producto.php";
    $productos = new Producto();

    date_default_timezone_set('America/Lima');
    $date_now = date("d_m_Y__h_i_s_A");
    $imagen_error = "this.src='../dist/svg/404-v2.svg'";
    $toltip = '<script> $(function () { $(\'[data-bs-toggle="tooltip"]\').tooltip(); }); </script>';

    $idproducto        = isset($_POST["idproducto"]) ? limpiarCadena($_POST["idproducto"]) : "";

    $idsucursal        = isset($_POST["sucursal"])? limpiarCadena($_POST["sucursal"]):"";
    $tipo              = isset($_POST["tipo"]) ? limpiarCadena($_POST["tipo"]) : "";
    $codigo_alterno    = isset($_POST["codigo_alterno"]) ? limpiarCadena($_POST["codigo_alterno"]) : "";
    $categoria         = isset($_POST["categoria"]) ? limpiarCadena($_POST["categoria"]) : "";
    $u_medida          = isset($_POST["u_medida"]) ? limpiarCadena($_POST["u_medida"]) : "";
    $tipo_igv          = isset($_POST["tipo_igv"]) ? limpiarCadena($_POST["tipo_igv"]) : "";
    $marca             = isset($_POST["marca"]) ? limpiarCadena($_POST["marca"]) : "";
    $nombre            = isset($_POST["nombre"]) ? limpiarCadena($_POST["nombre"]) : "";
    $descripcion       = isset($_POST["descripcion"]) ? limpiarCadena($_POST["descripcion"]) : "";
    $stock             = isset($_POST["stock"]) ? limpiarCadena($_POST["stock"]) : "";
    $stock_min         = isset($_POST["stock_min"]) ? limpiarCadena($_POST["stock_min"]) : "";
    $precio_v          = isset($_POST["precio_v"]) ? limpiarCadena($_POST["precio_v"]) : "";
    //$precio_v_sin_igv  = isset($_POST["precio_v_sin_igv"]) ? limpiarCadena($_POST["precio_v_sin_igv"]) : "";
    $precio_c          = isset($_POST["precio_c"]) ? limpiarCadena($_POST["precio_c"]) : "";
    $x_ganancia_max    = isset($_POST["x_ganancia_max"]) ? limpiarCadena($_POST["x_ganancia_max"]) : "";
    $x_ganancia_min    = isset($_POST["x_ganancia_min"]) ? limpiarCadena($_POST["x_ganancia_min"]) : "";
    $precio_v_min      = isset($_POST["precio_v_min"]) ? limpiarCadena($_POST["precio_v_min"]) : "";
    $Peso_kg           = isset($_POST["Peso_kg"]) ? limpiarCadena($_POST["Peso_kg"]) : "";

    $nombre_multip     = isset($_POST["nombre_multip"]) ? $_POST["nombre_multip"] : "";
    $monto_multip      = isset($_POST["monto_multip"]) ? $_POST["monto_multip"] : "";


    $code_present      = isset($_POST["code_present"]) ? $_POST["code_present"] : "";
    $nombre_present    = isset($_POST["nombre_present"]) ? $_POST["nombre_present"] : "";
    $u_medida_present  = isset($_POST["u_medida_present"]) ? $_POST["u_medida_present"] : "";
    $cant_present      = isset($_POST["cant_present"]) ? $_POST["cant_present"] : "";
    $precio_c_present  = isset($_POST["precio_c_present"]) ? $_POST["precio_c_present"] : "";
    $precio_v_present  = isset($_POST["precio_v_present"]) ? $_POST["precio_v_present"] : "";
    $precio_vm_present = isset($_POST["precio_vm_present"]) ? $_POST["precio_vm_present"] : "";

    switch ($_GET["op"]) {

      case 'guardar_editar':
        //guardar forma cuadrada
        if (!file_exists($_FILES['doc1']['tmp_name']) || !is_uploaded_file($_FILES['doc1']['tmp_name'])) {
          $img_1 = $_POST["doc_old_1"];
          $flat_img = false;
        } else {
          $ext = explode(".", $_FILES["doc1"]["name"]);
          $flat_img = true;
          $img_1 = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
          move_uploaded_file($_FILES["doc1"]["tmp_name"], "../assets/modulo/productos/" . $img_1);
        }

        //guardar froma Horizontal
        if (!file_exists($_FILES['doc2']['tmp_name']) || !is_uploaded_file($_FILES['doc2']['tmp_name'])) {
          $img_2 = $_POST["doc_old_2"];
          $flat_img = false;
        } else {
          $ext = explode(".", $_FILES["doc2"]["name"]);
          $flat_img = true;
          $img_2 = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
          move_uploaded_file($_FILES["doc2"]["tmp_name"], "../assets/modulo/productos/" . $img_2);
        }

        //guardar Forma vertical
        if (!file_exists($_FILES['doc3']['tmp_name']) || !is_uploaded_file($_FILES['doc3']['tmp_name'])) {
          $img_3 = $_POST["doc_old_3"];
          $flat_img = false;
        } else {
          $ext = explode(".", $_FILES["doc3"]["name"]);
          $flat_img = true;
          $img_3 = $date_now . '__' . random_int(0, 20) . round(microtime(true)) . random_int(21, 41) . '.' . end($ext);
          move_uploaded_file($_FILES["doc3"]["tmp_name"], "../assets/modulo/productos/" . $img_3);
        }

        if (empty($idproducto)) { #Creamos el registro

          $rspta = $productos->insertar(
            $idsucursal,$tipo,$codigo_alterno,$categoria,$u_medida,$tipo_igv,$marca,$nombre,$descripcion,
            $stock,$stock_min,$precio_v,$precio_c,$x_ganancia_max,$x_ganancia_min,$precio_v_min,
            $Peso_kg,$nombre_multip,$monto_multip,$code_present,$nombre_present,
            $u_medida_present,$cant_present,$precio_c_present,$precio_v_present,$precio_vm_present,$img_1,$img_2,$img_3
          );

          echo json_encode($rspta, true);

        } else { # Editamos el registro

          if ($flat_img == true || empty($img_1)) {
            $datos_f1 = $productos->return_image($idproducto,'imagen_cuadrado');
            $img1_ant = $datos_f1['data']['imagen'];
            if (!empty($img1_ant)) {
              unlink("../assets/modulo/productos/" . $img1_ant);
            }
          }

          if ($flat_img == true || empty($img_2)) {
            $datos_f2 = $productos->return_image($idproducto,'imagen_horizontal');
            $img2_ant = $datos_f2['data']['imagen'];
            if (!empty($img2_ant)) {
              unlink("../assets/modulo/productos/" . $img2_ant);
            }
          }

          if ($flat_img == true || empty($img_3)) {
            $datos_f3 = $productos->return_image($idproducto,'imagen_vertical');
            $img3_ant = $datos_f3['data']['imagen'];
            if (!empty($img3_ant)) {
              unlink("../assets/modulo/productos/" . $img3_ant);
            }
          }

          $rspta = $productos->editar(
            $idproducto,$idsucursal,$tipo,$codigo_alterno,$categoria,$u_medida,$tipo_igv,$marca,$nombre,$descripcion,
            $stock,$stock_min,$precio_v,$precio_c,$x_ganancia_max,$x_ganancia_min,$precio_v_min,
            $Peso_kg,$nombre_multip,$monto_multip,$code_present,$nombre_present,
            $u_medida_present,$cant_present,$precio_c_present,$precio_v_present,$precio_vm_present,$img_1,$img_2,$img_3 );

          echo json_encode($rspta, true);
        }

      break;

      case 'mostrar':
        $rspta = $productos->mostrar($idproducto);
        echo json_encode($rspta, true);
      break;

      case 'mostrar_detalle_producto':
        $tipo = $_POST['tipo']; $html_table ='';
        $rspta = $productos->mostrar($idproducto); //echo json_encode($rspta, true); die();
        if ($rspta['status']) {
          if ($tipo =='producto'){

                          $html_table ='<div class="col-xl-4">
                            <div class="row">
                              <div class="col-xl-12">
                                <div class="card custom-card">
                                  <div class="card-header">
                                    <div class="card-title" data-bs-toggle="tooltip" title="Códigos del Producto">Códigos</div>
                                    <div class="ms-auto text-success"> <span class="fw-semibold text-success" data-bs-toggle="tooltip" title="Código Producto">' . $rspta['data']['prod']['codigo'] . ' - </span><span class="fw-semibold text-success" data-bs-toggle="tooltip" title="Código Alterno">' . $rspta['data']['prod']['codigo_alterno'] . ' </span></div>
                                  </div>
                                  <div class="card-body p-0">
                                    <div class="p-2 border-bottom border-block-end-dashed">
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Nombre : </span>' . $rspta['data']['prod']['nombre'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Descripción : </span>' . $rspta['data']['prod']['descripcion'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Categoría : </span>' . $rspta['data']['prod']['categoria'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Marca : </span>' . $rspta['data']['prod']['marca'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">U.M : </span>' . $rspta['data']['prod']['unidad_medida'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Peso KG : </span>' . $rspta['data']['prod']['peso'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Creado : </span>' . $rspta['data']['prod']['created_at'] . '</p>
                                    </div>
                                    <div class="p-2 border-bottom border-block-end-dashed">
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Stock : </span>' . $rspta['data']['prod']['stock'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Stock Mínimo : </span>' . $rspta['data']['prod']['stock_minimo'] . '</p>
                                    </div>
                                    <div class="p-2  border-bottom border-block-end-dashed">
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Precio Compra : </span>' . $rspta['data']['prod']['precio_compra'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Precio Venta : </span>' . $rspta['data']['prod']['precio_venta'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">P. Venta Mín : </span>' . $rspta['data']['prod']['precio_venta_minima'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Ganancia Máx : </span>' . $rspta['data']['prod']['ganancia_maxima'] . '</p>
                                      <p class="mb-2 text-muted"><span class="fw-semibold text-default">Ganancia Mín : </span>' . $rspta['data']['prod']['ganacia_minima'] . '</p>

                                    </div>
                                   </div>
                                </div>
                              </div>
                            </div>
                          </div>';

                          $html_table .='<div class="col-xl-4">
                            <div class="card custom-card">
                              <div class="card-header">
                                <div class="card-title">
                                  Presentaciones
                                </div>
                              </div>
                              <div class="card-body">
                                <div class="order-track">';
                                foreach ($rspta['data']['present'] as $key => $val) {
                                  $html_table .='<div class="accordion" id="basicAccordion'.$val['idpp'].'">
                                    <div class="accordion-item border-0 bg-transparent next-step">
                                      <div class="accordion-header" id="headingFour'.$val['idpp'].'">
                                        <a class="px-0 pt-0 collapsed" href="javascript:void(0)" role="button" data-bs-toggle="collapse" data-bs-target="#ColapsePresentacion'.$val['idpp'].'" aria-expanded="false" aria-controls="ColapsePresentacion'.$val['idpp'].'">
                                          <div class="d-flex mb-2">
                                            <div class="me-2">
                                              <span class="avatar avatar-md avatar-rounded bg-primary-transparent text-primary border"><i class="fe fe-package fs-12"></i></span>
                                            </div>
                                            <div class="flex-fill">
                                              <p class="fw-semibold mb-0 fs-14">'.$val['nombre'].'</p>
                                              <span class="text-primary fs-12">Cód Alterno - '.$val['codigo'].'</span>
                                            </div>
                                          </div>
                                        </a>
                                      </div>
                                      <div id="ColapsePresentacion'.$val['idpp'].'" class="accordion-collapse border-top-0 collapse '.( count($rspta['data']['present']) > 2 ? '':'show').' " aria-labelledby="headingFour'.$val['idpp'].'" data-bs-parent="#basicAccordion'.$val['idpp'].'">
                                        <div class="accordion-body pt-0 ps-5">
                                          <div class="fs-11">
                                            <p class="mb-2 text-muted"><span class="fw-semibold text-default">U.M : </span>' . $val['unidad_medida_present'] . '</p>
                                            <p class="mb-2 text-muted"><span class="fw-semibold text-default">Contiene : </span>' . $val['cantidad'] . ' ' . $rspta['data']['prod']['unidad_medida'] . '</p>
                                            <p class="mb-2 text-muted"><span class="fw-semibold text-default">Precio Compra : </span>' . $val['precio_compra'] . '</p>
                                            <p class="mb-2 text-muted"><span class="fw-semibold text-default">Precio Venta : </span>' . $val['precio_venta'] . '</p>
                                            <p class="mb-2 text-muted"><span class="fw-semibold text-default">Precio Venta Mín : </span>' . $val['precio_minimo'] . '</p>

                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>';
                                }                        
                          $html_table .='</div>
                              </div>
                            </div>
                          </div>';

                          $conta_mul =1;

                          $html_table .='<div class="col-xl-4">
                          <div class="card custom-card">
                            <div class="card-header">
                              <div class="card-title">
                                Multi Precios
                              </div>
                            </div>
                            <div class="card-body">
                              <div class="order-track">';
                              foreach ($rspta['data']['mult_p'] as $key => $value) {
                                
                                $html_table .='<div class="accordion" id="basicAccordion'.$value['idproducto_precio'].'">
                                  <div class="accordion-item border-0 bg-transparent next-step">
                                    <div class="accordion-header" id="headingFour'.$value['idproducto_precio'].'">
                                      <a class="px-0 pt-0 collapsed" href="javascript:void(0)" role="button" data-bs-toggle="collapse" data-bs-target="#ColapsePrecio'.$value['idproducto_precio'].'" aria-expanded="false" aria-controls="ColapsePrecio'.$value['idproducto_precio'].'">
                                        <div class="d-flex mb-2">
                                          <div class="me-2">
                                            <span class="avatar avatar-md avatar-rounded bg-primary-transparent text-primary border"><i class="fe fe-package fs-12"></i></span>
                                          </div>
                                          <div class="flex-fill">
                                            <p class="fw-semibold mb-0 fs-14">Multi - Precio 0'.$conta_mul .'</p>
                                            <span class="text-primary fs-12">' . $value['nombre'] . ' - S/. '. $value['precio_venta'].'</span>
                                          </div>
                                        </div>
                                      </a>
                                    </div>
                                    <div id="ColapsePrecio'.$value['idproducto_precio'].'" class="accordion-collapse border-top-0 collapse show" aria-labelledby="headingFour'.$value['idproducto_precio'].'" data-bs-parent="#basicAccordion'.$value['idproducto_precio'].'">
                                      <div class="accordion-body pt-0 ps-5">
                                        <div class="fs-11">
                                          <p class="mb-2 text-muted"><span class="fw-semibold text-default">Nombre : </span>' . $value['nombre'] . '</p>
                                          <p class="mb-2 text-muted"><span class="fw-semibold text-default">Precio S/. : </span>' . $value['precio_venta'] . '</p>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>';
                                $conta_mul++;
                              }                        
                          $html_table .='</div>
                                        </div>
                                      </div>
                                    </div>'.$toltip;

          }elseif ($tipo=='en_otras_sucursales') {
            $html_table ='';
          }else{
            
            $html_table ='<div class="col-xl-12">
            <div class="row">
              <div class="col-xl-12">
                <div class="card custom-card">
                  <div class="card-body p-0">
                    <div class="row">
                      <div class="col-xl-4">
                        <div class="kanban-content mt-2 mb-2 border-bottom border-block-end-dashed">
                          <div class="task-image mt-2 mb-2">
                            <img src="../assets/modulo/productos/' . $rspta['data']['prod']['imagen_cuadrado'] . '" class="img-fluid rounded kanban-image" style="width: 80%;" alt="">
                          </div>
                        </div>
                      </div>
                      <div class="col-xl-4">
                        <div class="kanban-content mt-2 mb-2 border-bottom border-block-end-dashed">
                          <div class="task-image mt-2 mb-2">
                            <img src="../assets/modulo/productos/' . $rspta['data']['prod']['imagen_horizontal'] . '" class="img-fluid rounded kanban-image" style="width: 80%;" alt="">
                          </div>
                        </div>
                      </div>
                      <div class="col-xl-4">
                        <div class="kanban-content mt-2 mb-2 border-bottom border-block-end-dashed">
                          <div class="task-image mt-2 mb-2">
                            <img src="../assets/modulo/productos/' . $rspta['data']['prod']['imagen_vertical'] . '" class="img-fluid rounded kanban-image" style="width: 80%;" alt="">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>';

          }
          //$rspta = ['status' => true, 'message' => 'Todo bien', 'data' => $html_table];
          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $html_table,);
          echo json_encode($retorno, true);

        } else {
          echo json_encode($rspta, true);
        }
        
      break;

      case 'eliminar':
        $rspta = $productos->eliminar($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'papelera':
        $rspta = $productos->papelera($_GET["id_tabla"]);
        echo json_encode($rspta, true);
      break;

      case 'listar_tabla_p':
        $rspta = $productos->listar_tabla($_GET["categoria"], $_GET["unidad_medida"], $_GET["marca"]);

        $data = [];
        $count = 2;
        if ($rspta['status'] == true) {
          foreach ($rspta['data'] as $key => $value) {
            $img = empty($value['imagen_cuadrado']) ? 'no-producto.png' : $value['imagen_cuadrado'];
            $data[] = [
              "0" => $value['idproducto'] == 1 ? 1 : $count++,
              "1" => ($value['idproducto'] == 1 ? '<i class="bi bi-exclamation-triangle text-danger fs-6"></i>' :
                '<div class="hstack gap-2 fs-15 text-center"> 
                <button class="btn btn-icon btn-sm btn-warning-light border-warning" onclick="mostrar_producto(' . ($value['idproducto']) . ')" data-bs-toggle="tooltip" title="Editar"><i class="ri-edit-line"></i></button>' .
                '<button  class="btn btn-icon btn-sm btn-danger-light border-danger product-btn" onclick="eliminar_papelera_producto(' . $value['idproducto'] . '.,\'' . $value['nombre'] . '\')" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>' .
                '<button type="button" class=" btn-sm btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fa-solid fa-list"></i>
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item cursor-pointer " onclick="ver_producto(' . ($value['idproducto']) . ',\'' . $value['nombre'] . '\', \'' . "producto" . '\' )" >Ver Detalle</a></li>
              <li><a class="dropdown-item cursor-pointer " onclick="ver_producto(' . ($value['idproducto']) . ',\'' . $value['nombre'] . '\', \'' . "en_otras_sucursales" . '\')" >Stock - Sucursales</a></li>
              <li><a class="dropdown-item cursor-pointer " onclick="ver_producto(' . ($value['idproducto']) . ',\'' . $value['nombre'] . '\', \'' . "imagenes" . '\')" >Imagenes</a></li>
            </ul>
              </div>'),
              "2" => ('<i class="bi bi-upc"></i> ' . $value['codigo'] . '<br> <i class="bi bi-person"></i> ' . $value['codigo_alterno']),
              "3" => '<div class="d-flex flex-fill align-items-center">
                        <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="../assets/modulo/productos/' . $img . '" alt="" onclick="ver_img(\'' . $img . '\', \'' . encodeCadenaHtml(($value['nombre'])) . '\')"> </span></div>
                        <div>
                          <h6 class="d-block fw-semibold text-primary">' . $value['nombre'] . '</h6>
                          <span class="d-block fs-11 text-muted">Marca: <b>' . $value['marca'] . '</b> | Categoría: <b>' . $value['categoria'] . '</b></span> 
                        </div>
                      </div>',
              "4" => ($value['stock']),
              "5" => ($value['unidad_medida']),
              "6" => ($value['precio_compra']),
              "7" => ($value['precio_venta']),
              "8" => '<textarea class="textarea_datatable bg-light"  readonly>' . ($value['descripcion']) . '</textarea>',
              "9" => ($value['estado'] == '1') ? '<span class="badge bg-success-transparent"><i class="ri-check-fill align-middle me-1"></i>Activo</span>' : '<span class="badge bg-danger-transparent"><i class="ri-close-fill align-middle me-1"></i>Desactivado</span>',

              "10" => ($value['categoria']),
              "11" => ($value['marca']),
              "12" => ($value['nombre']),
              "13" => ($value['codigo']),
              "14" => ($value['codigo_alterno']),
              "15" => ($value['idproducto'])
            ];
          }
          $results = [
            'status' => true,
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
          ];
          echo json_encode($results);
        } else {
          echo $rspta['code_error'] . ' - ' . $rspta['message'] . ' ' . $rspta['data'];
        }
      break;

        // ══════════════════════════════════════  VALIDACION DE CODIGO  ══════════════════════════════════════
      case 'validar_code_producto':
        $rspta = $productos->validar_code_producto($_GET["idproducto"], $_GET["codigo_alterno"]);
        echo json_encode($rspta, true);
        break;

        // ══════════════════════════════════════  S E L E C T 2 - P A R A   F O R M  ══════════════════════════════════════

      case 'select_categoria':
        $rspta = $productos->select_categoria();
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_categoria'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);

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
            $data  .= '<option value="' . $value['idsunat_c03_unidad_medida'] . '" title ="' . $value['descripcion'] . '" abrv="' . $value['abreviatura'] . '">' . $value['nombre'] . ' - ' . $value['abreviatura'] . '</option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);

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
            $data  .= '<option value="' . $value['idproducto_marca'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);

          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

        // ══════════════════════════════════════  S E L E C T 2 - PARA FILTROS ══════════════════════════════════════ 
      case 'select2_filtro_categoria':
        $rspta = $productos->select2_filtro_categoria();
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_categoria'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }

          $retorno = array('status' => true,  'message' => 'Salió todo ok', 'data' => $data,);
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_filtro_u_medida':
        $rspta = $productos->select2_filtro_u_medida();
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idsunat_c03_unidad_medida'] . '" title ="' . $value['descripcion'] . '" abrv="' . $value['abreviatura'] . '" >' . $value['nombre'] . ' - ' . $value['abreviatura'] . '</option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);
          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_filtro_marca':
        $rspta = $productos->select2_filtro_marca();
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idproducto_marca'] . '" title ="' . $value['descripcion'] . '" >' . $value['nombre'] . '</option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);

          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      case 'select2_tipo_igv':
        $rspta = $productos->select2_tipo_igv();
        $data = "";

        if ($rspta['status']) {

          foreach ($rspta['data'] as $key => $value) {
            $data  .= '<option value="' . $value['idsunat_c07_afeccion_de_igv'] . '" title ="' . $value['nombre'] . '" >' . $value['nombre'] . '</option>';
          }

          $retorno = array('status' => true, 'message' => 'Salió todo ok', 'data' => $data,);

          echo json_encode($retorno, true);
        } else {
          echo json_encode($rspta, true);
        }
      break;

      default:
        $rspta = ['status' => 'error_code', 'message' => 'Te has confundido en escribir en el <b>swich.</b>', 'data' => []];
        echo json_encode($rspta, true);
      break;
    }
  } else {
    $retorno = ['status' => 'nopermiso', 'message' => 'Tu sesion a terminado pe, inicia nuevamente', 'data' => [], 'aaData' => []];
    echo json_encode($retorno);
  }
}
ob_end_flush();
