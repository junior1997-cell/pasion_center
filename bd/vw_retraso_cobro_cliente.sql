
SELECT
  pco.idpersona_cliente_v2,
  pco.idpersona, 
  pco.idpersona_cliente,
  pco.cliente_nombre_completo,
  pco.dia_cancelacion,
  CONCAT(
    YEAR(pco.primera_venta),
    '-',
    UPPER( LEFT(MONTHNAME(pco.primera_venta), 1) ),
    SUBSTR(MONTHNAME(pco.primera_venta), 2)
  ) AS mes_inicio,
  ROUND(
    COALESCE( ( pco.cant_total_mes - co.cant_cobrado ), 0 ),
    2
  ) AS avance,
  COALESCE(co.cant_cobrado, 0) AS cant_cobrado,
  pco.cant_total_mes AS cant_total,
  CASE 
    WHEN( pco.cant_total_mes - co.cant_cobrado ) = 0 THEN 'SIN DEUDA' 
    WHEN( pco.cant_total_mes - co.cant_cobrado ) > 0 THEN 'DEUDA' 
    WHEN( pco.cant_total_mes - co.cant_cobrado ) < 0 THEN 'ADELANTO' ELSE '-'
	END AS estado_deuda,
	CASE 
    WHEN( pco.cant_total_mes - co.cant_cobrado ) < 0 THEN ABS( ( pco.cant_total_mes - co.cant_cobrado )) 
    ELSE( pco.cant_total_mes - co.cant_cobrado )
	END AS avance_v2,
	pco.tipo_documento_abrev_nombre,
	pco.numero_documento,
  pco.idpersona_trabajador,
	pco.trabajador_nombre,

  pco.estado_pc, pco.estado_delete_pc, pco.estado_p, pco.estado_delete_p
FROM
  (SELECT
    
    MIN(vd.periodo_pago_format) AS primera_venta,
    CASE 
      WHEN pc.fecha_cancelacion > CURDATE() THEN DATE_FORMAT(pc.fecha_cancelacion, '%d/%m/%Y') 
      ELSE CONCAT( DATE_FORMAT(pc.fecha_cancelacion, '%d'), ' de cada mes' )
    END AS dia_cancelacion,
    CASE 
      WHEN pc.fecha_cancelacion > CURDATE() THEN TIMESTAMPDIFF( MONTH, MIN(vd.periodo_pago_format), CURDATE()) 
      ELSE 
        CASE 
          WHEN DATE_FORMAT(CURDATE(), '%d') > DATE_FORMAT(pc.fecha_cancelacion, '%d') THEN TIMESTAMPDIFF( MONTH, MIN(vd.periodo_pago_format), CURDATE()) +1 
          ELSE TIMESTAMPDIFF( MONTH, MIN(vd.periodo_pago_format), CURDATE())
        END
    END AS cant_total_mes,

    p.idpersona,
    LPAD(pc.idpersona_cliente, 5, '0') AS idpersona_cliente_v2,     
    pc.idpersona_cliente,
    CASE 
      WHEN p.tipo_persona_sunat = 'NATURAL' THEN CONCAT( p.nombre_razonsocial, ' ',  p.apellidos_nombrecomercial ) 
      WHEN p.tipo_persona_sunat = 'JURÍDICA' THEN p.nombre_razonsocial 
      ELSE '-'
    END AS cliente_nombre_completo,
    
    p.numero_documento,
    pc.estado as estado_pc,	pc.estado_delete as estado_delete_pc,
    p.estado as estado_p, p.estado_delete as estado_delete_p,

    sc06.abreviatura AS tipo_documento_abrev_nombre,

    pt.idpersona_trabajador,
    p1.nombre_razonsocial AS trabajador_nombre

    FROM persona_cliente AS pc
    INNER JOIN persona AS p ON p.idpersona = pc.idpersona
    INNER JOIN persona_trabajador AS pt on pc.idpersona_trabajador = pt.idpersona_trabajador
	  INNER JOIN persona as p1 on pt.idpersona = p1.idpersona
    INNER JOIN sunat_c06_doc_identidad AS sc06 ON p.tipo_documento = sc06.code_sunat
    INNER JOIN venta v ON v.idpersona_cliente = pc.idpersona_cliente
    INNER JOIN venta_detalle AS vd ON vd.idventa = v.idventa
    WHERE vd.es_cobro = 'SI' AND v.estado = 1 AND v.estado_delete = 1 AND v.sunat_estado = 'ACEPTADA' AND v.tipo_comprobante IN('01', '03', '12')
    GROUP BY p.idpersona, pc.idpersona_cliente
    ORDER BY pc.idpersona_cliente, cant_total_mes
  ) AS pco
  LEFT JOIN
    (SELECT  pc.idpersona_cliente, COUNT(v.idventa) AS cant_cobrado
      FROM venta AS v
      INNER JOIN venta_detalle AS vd ON vd.idventa = v.idventa
      INNER JOIN persona_cliente AS pc ON pc.idpersona_cliente = v.idpersona_cliente
      WHERE vd.es_cobro = 'SI' AND v.estado = 1 AND v.estado_delete = 1 AND v.sunat_estado = 'ACEPTADA' AND v.tipo_comprobante IN('01', '03', '12')
      GROUP BY pc.idpersona_cliente
      ORDER BY COUNT(v.idventa)
      DESC
    ) AS co ON pco.idpersona_cliente = co.idpersona_cliente
ORDER BY avance DESC;