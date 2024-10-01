SELECT
	-- ::::::::::::::: DATOS CLIENTE ::::::::::::::: 
	LPAD (pc.idpersona_cliente, 5, '0') as idpersona_cliente_v2,
	pc.idpersona_cliente,
	pc.ip_personal,
	DAY (pc.fecha_cancelacion) AS dia_cancelacion,
	CASE 
		WHEN pc.fecha_cancelacion > CURDATE() THEN DATE_FORMAT(pc.fecha_cancelacion, '%d/%m/%Y') 
		ELSE CONCAT( DATE_FORMAT(pc.fecha_cancelacion, '%d'), ' de cada mes' )
	END AS dia_cancelacion_v2,
	pc.fecha_cancelacion,
	pc.fecha_afiliacion,
	pc.descuento,
	pc.estado_descuento,
	cp.nombre as centro_poblado,
	pc.nota,
	pc.usuario_microtick,
	pc.estado as estado_pc,	pc.estado_delete as estado_delete_pc,
	CASE
		WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT (p.nombre_razonsocial,' ',p.apellidos_nombrecomercial	)
		WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial
		ELSE '-'
	END AS cliente_nombre_completo,

	IF(pc.fecha_cancelacion > CURDATE(),
			DATEDIFF(pc.fecha_cancelacion, CURDATE()),
			DATEDIFF(
				IF(DAY(pc.fecha_cancelacion) >= DAY(CURDATE()),
					DATE_ADD(LAST_DAY(CURDATE() - INTERVAL 1 MONTH), INTERVAL DAY(pc.fecha_cancelacion) DAY),
					DATE_ADD(LAST_DAY(CURDATE()), INTERVAL DAY(pc.fecha_cancelacion) DAY)
				),
				CURDATE()
			) 
		) AS dias_para_proximo_pago,
		IF(pc.fecha_cancelacion > CURDATE(),
			DATE_FORMAT(pc.fecha_cancelacion, '%d/%m/%Y'),
			DATE_FORMAT(
				IF(DAY(pc.fecha_cancelacion) >= DAY(CURDATE()),
					DATE_ADD(LAST_DAY(CURDATE() - INTERVAL 1 MONTH), INTERVAL DAY(pc.fecha_cancelacion) DAY),
					DATE_ADD(LAST_DAY(CURDATE()), INTERVAL DAY(pc.fecha_cancelacion) DAY)				
				),
			'%d/%m/%Y'
			)
		)	AS proximo_pago,
	-- ::::::::::::::: DATOS PERSONA CLIENTE ::::::::::::::: 
	p.idpersona, p.idtipo_persona, p.idbancos, p.idcargo_trabajador, p.tipo_persona_sunat, p.nombre_razonsocial, p.apellidos_nombrecomercial, 
	p.tipo_documento, p.numero_documento, p.fecha_nacimiento, p.celular, p.direccion, p.departamento, p.provincia, p.distrito, p.cod_ubigeo, p.correo, 
	p.cuenta_bancaria, p.cci, p.titular_cuenta, p.foto_perfil, p.estado as estado_p, p.estado_delete as estado_delete_p,
	-- ::::::::::::::: DATOS TECNICO (TRABAJADOR A CARGO) ::::::::::::::: 
 	pt.idpersona_trabajador,
	p1.nombre_razonsocial AS trabajador_nombre,
	-- ::::::::::::::: DATOS PLAN ::::::::::::::: 
	pl.idplan,
	pl.nombre as nombre_plan,
	pl.costo,
	-- ::::::::::::::: DATOS ZONA ANTENA ::::::::::::::: 
	za.idzona_antena,
	za.nombre as zona,
	za.ip_antena,
	-- ::::::::::::::: DATOS SUNAT ::::::::::::::: 
	sc06.abreviatura as tipo_documento_abrev_nombre

FROM
	persona_cliente as pc
	INNER JOIN persona AS p on pc.idpersona = p.idpersona
	INNER JOIN persona_trabajador AS pt on pc.idpersona_trabajador = pt.idpersona_trabajador
	INNER JOIN persona as p1 on pt.idpersona = p1.idpersona
	INNER JOIN plan as pl on pc.idplan = pl.idplan
	INNER JOIN zona_antena as za on pc.idzona_antena = za.idzona_antena
	INNER JOIN sunat_c06_doc_identidad as sc06 on p.tipo_documento = sc06.code_sunat
	INNER JOIN centro_poblado as cp on pc.idcentro_poblado = cp.idcentro_poblado
ORDER BY	pc.idpersona_cliente DESC