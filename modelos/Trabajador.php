<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

class Trabajador
{
	//Implementamos nuestro constructor
  public $id_usr_sesion; public $id_empresa_sesion;
  //Implementamos nuestro constructor
  public function __construct( $id_usr_sesion = 0, $id_empresa_sesion = 0 )
  {
    $this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
		$this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
  }

	//Implementamos un método para insertar registros
	public function insertar( $tipo_persona_sunat, $idtipo_persona, $tipo_documento, $numero_documento, $idcargo_trabajador, 
	$nombre_razonsocial, $apellidos_nombrecomercial, $correo, $celular, $fecha_nacimiento,  $ruc, $usuario_sol, $clave_sol, $direccion, $distrito, 
	$departamento, $provincia, $ubigeo, $sueldo_mensual, $sueldo_diario, $idbanco, $cuenta_bancaria, $cci, $titular_cuenta, $img_perfil)	{

		$sql_0 = "SELECT p.*, sdi.nombre as nombre_tipo_documento, pt.sueldo_mensual, c.nombre as cargo
		FROM persona AS p
		INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
		INNER JOIN cargo_trabajador as c ON c.idcargo_trabajador = p.idcargo_trabajador
		INNER JOIN persona_trabajador as pt ON pt.idpersona = p.idpersona
		WHERE p.tipo_documento = '$tipo_documento' AND p.numero_documento = '$numero_documento';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql = "INSERT INTO persona( idtipo_persona, idbancos, idcargo_trabajador, tipo_persona_sunat, nombre_razonsocial, apellidos_nombrecomercial, 
			tipo_documento, numero_documento, fecha_nacimiento, celular, direccion, departamento, provincia, distrito, cod_ubigeo, correo, 
			cuenta_bancaria, cci, titular_cuenta, foto_perfil ) VALUES 
			('$idtipo_persona', '$idbanco', '$idcargo_trabajador', '$tipo_persona_sunat', '$nombre_razonsocial', '$apellidos_nombrecomercial', '$tipo_documento', '$numero_documento',
			'$fecha_nacimiento', '$celular', '$direccion', '$departamento', '$provincia', '$distrito', '$ubigeo', '$correo', '$cuenta_bancaria','$cci','$titular_cuenta',	'$img_perfil')";
			$id_new = ejecutarConsulta_retornarID($sql, 'C');	if ($id_new['status'] == false) {  return $id_new; } 		

			$id = $id_new['data'];

			$sql_detalle = "INSERT INTO persona_trabajador( idpersona, ruc, usuario_sol, clave_sol, sueldo_mensual, sueldo_diario) VALUES 
			('$id', '$ruc', '$usuario_sol', '$clave_sol', '$sueldo_mensual', '$sueldo_diario')";
			$usr_permiso = ejecutarConsulta($sql_detalle, 'C'); if ($usr_permiso['status'] == false) {  return $usr_permiso; }		

