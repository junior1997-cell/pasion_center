<?php

  require "../config/Conexion_v2.php";

  class Facturacion
  {

    //Implementamos nuestro constructor
    public $id_usr_sesion; public $id_persona_sesion; public $id_trabajador_sesion;
    // public $id_empresa_sesion;   
    public function __construct( )
    {
      $this->id_usr_sesion        =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
      $this->id_persona_sesion    = isset($_SESSION['idpersona']) ? $_SESSION["idpersona"] : 0;
      $this->id_trabajador_sesion = isset($_SESSION['idpersona_trabajador']) ? $_SESSION["idpersona_trabajador"] : 0;
      // $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
    }

    public function listar_tabla_facturacion( $fecha_i, $fecha_f, $cliente, $comprobante, $estado_sunat ) {    

      $filtro_id_trabajador  = ''; $filtro_id_punto ='';
      $filtro_fecha = ""; $filtro_cliente = ""; $filtro_comprobante = ""; $filtro_estado_sunat = "";

      if ($_SESSION['user_cargo'] == 'VENDEDOR') {  $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";    } 
      if ($_SESSION['user_cargo'] == 'PUNTO DE COBRO') { $filtro_id_punto = "AND (v.user_created = '$this->id_usr_sesion' OR pc.idpersona_trabajador = '$this->id_trabajador_sesion')";  } 

      if ( !empty($fecha_i) && !empty($fecha_f) ) { $filtro_fecha = "AND DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') BETWEEN '$fecha_i' AND '$fecha_f'"; } 
      else if (!empty($fecha_i)) { $filtro_fecha = "AND DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') = '$fecha_i'"; }
      else if (!empty($fecha_f)) { $filtro_fecha = "AND DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') = '$fecha_f'"; }
      
      if ( empty($cliente) ) { } else {  $filtro_cliente = "AND v.idpersona_cliente = '$cliente'"; } 
      if ( empty($comprobante) ) { } else {  $filtro_comprobante = "AND v.idsunat_c01 = '$comprobante'"; } 
      if ( empty($estado_sunat) ) { } else {  $filtro_estado_sunat = "AND v.sunat_estado = '$estado_sunat'"; } 

      $sql = "SELECT v.*, LPAD(v.idventa, 5, '0') AS idventa_v2, CASE v.tipo_comprobante WHEN '07' THEN v.venta_total * -1 ELSE v.venta_total END AS venta_total_v2, 
      CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tp_comprobante_v2,
      DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format, 
      pc.nombre_razonsocial, pc.apellidos_nombrecomercial, pc.tipo_documento, 
      pc.numero_documento, pc.foto_perfil, tc.abreviatura as tp_comprobante_v1, pc.tipo_documento_abrev_nombre, v.estado,
      CASE 
        WHEN LENGTH(  pc.cliente_nombre_completo  ) <= 27 THEN  pc.cliente_nombre_completo 
        ELSE pc.cliente_nombre_completo
      END  AS cliente_nombre_recortado,
      pc.cliente_nombre_completo, pu.nombre_razonsocial as user_en_atencion, LPAD(v.user_created, 3, '0') AS user_created_v2
      FROM venta AS v
      INNER JOIN vw_cliente_all AS pc ON pc.idpersona_cliente = v.idpersona_cliente      
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01
      LEFT JOIN usuario as u ON u.idusuario = v.user_created
      LEFT JOIN persona as pu ON pu.idpersona = u.idpersona
      WHERE v.estado = 1 AND v.estado_delete = 1 AND v.tipo_comprobante in ( '01', '03', '07', '12') $filtro_id_trabajador $filtro_id_punto $filtro_cliente $filtro_comprobante $filtro_estado_sunat $filtro_fecha
      GROUP BY v.idventa
      ORDER BY v.fecha_emision DESC, p.nombre_razonsocial ASC;"; #return $sql;
      $venta = ejecutarConsulta($sql); if ($venta['status'] == false) {return $venta; }

      return $venta;
    }
    
    public function insertar(
      // DATOS TABLA venta
      $impuesto, $crear_y_emitir, $idsunat_c01 , $tipo_comprobante, $serie_comprobante, $idpersona_cliente, $observacion_documento,
      $metodo_pago, $total_recibido, $mp_monto, $total_vuelto, $usar_anticipo, $ua_monto_disponible, $ua_monto_usado,
      $mp_serie_comprobante,$mp_comprobante, $venta_subtotal, $tipo_gravada, $venta_descuento, $venta_igv, $venta_total,
      $nc_idventa, $nc_tipo_comprobante, $nc_serie_y_numero, $nc_motivo_anulacion, $tiempo_entrega, $validez_cotizacion,
      //DATOS TABLA venta DETALLE
      $idproducto, $pr_marca, $pr_categoria,$pr_nombre, $um_nombre, $um_abreviatura, $es_cobro, $periodo_pago, $cantidad, $precio_compra, $precio_sin_igv, $precio_igv, $precio_con_igv, $precio_venta_descuento, $descuento, $descuento_porcentaje, 
      $subtotal_producto, $subtotal_no_descuento    
    ){
      $tipo_v = ""; $cot_estado = ""; $fecha_actual_amd = date('Y-m-d');
      if ($tipo_comprobante == '100') {
        $tipo_v = "COTIZACIÓN";
        $cot_estado = "PENDIENTE";
      }else if ($tipo_comprobante == '12') {
        $tipo_v = "TICKET";
      }else if ($tipo_comprobante == '07') {
        $tipo_v = "NOTA DE CRÉDITO";         
        $metodo_pago= ""; $total_recibido= ""; $mp_monto= ""; $total_vuelto= ""; $mp_serie_comprobante = "";$mp_comprobante = "";
        $usar_anticipo= "NO"; $ua_monto_disponible= ""; $ua_monto_usado= "";        
      }else if ($tipo_comprobante == '03') {
        $tipo_v = "BOLETA";
      }else if ($tipo_comprobante == '01') {
        $tipo_v = "FACTURA";
      }

      $sql = "SELECT v.*, CONCAT(v.serie_comprobante, '-', v.numero_comprobante) as serie_y_numero_comprobante, 
      DATE_FORMAT(v.fecha_emision, '%d/%m/%Y %h:%i:%s %p') AS fecha_emision_format 
      FROM venta AS v 
      WHERE v.tipo_comprobante = '$tipo_comprobante' and ((v.sunat_error <> '' AND  v.sunat_error is not null  ) or (v.sunat_observacion <> '' AND  v.sunat_observacion is not null  ));";
      $buscando_error = ejecutarConsultaArray($sql); if ($buscando_error['status'] == false) {return $buscando_error; }

      $sql_periodo = "SELECT idperiodo_contable FROM periodo_contable WHERE estado = '1' AND estado_delete = '1' AND '$fecha_actual_amd' BETWEEN fecha_inicio AND fecha_fin;";
      $buscando_periodo = ejecutarConsultaSimpleFila($sql_periodo); if ($buscando_periodo['status'] == false) {return $buscando_periodo; }
      $idperiodo_contable = empty($buscando_periodo['data']) ? '' : (empty($buscando_periodo['data']['idperiodo_contable']) ? '' : $buscando_periodo['data']['idperiodo_contable'] ) ;
      // return $sql_periodo;
      if ( empty($idperiodo_contable) ) {  
        $retorno = array( 'status' => 'error_usuario', 'titulo' => 'No existe periodo!!', 'message' => ' No cierre el módulo. <br> No existe un periodo contable del mes: <b>'. nombre_mes(date('Y-m-d')).'-'.date('Y'). '</b>. '. ($_SESSION['user_cargo'] == 'ADMINISTRADOR' ? 'Configure el período contable en el módulo siguiente: <a href="periodo_facturacion.php" target="_blank" >Periodo contable</a>' : 'Solicite a su administrador que configure el período contable para el mes actual.'), 'user' =>  $_SESSION['user_nombre'], 'data' => $buscando_error['data'], 'id_tabla' => '' );
        return $retorno;
      }

      if ( empty( $buscando_error['data'] ) ) {
        $sql_1 = "INSERT INTO venta(idpersona_cliente, idperiodo_contable, iddocumento_relacionado, crear_enviar_sunat, idsunat_c01, tipo_comprobante, serie_comprobante,  impuesto, 
        venta_subtotal, venta_descuento, venta_igv, venta_total, metodo_pago, mp_serie_comprobante, mp_comprobante, mp_monto, venta_credito, vc_numero_operacion, 
        vc_fecha_proximo_pago, total_recibido, total_vuelto, usar_anticipo, ua_monto_disponible, ua_monto_usado, nc_motivo_nota, nc_tipo_comprobante, nc_serie_y_numero, cot_tiempo_entrega, cot_validez, cot_estado, observacion_documento) 
        VALUES ('$idpersona_cliente', '$idperiodo_contable', '$nc_idventa', '$crear_y_emitir', '$idsunat_c01', '$tipo_comprobante', '$serie_comprobante', '$impuesto', '$venta_subtotal', '$venta_descuento',
        '$venta_igv','$venta_total','$metodo_pago','$mp_serie_comprobante','$mp_comprobante','$mp_monto','','',CURRENT_TIMESTAMP, '$total_recibido', '$total_vuelto',
        '$usar_anticipo','$ua_monto_disponible','$ua_monto_usado', '$nc_motivo_anulacion', '$nc_tipo_comprobante', '$nc_serie_y_numero', '$tiempo_entrega', '$validez_cotizacion', '$cot_estado', '$observacion_documento')"; 
        $newdata = ejecutarConsulta_retornarID($sql_1, 'C'); if ($newdata['status'] == false) { return  $newdata;}
        $id = $newdata['data'];

        $i = 0;
        $detalle_new = "";
       
        if ( !empty($newdata['data']) ) {      
          while ($i < count($idproducto)) {

            $sql_2 = "INSERT INTO venta_detalle( idventa, idproducto, pr_nombre, pr_marca, pr_categoria, pr_unidad_medida, tipo, cantidad, precio_compra, precio_venta, precio_venta_descuento, descuento, descuento_porcentaje, subtotal, subtotal_no_descuento, um_nombre, um_abreviatura, es_cobro, periodo_pago)
            VALUES ('$id', '$idproducto[$i]', '$pr_nombre[$i]', '$pr_marca[$i]', '$pr_categoria[$i]', '$um_nombre[$i]', '$tipo_v', '$cantidad[$i]', '$precio_compra[$i]',  '$precio_con_igv[$i]', '$precio_venta_descuento[$i]', '$descuento[$i]', '$descuento_porcentaje[$i]', '$subtotal_producto[$i]', '$subtotal_no_descuento[$i]', '$um_nombre[$i]', '$um_abreviatura[$i]','$es_cobro[$i]', '$periodo_pago[$i]');";
            $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}          
            $id_d = $detalle_new['data'];
            $i = $i + 1;
          }
        }
        return $newdata;
      } else {

        $info_repetida = ''; 

        foreach ($buscando_error['data'] as $key => $val) {
          $info_repetida .= '<li class="text-left font-size-13px">
            <span class="font-size-13px text-danger"><b>Fecha: </b>'.$val['fecha_emision_format'].'</span><br>
            <span class="font-size-13px text-danger"><b>Comprobante: </b>'.$val['serie_y_numero_comprobante'].'</span><br>
            <span class="font-size-13px text-danger"><b>Total: </b>'.$val['venta_total'].'</span><br>
            <span class="font-size-13px text-danger"><b>Error: </b>'.$val['sunat_error'].'</span><br>
            <span class="font-size-13px text-danger"><b>Observación: </b>'.$val['sunat_observacion'].'</span><br>            
            <hr class="m-t-2px m-b-2px">
          </li>'; 
        }

        $retorno = array( 'status' => 'error_usuario', 'titulo' => 'Errores anteriores!!', 'message' => 'No se podran generar comprobantes hasta corregir los errores anteriores a este: '. $info_repetida, 'user' =>  $_SESSION['user_nombre'], 'data' => $buscando_error['data'], 'id_tabla' => '' );
        return $retorno;
      }      
    }

    public function editar( $idventa, $idpersona_cliente,  $tipo_comprobante, $serie, $impuesto, $descripcion, $venta_subtotal, $tipo_gravada, $venta_igv, $venta_total, $fecha_venta, $img_comprob,        
    $idproducto, $unidad_medida, $cantidad, $precio_sin_igv, $precio_igv, $precio_con_igv,  $descuento, $subtotal_producto) {

      $sql_1 = "UPDATE venta SET idpersona_cliente = '$idpersona_cliente', fecha_venta = '$fecha_venta', tipo_comprobante = '$tipo_comprobante', serie_comprobante = '$serie', 
      val_igv = '$impuesto', descripcion = '$descripcion', subtotal = '$venta_subtotal', igv = '$venta_igv', total = '$venta_total', comprobante = '$img_comprob'
      WHERE idventa = '$idventa'";
      $result_sql_1 = ejecutarConsulta($sql_1, 'U');if ($result_sql_1['status'] == false) { return $result_sql_1; }

      // Eliminamos los productos
      $sql_del = "DELETE FROM venta_detalle WHERE idventa = '$idventa'";
      ejecutarConsulta($sql_del);

      // Creamos los productos
      foreach ($idproducto as $ii => $producto) {
        $sql_2 = "INSERT INTO venta_detalle(idproducto, idventa, cantidad, precio_sin_igv, igv, precio_con_igv, descuento, subtotal)
        VALUES ('$idproducto[$ii]', '$idventa', '$cantidad[$ii]', '$precio_sin_igv[$ii]', '$precio_igv[$ii]', '$precio_con_igv[$ii]', '$descuento[$ii]', '$subtotal_producto[$ii]');";
        $detalle_new =  ejecutarConsulta_retornarID($sql_2, 'C'); if ($detalle_new['status'] == false) { return  $detalle_new;}        
      }  
      
      return array('status' => true, 'message' => 'Datos actualizados correctamente.');
    }   

    public function actualizar_respuesta_sunat( $idventa, $sunat_estado , $sunat_observacion, $sunat_code, $sunat_hash, $sunat_mensaje, $sunat_error) {
      
      $sql_1 = "UPDATE venta SET sunat_estado='$sunat_estado',sunat_observacion='$sunat_observacion',sunat_code='$sunat_code',
      sunat_hash='$sunat_hash',sunat_mensaje='$sunat_mensaje', sunat_error = '$sunat_error' WHERE idventa = '$idventa';";
      return ejecutarConsulta($sql_1);     

    } 

    public function actualizar_doc_anulado_x_nota_credito( $idventa) {
      
      $sql_1 = "UPDATE venta SET sunat_estado='ANULADO' WHERE idventa = '$idventa';";
      return ejecutarConsulta($sql_1);     

    } 


    public function mostrar_venta($id){
      $sql = "SELECT * FROM venta WHERE idventa = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function mostrar_cliente($id){
      $sql = "SELECT p.*, 
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo,  pc.idcentro_poblado, pc.fecha_afiliacion, pc.ip_personal
      FROM persona_cliente as pc
      INNER JOIN persona as p ON p.idpersona = pc.idpersona
      WHERE pc.idpersona_cliente = '$id'";
      return ejecutarConsultaSimpleFila($sql);
    }

    public function mostrar_detalle_venta($idventa){

      $sql_1 = "SELECT v.*, LPAD(v.idventa, 5, '0') AS idventa_v2, CONCAT(v.serie_comprobante, '-', v.numero_comprobante) as serie_y_numero_comprobante, DATE_FORMAT(v.fecha_emision, '%d/%m/%Y %h:%i:%s %p') AS fecha_emision_format, 
      DATE_FORMAT(v.fecha_emision, '%h:%i:%s %p') AS fecha_emision_hora12, DATE_FORMAT(v.fecha_emision, '%d/%m/%Y') AS fecha_emision_dmy,
      DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format, LEFT(v.periodo_pago_month, 3) as periodo_pago_month_v2,
      v.estado, pc.idpersona, pc.idpersona_cliente, pc.nombre_razonsocial, pc.apellidos_nombrecomercial, 
      pc.cliente_nombre_completo,  pc.landing_user,
      pc.tipo_documento, pc.numero_documento, pc.direccion, pc.celular, pc.correo,
      tc.abreviatura as nombre_comprobante, pc.tipo_documento_abrev_nombre,
      pu.nombre_razonsocial as user_en_atencion, IFNULL(cnc.nombre, '') as nc_nombre_motivo
      FROM venta AS v
      INNER JOIN vw_cliente_all AS pc ON pc.idpersona_cliente = v.idpersona_cliente      
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01
      LEFT JOIN sunat_c09_codigo_nota_credito AS cnc ON cnc.codigo = v.nc_motivo_nota
      LEFT JOIN usuario as u ON u.idusuario = v.user_created
      LEFT JOIN persona as pu ON pu.idpersona = u.idpersona
      WHERE v.idventa = '$idventa'";
      $venta = ejecutarConsultaSimpleFila($sql_1); if ($venta['status'] == false) {return $venta; }


      $sql_2 = "SELECT vc.*,  p.idproducto, p.idsunat_c03_unidad_medida, p.idproducto_categoria, p.idproducto_marca, 
      p.nombre as nombre_producto, p.codigo, p.codigo_alterno, p.imagen, p.tipo as tipo_producto, p.nombre_um, p.abreviatura_um, p.nombre_categoria, p.nombre_marca
      FROM venta_detalle AS vc
      INNER JOIN vw_producto_all AS p ON p.idproducto = vc.idproducto;
      WHERE vc.idventa = '$idventa';";
      $detalle = ejecutarConsultaArray($sql_2); if ($detalle['status'] == false) {return $detalle; }

      return $datos = ['status' => true, 'message' => 'Todo ok', 'data' => ['venta' => $venta['data'], 'detalle' => $detalle['data']]];

    }

    public function eliminar($id){
      $sql = "UPDATE venta SET sunat_estado = 'ANULADO', estado_delete = '0' WHERE idventa = '$id'";
      return ejecutarConsulta($sql, 'D');
    }

    public function papelera($id){
      $sql = "UPDATE venta SET sunat_estado = 'ANULADO', estado = '0'  WHERE idventa = '$id'";
      return ejecutarConsulta($sql, 'T');
    }    

    public function listar_tabla_producto($tipo_producto){
      $sql = "SELECT p.*
      FROM vw_producto_all AS p      
      WHERE p.tipo = '$tipo_producto'  AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsulta($sql);
    }

    public function mostrar_producto($idproducto){
      $sql = "SELECT p.*
      FROM vw_producto_all AS p      
      WHERE p.idproducto = '$idproducto'  AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsultaSimpleFila($sql);
    }

    Public function mini_reporte($periodo, $filtro_trabajador){

      $meses_espanol = array( 1 => "Ene", 2 => "Feb", 3 => "Mar", 4 => "Abr", 5 => "May", 6 => "Jun", 7 => "Jul", 8 => "Ago", 9 => "Sep", 10 => "Oct", 11 => "Nov", 12 => "Dic" );

      $filtro_id_trabajador  = ''; $filtro_id_user  = '';
      if ($_SESSION['user_cargo'] == 'VENDEDOR') { $filtro_id_trabajador = "AND v.user_created = '$this->id_trabajador_sesion'";  } else { $filtro_id_trabajador = "AND v.user_created = '$filtro_trabajador'"; }
      //if ($_SESSION['user_cargo'] == 'PUNTO DE COBRO') { $filtro_id_user = "AND (v.user_created = '$this->id_usr_sesion' OR pc.idpersona_trabajador = '$this->id_trabajador_sesion')";  } 

      $sql_01 = "WITH ventas_mes_actual AS (
        SELECT vd.idproducto, SUM(vd.cantidad) AS total_vendido, AVG(vd.subtotal / vd.cantidad) AS precio_promedio
        FROM venta v
        JOIN venta_detalle vd ON v.idventa = vd.idventa
        WHERE v.fecha_emision >= DATE_FORMAT(DATE('$periodo-01'), '%Y-%m-01') and 
        sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.estado = 1 AND v.estado_delete = 1 AND v.tipo_comprobante in('01', '03','12') $filtro_id_trabajador
        GROUP BY vd.idproducto
      ),
      ventas_mes_anterior AS (
        SELECT vd.idproducto, SUM(vd.cantidad) AS total_vendido, AVG(vd.subtotal / vd.cantidad) AS precio_promedio
        FROM venta v
        JOIN venta_detalle vd ON v.idventa = vd.idventa
        WHERE v.fecha_emision >= DATE_FORMAT(DATE_SUB(DATE('$periodo-01'), INTERVAL 1 MONTH), '%Y-%m-01') AND v.fecha_emision < DATE_FORMAT(DATE('$periodo-01'), '%Y-%m-01') and 
        sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.estado = 1 AND v.estado_delete = 1 AND v.tipo_comprobante in('01', '03','12') $filtro_id_trabajador
        GROUP BY vd.idproducto
      )
      SELECT p.idproducto, p.nombre as nombre_producto, p.imagen, p.nombre_categoria, p.nombre_marca, p.nombre_um,
          ROUND( COALESCE(vma.total_vendido, 0), 2) AS total_vendido_mes_actual,
          ROUND( COALESCE(vma.precio_promedio, p.precio_venta), 2) AS precio_promedio_mes_actual,
          ROUND( COALESCE(vmp.total_vendido, 0), 2) AS total_vendido_mes_anterior,
          ROUND( COALESCE(vmp.precio_promedio, p.precio_venta), 2) AS precio_promedio_mes_anterior,
          CASE 
              WHEN COALESCE(vmp.total_vendido, 0) = 0 THEN 0.00
              ELSE ROUND( ((COALESCE(vma.total_vendido, 0) - COALESCE(vmp.total_vendido, 0)) * 100.0 / COALESCE(vmp.total_vendido, 1)), 2)
          END AS porcentaje_incremento
      FROM vw_producto_all p 
      LEFT JOIN ventas_mes_actual vma ON p.idproducto = vma.idproducto
      LEFT JOIN ventas_mes_anterior vmp ON p.idproducto = vmp.idproducto
      ORDER BY total_vendido_mes_actual DESC LIMIT 5;";
      $producto = ejecutarConsultaArray($sql_01); if ($producto['status'] == false) {return $producto; }      

      $data_line = []; $mes_nombre = []; $date_now = date("Y-m-d");  $fecha_actual = date("Y-m-d", strtotime("-5 months", strtotime($date_now)));
      for ($i=1; $i <=6 ; $i++) { 
        $nro_mes = floatval( date("m", strtotime($fecha_actual)) ); 
        array_push($mes_nombre, $meses_espanol[$nro_mes] ); 
        $fecha_actual= date("Y-m-d", strtotime("1 months", strtotime($fecha_actual))); 
      }      

      foreach ($producto['data'] as $key => $val) {
        $fecha_actual = date("Y-m-d", strtotime("-5 months", strtotime($date_now))); // reiniciamos la fecha actual, para cada producto
        $idproducto = $val['idproducto']; // obtenemos el id en variable, para mejor uso en sql
        $mes_factura = [];
        for ($i=1; $i <=6 ; $i++) { 
          $nro_mes = floatval( date("m", strtotime($fecha_actual)) ); // convertimos la fecha a numeric
          $sql_mes = "SELECT MONTHNAME(v.fecha_emision) AS fecha_emision , COALESCE(SUM(vd.cantidad), 0) AS cantidad_total 
          FROM venta as v 
          INNER JOIN venta_detalle as vd on vd.idventa = v.idventa
          WHERE MONTH(v.fecha_emision) = '$nro_mes' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in ('01', '03', '12') 
          AND v.estado = '1' AND v.estado_delete = '1' and vd.idproducto = '$idproducto' $filtro_id_trabajador ;";
          $mes_f = ejecutarConsultaSimpleFila($sql_mes); if ($mes_f['status'] == false) {return $mes_f; }

          array_push($mes_factura, floatval($mes_f['data']['cantidad_total']) ); // añadimos los resultados
          $fecha_actual= date("Y-m-d", strtotime("1 months", strtotime($fecha_actual))); // aumentamos 1 mes
        } 
        $data_line[] = [
          'name' => $val['nombre_producto'],
          'data' => $mes_factura
        ];
      }           

      return ['status' => true, 'message' =>'todo okey', 
        'data'=>[
          'mes_nombre'    => $mes_nombre,
          'data_line'     => $data_line ,
          'data_producto' => $producto['data']
        ]
      ];

    }

    Public function mini_reporte_v2($periodo,  $trabajador){ 
      $filtro_periodo = ""; $filtro_trabajador_1 = ""; $filtro_trabajador_2 = "";    
      
      if ( empty($periodo) )    { } else { $filtro_periodo = "AND DATE_FORMAT( vd.periodo_pago_format, '%Y-%m') = '$periodo'"; } 
      if ( empty($trabajador) ) { } else { $filtro_trabajador_1 = "WHERE pc.idpersona_trabajador = '$trabajador'"; } 
      if ( empty($trabajador) ) { } else { $filtro_trabajador_2 = "AND pc.idpersona_trabajador = '$trabajador'"; } 

      $sql = "SELECT pco.idcentro_poblado, pco.centro_poblado, ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) as avance,
       COALESCE(co.cant_cobrado,0) as cant_cobrado,  pco.cant_cliente as cant_total
      FROM 
      (SELECT cp.idcentro_poblado, cp.nombre as centro_poblado, COUNT(pc.idpersona_cliente) as cant_cliente
      FROM persona_cliente as pc       
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      $filtro_trabajador_1
      GROUP BY cp.idcentro_poblado
      order by COUNT(pc.idpersona_cliente) DESC) AS pco 

      LEFT JOIN

      (SELECT cp.idcentro_poblado, cp.nombre as centro_poblado, COUNT(v.idventa) as cant_cobrado 
      FROM venta as v
      INNER JOIN venta_detalle as vd ON vd.idventa = v.idventa
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      WHERE v.estado = 1 AND v.estado_delete = 1 and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) 
      $filtro_periodo $filtro_trabajador_2
      GROUP BY cp.idcentro_poblado
      order by COUNT(v.idventa) DESC) as co ON pco.idcentro_poblado = co.idcentro_poblado
      order by ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) DESC ;"; #return $sql;
      $centro_poblado = ejecutarConsultaArray($sql); if ($centro_poblado['status'] == false) {return $centro_poblado; }

      $sql = "SELECT ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) as avance,
      COALESCE(co.cant_cobrado,0) as cant_cobrado,  pco.cant_cliente as cant_total
      FROM 

      (SELECT pc.idpersona_trabajador, COUNT(pc.idpersona_cliente) as cant_cliente
      FROM persona_cliente as pc       
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      $filtro_trabajador_1
      GROUP BY pc.idpersona_trabajador
      order by COUNT(pc.idpersona_cliente) DESC) AS pco 

      LEFT JOIN

      (SELECT pc.idpersona_trabajador, COUNT(v.idventa) as cant_cobrado 
      FROM venta as v
      INNER JOIN venta_detalle as vd ON vd.idventa = v.idventa
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN centro_poblado as cp ON cp.idcentro_poblado = pc.idcentro_poblado
      WHERE v.estado = 1 AND v.estado_delete = 1 and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) $filtro_periodo $filtro_trabajador_2
      GROUP BY pc.idpersona_trabajador
      order by COUNT(v.idventa) DESC) as co ON pco.idpersona_trabajador = co.idpersona_trabajador
      order by ROUND( COALESCE((( co.cant_cobrado /  pco.cant_cliente) * 100), 0) , 2) DESC ;"; #return $sql;
      $total = ejecutarConsultaSimpleFila($sql); if ($total['status'] == false) {return $total; }

      return ['status' => true, 'message' =>'todo okey', 
        'data'=>[
          'total'  => $total['data'],
          'centro_poblado'    => $centro_poblado['data'],
        ]
      ];
    }

    public function listar_producto_x_codigo($codigo){
      $sql = "SELECT p.*
      FROM vw_producto_all AS p
      WHERE (p.codigo = '$codigo' OR p.codigo_alterno = '$codigo' ) AND p.estado = 1 AND p.estado_delete = 1;";
      return ejecutarConsultaSimpleFila($sql);
      
    }

    // ══════════════════════════════════════ C O M P R O B A N T E ══════════════════════════════════════

    public function datos_empresa(){
      $sql = "SELECT * FROM empresa;";
      return ejecutarConsultaSimpleFila($sql);      
    }

    // ══════════════════════════════════════ U S A R   A N T I C I P O ══════════════════════════════════════
    public function mostrar_anticipos($idcliente){
      $sql = "SELECT  pc.idpersona_cliente, p.nombre_razonsocial AS nombres,  p.apellidos_nombrecomercial AS apellidos,
        (
          IFNULL( (SELECT  SUM( CASE  WHEN ac.tipo = 'EGRESO' THEN ac.total * -1 ELSE ac.total END )
          FROM anticipo_cliente AS ac
          WHERE ac.idpersona_cliente = pc.idpersona_cliente
          GROUP BY ac.idpersona_cliente) , 0)
        ) AS total_anticipo
      FROM persona_cliente AS pc  
      INNER JOIN persona AS p ON pc.idpersona = p.idpersona
      WHERE pc.idpersona_cliente = '$idcliente';";
      return ejecutarConsultaSimpleFila($sql);
      
    }

    // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
    public function select2_cliente(){
      $filtro_id_trabajador  = '';
      if ($_SESSION['user_cargo'] == 'VENDEDOR') {
        $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
      } 
      $sql = "SELECT * FROM vw_cliente_all AS vw_c
      WHERE vw_c.estado_pc = '1' and vw_c.estado_delete_pc = '1' and vw_c.estado_p = '1' and vw_c.estado_delete_p = '1' and vw_c.idpersona > 2 $filtro_id_trabajador 
      ORDER BY vw_c.cliente_nombre_completo ASC;"; 
      return ejecutarConsultaArray($sql);
    }

    public function select2_comprobantes_anular($tipo_comprobante){
      $filtro_id_trabajador  = '';
      if ($_SESSION['user_cargo'] == 'VENDEDOR') {
        $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
      } 
      $sql = "SELECT v.idventa, v.tipo_comprobante, v.serie_comprobante, v.numero_comprobante,  
      CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS nombre_tipo_comprobante_v2,
      CASE
        WHEN TIMESTAMPDIFF(DAY, v.fecha_emision, CURDATE()) = 1 THEN 'hace 1 día'
        WHEN TIMESTAMPDIFF(DAY, v.fecha_emision, CURDATE()) > 1 THEN CONCAT('hace ', TIMESTAMPDIFF(DAY, v.fecha_emision, CURDATE()), ' días')
        ELSE 'hoy'
      END AS fecha_emision_dif
      FROM venta as v
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.codigo = v.tipo_comprobante
      WHERE v.tipo_comprobante = '$tipo_comprobante' AND v.sunat_estado ='ACEPTADA' AND  v.fecha_emision >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)  $filtro_id_trabajador 
      ORDER BY CONVERT(v.numero_comprobante, SIGNED) DESC;";  #return $sql;
      return ejecutarConsultaArray($sql); 
    }

    public function select2_series_comprobante($codigo, $nc_tp){

      $filtro_nc = "";

      if ($codigo == '07') {        // Acciones solo si es: Nota de Credito 
      
        if ($nc_tp == '01') {
          $filtro_nc = "AND stp.abreviatura LIKE '%FACTURA'";
        }else if ($nc_tp == '03') {
          $filtro_nc = "AND stp.abreviatura LIKE '%BOLETA'";
        }
      }

      $sql = "SELECT stp.abreviatura,  stp.serie
      FROM sunat_usuario_comprobante as suc
      INNER JOIN sunat_c01_tipo_comprobante as stp ON stp.idtipo_comprobante = suc.idtipo_comprobante
      WHERE stp.codigo = '$codigo'  $filtro_nc AND suc.idusuario = '$this->id_usr_sesion';";
      return ejecutarConsultaArray($sql);      
    }

    public function select2_codigo_x_anulacion_comprobante(){
      $sql = "SELECT idsunat_c09_codigo_nota_credito as idsunat_c09, codigo, nombre, estado FROM sunat_c09_codigo_nota_credito;";
      return ejecutarConsultaArray($sql);      
    }

    public function select2_filtro_tipo_comprobante($tipos){
      $sql="SELECT idtipo_comprobante, codigo, abreviatura AS tipo_comprobante, serie,
      CASE idtipo_comprobante WHEN '3' THEN 'BOLETA' WHEN '7' THEN 'NOTA CRED. FACTURA' WHEN '8' THEN 'NOTA CRED. BOLETA' ELSE abreviatura END AS nombre_tipo_comprobante_v2
      FROM sunat_c01_tipo_comprobante WHERE codigo in ($tipos) ;";
      return ejecutarConsultaArray($sql);
    }

    public function select2_filtro_cliente(){
      $filtro_id_trabajador  = '';
      if ($_SESSION['user_cargo'] == 'VENDEDOR') {
        $filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
      } 
      $sql="SELECT p.idpersona, pc.idpersona_cliente, 
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo, p.numero_documento, sc06.abreviatura as nombre_tipo_documento,
      count(v.idventa) as cantidad
      FROM venta as v 
      INNER JOIN persona_cliente as pc ON pc.idpersona_cliente = v.idpersona_cliente
      INNER JOIN persona as p ON p.idpersona = pc.idpersona
      INNER JOIN sunat_c06_doc_identidad as sc06 on p.tipo_documento=sc06.code_sunat
      WHERE v.estado = '1' AND v.estado_delete = '1' $filtro_id_trabajador
      GROUP BY p.idpersona, pc.idpersona_cliente, p.numero_documento, sc06.abreviatura ORDER BY  count(v.idventa) desc, p.nombre_razonsocial asc ;";
      return ejecutarConsultaArray($sql);
    }

    public function select2_filtro_trabajador(){
      $filtro_id_trabajador  = '';
      if ($_SESSION['user_cargo'] == 'VENDEDOR') {
        $filtro_id_trabajador = "AND pt.idpersona_trabajador = '$this->id_trabajador_sesion'";
      } 
      $sql="SELECT p.*,
      CASE 
        WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
        WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
        ELSE '-'
      END AS cliente_nombre_completo,
      pt.idpersona_trabajador, pt.ruc, pt.usuario_sol, pt.clave_sol, pt.sueldo_mensual, pt.sueldo_diario, t.nombre as tipo_persona, 
      c.nombre as cargo_trabajador, s_c06.abreviatura as tipo_documento_abrev_nombre      
      FROM  persona as p
      inner join persona_trabajador as pt on pt.idpersona = p.idpersona
      INNER JOIN tipo_persona as t ON t.idtipo_persona = p.idtipo_persona
      INNER JOIN cargo_trabajador as c ON c.idcargo_trabajador = p.idcargo_trabajador
      INNER JOIN sunat_c06_doc_identidad as s_c06 ON s_c06.code_sunat = p.tipo_documento
      WHERE p.estado = '1' AND p.estado_delete = '1' $filtro_id_trabajador
      ORDER BY p.nombre_razonsocial desc, p.nombre_razonsocial asc ;";
      return ejecutarConsultaArray($sql);
    }

    public function select2_banco(){
     
      $sql="SELECT * FROM bancos WHERE idbancos <> 1 and estado = '1' AND estado_delete = '1';";
      return ejecutarConsultaArray($sql);
    }

    public function select2_periodo_contable(){      
     
      $sql="SELECT pco.periodo, pco.idperiodo_contable, pco.periodo_year, pco.periodo_month, count(v.idventa) as cant_comprobante 
      FROM periodo_contable as pco
      LEFT JOIN venta as v ON v.idperiodo_contable = pco.idperiodo_contable  and v.estado = '1' and v.estado_delete = '1' and v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante <> '100'
      WHERE pco.estado = '1' and pco.estado_delete = '1'
      GROUP BY pco.idperiodo_contable, pco.periodo_year, periodo_month
      ORDER BY periodo DESC";
      return ejecutarConsultaArray($sql);
    }
  }
?>