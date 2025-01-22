<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

class Cliente
{
	public $id_usr_sesion; public $id_persona_sesion; public $id_trabajador_sesion;
	//Implementamos nuestro constructor
	public function __construct($id_usr_sesion = 0, $id_empresa_sesion = 0)
	{
		$this->id_usr_sesion =  isset($_SESSION['idusuario']) ? $_SESSION["idusuario"] : 0;
		$this->id_persona_sesion = isset($_SESSION['idpersona']) ? $_SESSION["idpersona"] : 0;
		$this->id_trabajador_sesion = isset($_SESSION['idpersona_trabajador']) ? $_SESSION["idpersona_trabajador"] : 0;
	}
	/*T-- paepelera --desacctivar
	C-- crear
	R-- read
	U-- actualizar
	D-- delete -- eliminar*/	

	//Implementamos un método para insertar registros
	public function insertar_cliente(	$idtipo_persona, $idbancos,	$idcargo_trabajador,  $tipo_persona_sunat,	
	$tipo_documento, $numero_documento,	$nombre_razonsocial, $apellidos_nombrecomercial, $fecha_nacimiento,	$celular,	$direccion,	
	$distrito, $departamento, $provincia, $ubigeo, $correo, $idselec_centroProbl, $fecha_afiliacion, $nota, $img_perfil	) {
		
		$sql_0 = "SELECT p.*, CASE 
			WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
			WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
			ELSE '-'
		END AS cliente_nombre_completo  FROM persona as p WHERE p.numero_documento = '$numero_documento'";
		$buscando = ejecutarConsultaArray($sql_0);		

		if ( empty($buscando['data']) || $tipo_documento == '0' ) {
			$sql1 = "INSERT INTO persona(idtipo_persona, idbancos, idcargo_trabajador, tipo_persona_sunat, nombre_razonsocial, 
			apellidos_nombrecomercial, tipo_documento, numero_documento, fecha_nacimiento, celular, direccion, departamento, provincia, 
			distrito, cod_ubigeo, correo,foto_perfil) 
			VALUES ( '$idtipo_persona', '$idbancos', '$idcargo_trabajador', '$tipo_persona_sunat', '$nombre_razonsocial', 
			'$apellidos_nombrecomercial', '$tipo_documento', '$numero_documento', '$fecha_nacimiento', '$celular', '$direccion', '$departamento', '$provincia', 
			'$distrito', '$ubigeo', '$correo','$img_perfil')";
			$inst_persona = ejecutarConsulta_retornarID($sql1, 'C');if ($inst_persona['status'] == false) {return $inst_persona;}

			$id = $inst_persona['data'];

			$sql2 = "INSERT INTO persona_cliente(idpersona, idcentro_poblado, fecha_afiliacion,nota) 
			VALUES ('$id', '$idselec_centroProbl', '$fecha_afiliacion','$nota')";
			$insertar =  ejecutarConsulta($sql2, 'C');	if ($insertar['status'] == false) {	return $insertar;	}

			return $insertar;
		} else {
			$info_repetida = ''; 

			foreach ($buscando['data'] as $key => $value) {
				$info_repetida .= '<li class="text-left font-size-13px">
					<span class="font-size-15px text-danger"><b>Nombre: </b>'.$value['cliente_nombre_completo'].'</span><br>
					<span class="font-size-15px text-danger"><b>DNI: </b>'.$value['numero_documento'].'</span><br>
					<b>Distrito: </b>'.$value['distrito'].'<br>
					<b>Papelera: </b>'.( $value['estado']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO') .' <b>|</b>
					<b>Eliminado: </b>'. ($value['estado_delete']==0 ? '<i class="fas fa-check text-success"></i> SI':'<i class="fas fa-times text-danger"></i> NO').'<br>
					<hr class="m-t-2px m-b-2px">
				</li>'; 
			}
			return array( 'status' => 'duplicado', 'message' => 'duplicado', 'data' => '<ul>'.$info_repetida.'</ul>', 'id_tabla' => '' );
		}		

		
	}

	//Implementamos un método para editar registros
	public function editar_cliente(	$idpersona,	$idtipo_persona, $idbancos,	$idcargo_trabajador, $idpersona_cliente, $tipo_persona_sunat,	
	$tipo_documento, $numero_documento,	$nombre_razonsocial, $apellidos_nombrecomercial, $fecha_nacimiento,	$celular,	$direccion,	
	$distrito, $departamento, $provincia, $ubigeo, $correo, $idselec_centroProbl, $fecha_afiliacion, $nota, $img_perfil	) {

		$sql1 = "UPDATE persona SET 		
						idtipo_persona='$idtipo_persona',
						idbancos='$idbancos',
						idcargo_trabajador='$idcargo_trabajador',
						tipo_persona_sunat='$tipo_persona_sunat',
						nombre_razonsocial='$nombre_razonsocial',
						apellidos_nombrecomercial='$apellidos_nombrecomercial',
						tipo_documento='$tipo_documento',
						numero_documento='$numero_documento',
						fecha_nacimiento='$fecha_nacimiento',
						celular='$celular',
						direccion='$direccion',
						departamento='$departamento',
						provincia='$provincia',
						distrito='$distrito',
						cod_ubigeo='$ubigeo',
						correo='$correo',		
						foto_perfil='$img_perfil'	
				WHERE idpersona='$idpersona';";

		$editar1 =  ejecutarConsulta($sql1, 'U');	if ($editar1['status'] == false) {return $editar1;}

		$sql = "UPDATE persona_cliente SET
		idpersona ='$idpersona',
		idcentro_poblado='$idselec_centroProbl',
		fecha_afiliacion='$fecha_afiliacion',
		nota='$nota'
		WHERE idpersona_cliente='$idpersona_cliente';"; 

		$editar =  ejecutarConsulta($sql, 'U');	if ($editar['status'] == false) {	return $editar;	}

		return $editar;
	}

	//Implementamos un método para desactivar color
	public function desactivar_cliente($idpersona_cliente, $descripcion)	{
		$sql = "UPDATE persona_cliente SET estado='0', nota ='$descripcion' WHERE idpersona_cliente='$idpersona_cliente'";
		$desactivar = ejecutarConsulta($sql, 'T');

		return $desactivar;
	}

	//Implementamos un método para desactivar color
	public function activar_cliente($idpersona_cliente, $descripcion)	{
		$sql = "UPDATE persona_cliente SET estado='1', nota ='$descripcion' WHERE idpersona_cliente='$idpersona_cliente'";
		$desactivar = ejecutarConsulta($sql, 'T');

		return $desactivar;
	}

	//Implementamos un método para eliminar persona_cliente
	public function eliminar_cliente($idpersona_cliente)	{
		$sql = "UPDATE persona_cliente SET estado_delete='0' WHERE idpersona_cliente='$idpersona_cliente'";
		$eliminar =  ejecutarConsulta($sql, 'D');		if ($eliminar['status'] == false) {	return $eliminar;	}

		return $eliminar;
	}

	//Implementar un método para mostrar los datos de un registro a modificar
	public function mostrar_cliente($idpersona_cliente)	{
		$sql = "SELECT vw_c.*
		FROM vw_cliente_all as vw_c	
		WHERE vw_c.idpersona_cliente='$idpersona_cliente';";

		return ejecutarConsultaSimpleFila($sql);
	}

	//Implementar un método para listar los registros
	public function tabla_principal_cliente( $filtro_mes_afi, $filtro_dist)	{

		//var_dump($filtro_mes_afiliacion); die();

		$filtro_mes_afiliacion  = ''; $filtro_distrito  = ''; 

		if ( empty($filtro_mes_afi) 		|| $filtro_mes_afi 		== 'TODOS' ) { } else{ 	$filtro_mes_afiliacion 		= "AND DATE_FORMAT(vw_c.fecha_afiliacion, '%Y-%m')  = '$filtro_mes_afi'";	}
		if ( empty($filtro_dist) 				|| $filtro_dist 				== 'TODOS' ) { } else{	$filtro_distrito 		= "AND vw_c.distrito = '$filtro_dist'";	}
		//var_dump($filtro_mes_afiliacion); die();
		$sql = "SELECT 		
		vw_c.*		
		FROM vw_cliente_all as vw_c		
		where  vw_c.estado_delete_pc='1'  $filtro_mes_afiliacion $filtro_distrito 
		ORDER BY vw_c.idpersona_cliente DESC";
		return ejecutarConsulta($sql);
	}

	public function empresa(){
		$sql = "SELECT * FROM empresa WHERE numero_documento = '20610630431'";
		return ejecutarConsultaSimpleFila($sql);
	}
	
	// ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════
	
	public function select2_filtro_mes_afiliacion()	{
		
		$sql = "SELECT CONCAT(DATE_FORMAT(fecha_afiliacion, '%Y - %m '),
    CASE MONTH(fecha_afiliacion)
        WHEN 1 THEN 'Enero'
        WHEN 2 THEN 'Febrero'
        WHEN 3 THEN 'Marzo'
        WHEN 4 THEN 'Abril'
        WHEN 5 THEN 'Mayo'
        WHEN 6 THEN 'Junio'
        WHEN 7 THEN 'Julio'
        WHEN 8 THEN 'Agosto'
        WHEN 9 THEN 'Septiembre'
        WHEN 10 THEN 'Octubre'
        WHEN 11 THEN 'Noviembre'
        WHEN 12 THEN 'Diciembre'
    END) AS  mes_afiliacion, DATE_FORMAT(fecha_afiliacion, '%Y-%m') AS fecha_mes_anio, COUNT(pc.idpersona_cliente) AS cant_cliente
		FROM persona_cliente as pc where fecha_afiliacion is not null
		GROUP BY mes_afiliacion
		ORDER BY mes_afiliacion ASC;";
		return ejecutarConsulta($sql);
	}

	public function select2_filtro_anio_pago()	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'VENDEDOR') {
			$filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		} 
		$sql = "SELECT YEAR(vd.periodo_pago_format) as anio_cancelacion, COUNT(pc.idpersona_cliente) AS cant_cliente
		FROM venta as v
		INNER JOIN venta_detalle as vd ON vd.idventa = v.idventa
		INNER JOIN persona_cliente as pc on pc.idpersona_cliente = v.idpersona_cliente
		where v.estado = '1' AND v.estado_delete = '1' AND v.sunat_estado in ('ACEPTADA', 'POR ENVIAR') AND v.tipo_comprobante in( '01', '03', '12' ) $filtro_id_trabajador
		GROUP BY YEAR(vd.periodo_pago_format)
		ORDER BY YEAR(vd.periodo_pago_format) DESC;";
		return ejecutarConsulta($sql);
	}

	public function select2_filtro_distrito()	{
		
		$sql = "SELECT p.distrito,  COUNT(pc.idpersona_cliente) AS cant_cliente
		FROM persona_cliente as pc
		INNER JOIN persona as p ON p.idpersona = pc.idpersona
		WHERE pc.estado = '1' and pc.estado_delete = '1'
		GROUP BY p.distrito ORDER BY COUNT(pc.idpersona_cliente) desc, p.distrito asc;";
		return ejecutarConsulta($sql);
	}

	public function select2_filtro_zona_antena()	{
		$filtro_id_trabajador  = '';
		if ($_SESSION['user_cargo'] == 'VENDEDOR') {
			$filtro_id_trabajador = "AND pc.idpersona_trabajador = '$this->id_trabajador_sesion'";
		} 
		$sql = "SELECT za.idzona_antena, za.nombre, za.ip_antena, COUNT(pc.idpersona_cliente) AS cant_cliente
		FROM persona_cliente as pc
		INNER JOIN zona_antena as za ON za.idzona_antena = pc.idzona_antena
		WHERE za.estado = '1' and za.estado_delete = '1' $filtro_id_trabajador
		GROUP BY pc.idzona_antena ORDER BY COUNT(idpersona_cliente) desc, za.nombre asc;";
		return ejecutarConsulta($sql);
	}
	
	public function select2_plan()	{
		$sql = "SELECT idplan, nombre, costo FROM plan WHERE estado='1' and estado_delete='1';";
		return ejecutarConsulta($sql);
	}

	public function select2_zona_antena()	{
		$sql = "SELECT idzona_antena, nombre, ip_antena FROM zona_antena WHERE estado='1' and estado_delete='1';";
		return ejecutarConsulta($sql);
	}

	public function select2_trabajador(){
		$sql = "SELECT pt.idpersona_trabajador, p.idpersona, 
		CASE 
		WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT(p.nombre_razonsocial, ' ', p.apellidos_nombrecomercial) 
		WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
		ELSE '-'
		END AS nombre_completo, 
		p.tipo_documento, p.numero_documento 
		FROM persona_trabajador pt 
		INNER JOIN persona AS p ON pt.idpersona = p.idpersona 
		WHERE pt.estado = '1' AND pt.estado_delete = '1' AND p.idtipo_persona = '2';";
		return ejecutarConsulta($sql);
	}

	//Implementar un método para listar los registros
	public function perfil_trabajador($id)	{
		$sql = "SELECT p.foto_perfil	FROM persona as p WHERE p.idpersona = '$id' ;";
		return ejecutarConsultaSimpleFila($sql);
	}

	public function selec_centroProbl(){
		$sql="SELECT *
		FROM vw_ubigeo_depa_prov_dist_cp WHERE estado='1' and estado_delete='1' order by nombre_departamento, nombre_provincia, nombre_distrito, nombre_centro_poblado;";
		return ejecutarConsulta($sql);
	}
}