			return $id_new;
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>'.$value['nombre_tipo_documento'].': </b>'.$value['numero_documento'].'</span><br>
					<b>Nombre: </b>'.$value['nombre_razonsocial'].' '.$value['apellidos_nombrecomercial'].'<br>
					<b>Cargo: </b>'.$value['cargo'].'<br>
					<b>Sueldo: </b>'.$value['sueldo_mensual'].'<br>
					<b>Fecha Nac.: </b>'.$value['fecha_nacimiento'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}			
	}

	//Implementamos un método para editar registros
	public function editar($idpersona, $tipo_persona_sunat, $idtipo_persona,  $idpersona_trabajador, $tipo_documento, $numero_documento, $idcargo_trabajador, 
	$nombre_razonsocial, $apellidos_nombrecomercial, $correo, $celular, $fecha_nacimiento, $ruc, $usuario_sol, $clave_sol, $direccion, $distrito, 
	$departamento, $provincia, $ubigeo, $sueldo_mensual, $sueldo_diario, $idbanco, $cuenta_bancaria, $cci, $titular_cuenta, $img_perfil) {

		$sql_0 = "SELECT p.*, sdi.nombre as nombre_tipo_documento, pt.sueldo_mensual, c.nombre as cargo
		FROM persona AS p
		INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
		INNER JOIN cargo_trabajador as c ON c.idcargo_trabajador = p.idcargo_trabajador
		INNER JOIN persona_trabajador as pt ON pt.idpersona = p.idpersona
		WHERE p.tipo_documento = '$tipo_documento' AND p.numero_documento = '$numero_documento' AND p.idpersona <> '$idpersona';";
    $existe = ejecutarConsultaArray($sql_0); if ($existe['status'] == false) { return $existe;}
      
    if ( empty($existe['data']) ) {
			$sql = "UPDATE persona SET idtipo_persona='$idtipo_persona', idbancos='$idbanco', idcargo_trabajador='$idcargo_trabajador',
			tipo_persona_sunat='$tipo_persona_sunat', nombre_razonsocial='$nombre_razonsocial', apellidos_nombrecomercial='$apellidos_nombrecomercial',
			tipo_documento='$tipo_documento',	numero_documento='$numero_documento',fecha_nacimiento='$fecha_nacimiento',celular='$celular',direccion='$direccion',
			departamento='$departamento',	provincia='$provincia', distrito='$distrito', cod_ubigeo='$ubigeo', correo='$correo', cuenta_bancaria='$cuenta_bancaria', cci='$cci',
			titular_cuenta='$titular_cuenta', foto_perfil='$img_perfil' 
			WHERE idpersona = '$idpersona'";
			$edit_user = ejecutarConsulta($sql, 'U'); if ($edit_user['status'] == false) {  return $edit_user; }

			$sql_detalle = "UPDATE persona_trabajador SET idpersona='$idpersona',ruc='$ruc',usuario_sol='$usuario_sol',
			clave_sol='$clave_sol',sueldo_mensual='$sueldo_mensual',sueldo_diario='$sueldo_diario' WHERE idpersona_trabajador = '$idpersona_trabajador'";
			$usr_permiso = ejecutarConsulta($sql_detalle, 'U'); if ($usr_permiso['status'] == false) {  return $usr_permiso; }

			return $edit_user;	
		} else {
			$info_repetida = ''; 

			foreach ($existe['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>'.$value['nombre_tipo_documento'].': </b>'.$value['numero_documento'].'</span><br>
					<b>Nombre: </b>'.$value['nombre_razonsocial'].' '.$value['apellidos_nombrecomercial'].'<br>
					<b>Cargo: </b>'.$value['cargo'].'<br>
					<b>Sueldo: </b>'.$value['sueldo_mensual'].'<br>
					<b>Fecha Nac.: </b>'.$value['fecha_nacimiento'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}			
	}

	public function eliminar($idtrabajador, $idpersona) {
		$sql = "UPDATE persona set estado_delete='0' where idpersona='$idpersona'";	ejecutarConsulta($sql);
		$sql = "UPDATE persona_trabajador set estado_delete='0' where idpersona_trabajador='$idtrabajador'";
		return ejecutarConsulta($sql);
	}

	public function papelera($idtrabajador, $idpersona) {
		$sql = "UPDATE persona set estado='0' where idpersona='$idpersona'";	ejecutarConsulta($sql);
		$sql = "UPDATE persona_trabajador set estado='0' where idpersona_trabajador='$idtrabajador'";
		return ejecutarConsulta($sql);
	}

	public function activar($idtrabajador, $idpersona)	{
		$sql = "UPDATE persona set estado='0' where idpersona='$idpersona'";	ejecutarConsulta($sql);
		$sql = "UPDATE persona_trabajador set estado='1' where idpersona_trabajador='$idtrabajador'";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar_trabajdor($idpersona)	{
		$sql = "SELECT p.*, pt.idpersona_trabajador, pt.ruc, pt.usuario_sol, pt.clave_sol, pt.sueldo_mensual, pt.sueldo_diario, t.nombre as tipo_persona, c.nombre as cargo_trabajador, 
		sdi.abreviatura as tipo_documento, sdi.code_sunat, pt.idpersona_trabajador		
		FROM  persona as p
		inner join persona_trabajador as pt on pt.idpersona = p.idpersona
		INNER JOIN tipo_persona as t ON t.idtipo_persona = p.idtipo_persona
		INNER JOIN cargo_trabajador as c ON c.idcargo_trabajador = p.idcargo_trabajador
		INNER JOIN sunat_c06_doc_identidad as sdi ON sdi.code_sunat = p.tipo_documento
		WHERE p.idpersona='$idpersona' AND p.estado = '1' AND p.estado_delete = '1';";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function listar_tabla_principal()	{
		$sql = "SELECT p.*, pt.idpersona_trabajador, pt.ruc, pt.usuario_sol, pt.clave_sol, pt.sueldo_mensual, pt.sueldo_diario, t.nombre as tipo_persona, 
		c.nombre as cargo_trabajador, s_c06.abreviatura as tipo_documento, 
		0 AS cant_cliente
		FROM  persona as p
		inner join persona_trabajador as pt on pt.idpersona = p.idpersona
		INNER JOIN tipo_persona as t ON t.idtipo_persona = p.idtipo_persona
		INNER JOIN cargo_trabajador as c ON c.idcargo_trabajador = p.idcargo_trabajador
		INNER JOIN sunat_c06_doc_identidad as s_c06 ON s_c06.code_sunat = p.tipo_documento
		WHERE p.estado = '1' AND p.estado_delete = '1';";
		return ejecutarConsulta($sql);
	}
	//Implementar un método para listar los registros
	public function clientes_x_trabajador($id)	{
		$sql = "SELECT v.*, LPAD(v.idventa, 5, '0') AS idventa_v2, 
		CASE v.tipo_comprobante WHEN '07' THEN v.venta_total * -1 ELSE v.venta_total END AS venta_total_v2, 
		CASE v.tipo_comprobante WHEN '03' THEN 'BOLETA' WHEN '07' THEN 'NOTA CRED.' ELSE tc.abreviatura END AS tp_comprobante_v2,
		DATE_FORMAT(v.fecha_emision, '%Y-%m-%d') as fecha_emision_format, vw_c.*
		FROM vw_cliente_all as vw_c
		INNER JOIN venta as v on v.idpersona_cliente = vw_c.idpersona_cliente
		INNER JOIN sunat_c01_tipo_comprobante AS tc ON tc.idtipo_comprobante = v.idsunat_c01
		WHERE v.estado = '1' AND v.estado_delete = '1' and v.sunat_estado = 'ACEPTADA' and v.user_created = '$id'  ORDER BY vw_c.idpersona_cliente DESC";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function perfil_trabajador($id)	{
		$sql = "SELECT p.foto_perfil	FROM persona as p WHERE p.idpersona = '$id' ;";
		return ejecutarConsultaSimpleFila($sql);
	}

}
