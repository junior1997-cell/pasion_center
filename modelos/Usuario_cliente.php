<?php
//Incluímos inicialmente la conexión a la base de datos
require "../config/Conexion_v2.php";

class Usuario_cliente
{

  //Implementamos nuestro constructor
  public $id_usr_sesion; public $id_empresa_sesion;
  //Implementamos nuestro constructor
  public function __construct( $id_usr_sesion = 0, $id_empresa_sesion = 0 )
  {
    $this->id_usr_sesion =  isset($_SESSION['idpersona_cliente']) ? $_SESSION["idpersona_cliente"] : 0;
		// $this->id_empresa_sesion = isset($_SESSION['idempresa']) ? $_SESSION["idempresa"] : 0;
  }

  // validar inicio de sesión del usuario cliente
  public function verificar($login, $clave){
    $sql = "SELECT pc.idpersona_cliente, p.idpersona, p.nombre_razonsocial, p.apellidos_nombrecomercial, 
    pc.landing_user, pc.landing_password, 
    p.tipo_documento, p.numero_documento, p.foto_perfil
    FROM persona_cliente AS pc
    INNER JOIN persona AS p ON pc.idpersona = p.idpersona
    WHERE pc.landing_user = '$login' AND pc.landing_password = '$clave';";
    // AND pc.estado = 1 AND pc.estado_delete = 1
    // AND p.estado = 1 AND p.estado_delete = 1
    
    $user = ejecutarConsultaSimpleFila($sql); if ($user['status'] == false) {  return $user; } 
    $data = [ 'status'=>true, 'message'=>'todo okey','data'=> ['usuario_cliente' => $user['data']]  ];
    return $data;
  }
}