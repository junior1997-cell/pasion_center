<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

class Producto
{

  //Implementamos nuestro constructor
  public $id_usr_sesion;
  // public $id_empresa_sesion;
  //Implementamos nuestro constructor
  public function __construct($id_usr_sesion = 0, $id_empresa_sesion = 0)
  {
    $this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
    // $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
  }

  public function insertar(
    $idsucursal, $tipo, $codigo_alterno, $categoria, $u_medida, $tipo_igv, $marca, $nombre,
    $descripcion, $stock, $stock_min, $precio_v, $precio_c, $x_ganancia_max, $x_ganancia_min,
    $precio_v_min, $Peso_kg, $nombre_multip, $monto_multip, $code_present,
    $nombre_present, $u_medida_present, $cant_present, $precio_c_present, $precio_v_present, $precio_vm_present,
    $img_1, $img_2, $img_3
  ) {

    $sql_0 = "SELECT * FROM producto WHERE nombre = '$nombre';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe; }

    if (empty($existe['data'])) {

      $sql = "INSERT INTO producto(idsunat_c03_unidad_medida, idproducto_marca, idproducto_categoria, idsunat_c07_afeccion_de_igv, tipo, 
      codigo_alterno, nombre, peso, descripcion, imagen_cuadrado, imagen_horizontal, imagen_vertical) 
      VALUES ('$u_medida','$marca','$categoria','$tipo_igv','$tipo','$codigo_alterno','$nombre','$Peso_kg','$descripcion','$img_1','$img_2','$img_3')";

      $id_new = ejecutarConsulta_retornarID($sql, 'C'); if ($id_new['status'] == false) {return $id_new;}

      $id_pr = $id_new['data'];

      $zz = 0;
      $yy = 0;

      $sql_1 ="INSERT INTO producto_sucursal
      (idproducto, sucursal_idsucursal, stock, stock_minimo, precio_compra, precio_venta, precio_venta_minima, ganancia_maxima, ganacia_minima) 
      VALUES ('$id_pr','$idsucursal','$stock','$stock_min','$precio_c','$precio_v','$precio_v_min','$x_ganancia_max','$x_ganancia_min')";
      $id_new_sucursal = ejecutarConsulta_retornarID($sql_1, 'C'); if ($id_new_sucursal['status'] == false) {return $id_new_sucursal;}

      $id_suc = $id_new_sucursal['data'];

      if (!empty($monto_multip)) {
        
        while ($zz < count($monto_multip)) {
          $sql_multi_p = "INSERT into producto_precio(idproducto_sucursal, nombre,precio_venta) values ('$id_suc', '$nombre_multip[$zz]','$monto_multip[$zz]')";
          $usr_mp = ejecutarConsulta($sql_multi_p, 'C'); if ($usr_mp['status'] == false) { return $usr_mp; }
          $zz = $zz + 1;
        }
      }

      if (!empty($code_present)) {

        while ($yy < count($code_present)) {

          $sql_prsent_p = "INSERT INTO producto_presentacion(idproducto, idsunat_c03_unidad_medida, codigo, nombre, precio_compra, precio_venta,
          precio_minimo, cantidad) 
          VALUES ('$id_pr','$u_medida_present[$yy]','$code_present[$yy]','$nombre_present[$yy]','$precio_c_present[$yy]','$precio_v_present[$yy]',
          '$precio_vm_present[$yy]', '$cant_present[$yy]')";

          $prsent_p = ejecutarConsulta($sql_prsent_p, 'C'); if ($prsent_p['status'] == false) { return $prsent_p; }

          $yy = $yy + 1;

        }
      }
      
      return $id_new;

    } else {
      $info_repetida = '';

      foreach ($existe['data'] as $key => $value) {
        $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>' . $value['nombre'] . '</span><br>
            <b>Papelera: </b>' . ($value['estado'] == 0 ? '<i class="fas fa-check text-success"></i> SI' : '<i class="fas fa-times text-danger"></i> NO') . ' <b>|</b>
            <b>Eliminado: </b>' . ($value['estado_delete'] == 0 ? '<i class="fas fa-check text-success"></i> SI' : '<i class="fas fa-times text-danger"></i> NO') . '<br>
            <hr class="m-t-2px m-b-2px">
          </li>';
      }
      return array('status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>' . $info_repetida . '</ul>', 'id_tabla' => '');
    }
  }

  public function editar(
    $idproducto,$idsucursal,$tipo,$codigo_alterno,$categoria,$u_medida,$tipo_igv,$marca,$nombre,$descripcion,
    $stock,$stock_min,$precio_v,$precio_c,$x_ganancia_max,$x_ganancia_min,$precio_v_min,
    $Peso_kg,$nombre_multip,$monto_multip,$code_present,$nombre_present,
    $u_medida_present,$cant_present,$precio_c_present,$precio_v_present,$precio_vm_present,$img_1,$img_2,$img_3
   ) {

    $sql_0 = "SELECT * FROM producto WHERE nombre = '$nombre' AND idproducto <> '$idproducto';";

    $existe = ejecutarConsultaArray($sql_0);

    if ($existe['status'] == false) { return $existe; }

    if (empty($existe['data'])) {

      $sql = "UPDATE producto SET idsunat_c03_unidad_medida = '$u_medida', idproducto_categoria = '$categoria', idproducto_marca = '$marca', 
      tipo = '$tipo', codigo_alterno = '$codigo_alterno', nombre = '$nombre', descripcion = '$descripcion', imagen_cuadrado = '$img_1',
      imagen_horizontal='$img_2', imagen_vertical='$img_3', idsunat_c07_afeccion_de_igv='$tipo_igv',peso='$Peso_kg' WHERE idproducto = '$idproducto'";
      
      $edit_user = ejecutarConsulta($sql, 'U');
      
      if ($edit_user['status'] == false) { return $edit_user; }

      $sql_p_s = "UPDATE producto_sucursal SET stock='$stock', 
      stock_minimo='$stock_min', precio_compra='$precio_c', precio_venta='$precio_v', 
      precio_venta_minima='$precio_v_min', ganancia_maxima='$x_ganancia_max', ganacia_minima='$x_ganancia_min' 
      WHERE idproducto='$idproducto' and sucursal_idsucursal='$idsucursal' ";
      
      $id_new_sucursal = ejecutarConsulta($sql_p_s, 'U'); if ($id_new_sucursal['status'] == false) {return $id_new_sucursal;}

      //Eliminamos los precios

      $sql_del_multi ="DELETE FROM producto_precio WHERE idproducto_sucursal='$idsucursal'    ";
      $sql_del_multiprec = ejecutarConsulta($sql_del_multi, 'U'); if ($sql_del_multiprec['status'] == false) {return $sql_del_multiprec;}

      //eliminamos presentaciones

      $sql_del_present ="DELETE FROM producto_presentacion WHERE idproducto='$idproducto'";
      $sql_del_presnt = ejecutarConsulta($sql_del_present, 'U'); if ($sql_del_presnt['status'] == false) {return $sql_del_presnt;}

      $zz = 0;
      $yy = 0;

      if (!empty($monto_multip)) {
        
        while ($zz < count($monto_multip)) {
          $sql_multi_p = "INSERT into producto_precio(idproducto_sucursal, nombre,precio_venta) values ('$idsucursal', '$nombre_multip[$zz]','$monto_multip[$zz]')";
          $usr_mp = ejecutarConsulta($sql_multi_p, 'C'); if ($usr_mp['status'] == false) { return $usr_mp; }
          $zz = $zz + 1;
        }
      }

      if (!empty($code_present)) {

        while ($yy < count($code_present)) {

          $sql_prsent_p = "INSERT INTO producto_presentacion(idproducto, idsunat_c03_unidad_medida, codigo, nombre, precio_compra, precio_venta,
          precio_minimo, cantidad) 
          VALUES ('$idproducto','$u_medida_present[$yy]','$code_present[$yy]','$nombre_present[$yy]','$precio_c_present[$yy]','$precio_v_present[$yy]',
          '$precio_vm_present[$yy]', '$cant_present[$yy]')";

          $prsent_p = ejecutarConsulta($sql_prsent_p, 'C'); if ($prsent_p['status'] == false) { return $prsent_p; }

          $yy = $yy + 1;

        }
      }

      return $edit_user;

    } else {

      $info_repetida = '';

      foreach ($existe['data'] as $key => $value) {
        $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-15px text-danger"><b>' . $value['nombre'] . '</span><br>
            <b>Papelera: </b>' . ($value['estado'] == 0 ? '<i class="fas fa-check text-success"></i> SI' : '<i class="fas fa-times text-danger"></i> NO') . ' <b>|</b>
            <b>Eliminado: </b>' . ($value['estado_delete'] == 0 ? '<i class="fas fa-check text-success"></i> SI' : '<i class="fas fa-times text-danger"></i> NO') . '<br>
            <hr class="m-t-2px m-b-2px">
          </li>';
      }

      return array('status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>' . $info_repetida . '</ul>', 'id_tabla' => '');
    }
  }

  function mostrar($id)
  {

    $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca, ps.* 
    FROM producto AS p 
    INNER JOIN producto_sucursal as ps on p.idproducto = ps.idproducto 
    INNER JOIN sunat_c03_unidad_medida AS sum ON p.idsunat_c03_unidad_medida = sum.idsunat_c03_unidad_medida 
    INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria 
    INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca WHERE p.idproducto='$id';";

    $prod = ejecutarConsultaSimpleFila($sql); if ($prod['status'] == false) { return $prod; }

    $sql_1 = "SELECT pp.idproducto_presentacion as idpp, pp.idproducto, pp.idsunat_c03_unidad_medida, pp.codigo, pp.nombre, 
    pp.precio_compra, pp.precio_venta, pp.precio_minimo, pp.cantidad, pp.estado, pp.estado_delete, s_um.nombre as unidad_medida_present 
    FROM producto_presentacion as pp 
    INNER JOIN sunat_c03_unidad_medida as s_um on pp.idsunat_c03_unidad_medida=s_um.idsunat_c03_unidad_medida 
    WHERE pp.idproducto='$id';";

    $present = ejecutarConsultaArray($sql_1); if ($present['status'] == false) { return $present; }

    $sql_2 = "SELECT pp.idproducto_precio, ps.idproducto_sucursal,p.idproducto, pp.nombre, pp.precio_venta, pp.estado 
    FROM producto_precio as pp INNER JOIN producto_sucursal as ps on pp.idproducto_sucursal =ps.idproducto_sucursal 
    INNER JOIN producto as p on ps.idproducto = p.idproducto WHERE p.idproducto='$id';";

    $mult_p = ejecutarConsultaArray($sql_2); if ($mult_p['status'] == false) { return $mult_p; }


    return ['status' => true, 'message' => 'Todo ok', 'data' => ['prod' => $prod['data'], 'present' => $present['data'], 'mult_p' => $mult_p['data']]];


  }

  function listar_tabla($categoria, $unidad_medida, $marca)
  {

    // var_dump($categoria, $unidad_medida, $marca); die();
    $filtro_categoria = ""; $filtro_unidad_medida = ""; $filtro_marca = "";

    if (empty($categoria)) {
    } else {
      $filtro_categoria = "AND p.idproducto_categoria = '$categoria'";
    }
    if (empty($unidad_medida)) {
    } else {
      $filtro_unidad_medida = "AND p.idsunat_c03_unidad_medida = '$unidad_medida'";
    }
    if (empty($marca)) {
    } else {
      $filtro_marca = "AND p.idproducto_marca = '$marca'";
    }

    $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca, ps.stock, ps.precio_compra, ps.precio_venta 
    FROM producto AS p 
    INNER JOIN producto_sucursal as ps on p.idproducto = ps.idproducto 
    INNER JOIN sunat_c03_unidad_medida AS sum ON p.idsunat_c03_unidad_medida = sum.idsunat_c03_unidad_medida 
    INNER JOIN producto_categoria AS cat ON p.idproducto_categoria = cat.idproducto_categoria 
    INNER JOIN producto_marca AS mc ON p.idproducto_marca = mc.idproducto_marca 
    WHERE p.idproducto_categoria <> 2 AND p.estado = 1 AND p.estado_delete = 1 $filtro_categoria $filtro_unidad_medida $filtro_marca
     ORDER BY p.nombre ASC;";
    return ejecutarConsulta($sql);
  }


  function mostrar_detalle_producto($id)
  {
    $sql = "SELECT p.*, sum.nombre AS unidad_medida, cat.nombre AS categoria, mc.nombre AS marca, ps.stock, ps.precio_compra, ps.precio_venta 
      FROM producto AS p      
    INNER JOIN producto_sucursal as ps on p.idproducto = ps.idproducto 
      INNER JOIN sunat_c03_unidad_medida AS sum ON p.idsunat_c03_unidad_medida = sum.idsunat_c03_unidad_medida
      INNER JOIN producto_categoria AS cat ON p.idcategoria = cat.idproducto_categoria
      INNER JOIN producto_marca AS mc ON p.idmarca = mc.idproducto_marca
      WHERE p.idproducto = '$id' ;";
    return ejecutarConsultaSimpleFila($sql);
  }

  public function eliminar($id)
  {
    $sql = "UPDATE producto SET estado_delete = 0
      WHERE idproducto = '$id'";
    return ejecutarConsulta($sql, 'U');
  }

  public function papelera($id)
  {
    $sql = "UPDATE producto SET estado = 0
      WHERE idproducto = '$id'";
    return ejecutarConsulta($sql, 'U');
  }

  // ══════════════════════════════════════  VALIDACION DE CODIGO  ══════════════════════════════════════
  public function validar_code_producto($id, $code)
  {
    $validar_id = empty($id) ? "" : "AND p.idproducto != '$id'";
    $sql = "SELECT p.idproducto, p.codigo_alterno, p.estado FROM producto AS p WHERE p.codigo_alterno = '$code' $validar_id;";
    $buscando =  ejecutarConsultaArray($sql);
    if ($buscando['status'] == false) {
      return $buscando;
    }

    if (empty($buscando['data'])) {
      return true;
    } else {
      return false;
    }
  }
  // ══════════════════════════════════════  S E L E C T 2 - P A R A   F O R M  ══════════════════════════════════════

  public function select_categoria()
  {
    $sql = "SELECT * FROM producto_categoria WHERE idproducto_categoria <> 2 AND estado = 1 AND estado_delete = 1;";
    return ejecutarConsultaArray($sql);
  }

  public function select_marca()
  {
    $sql = "SELECT * FROM producto_marca WHERE estado = 1 AND estado_delete = 1;";
    return ejecutarConsultaArray($sql);
  }

  public function select_u_medida()
  {
    $sql = "SELECT * FROM sunat_c03_unidad_medida WHERE estado = 1 AND estado_delete = 1;";
    return ejecutarConsultaArray($sql);
  }

  public function return_image(){

  }

  // ══════════════════════════════════════  S E L E C T 2 - PARA FILTROS ══════════════════════════════════════ 
  public function select2_filtro_categoria()
  {
    $sql = "SELECT c.*
      FROM producto as p
      INNER JOIN producto_categoria as c ON c.idproducto_categoria = p.idproducto_categoria
      WHERE c.idproducto_categoria <> 2 AND p.estado = '1' AND p.estado_delete = '1'
      GROUP BY c.idproducto_categoria ORDER BY c.idproducto_categoria ASC;";
    return ejecutarConsultaArray($sql);
  }

  public function select2_filtro_u_medida()
  {
    $sql = "SELECT um.*
      FROM producto as p
      INNER JOIN sunat_c03_unidad_medida as um ON um.idsunat_c03_unidad_medida = p.idsunat_c03_unidad_medida
      WHERE p.estado = '1' AND p.estado_delete = '1'
      GROUP BY um.idsunat_c03_unidad_medida ORDER BY um.idsunat_c03_unidad_medida ASC;";
    return ejecutarConsultaArray($sql);
  }

  public function select2_filtro_marca()
  {
    $sql = "SELECT m.*
      FROM producto as p
      INNER JOIN producto_marca as m ON m.idproducto_marca = p.idproducto_marca
      WHERE p.estado = '1' AND p.estado_delete = '1'
      GROUP BY m.idproducto_marca ORDER BY m.idproducto_marca ASC;";
    return ejecutarConsultaArray($sql);
  }

  public function select2_tipo_igv()
  {
    $sql = "SELECT idsunat_c07_afeccion_de_igv,nombre FROM sunat_c07_afeccion_de_igv where estado ='1' and estado_delete ='1';";
    return ejecutarConsultaArray($sql);
  }
}
