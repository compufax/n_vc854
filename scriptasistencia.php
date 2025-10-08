<?php

include("cnx_db.php");

if(date('w') != 0){
	mysql_query("insert into asistencia (plaza, fecha, personal, estatus) select a.plaza, CURDATE(), a.cve, IF(IFNULL(b.motivo,0) > 0, 2, 0) from personal a left join dias_justificados b on a.cve = b.personal and b.fecha = CURDATE() where a.estatus=1");
}
else{
	mysql_query("insert into asistencia (plaza, fecha, personal, estatus, domingo) select a.plaza, CURDATE(), a.cve, IF(IFNULL(b.motivo,0) > 0, 2, 0), 1 from personal a left join dias_justificados b on a.cve = b.personal and b.fecha = CURDATE() where a.estatus=1");	
}

if(date('d')=='01'){
	$fecha_fin = date( "Y-m-d", strtotime("-1 day"));
	$fecha_ini = substr($fecha_fin, 0, 8).'01';
	$root = mysql_fetch_assoc(mysql_query("SELECT correosexistencia FROM usuarios WHERE cve=1"));
	if($root['correosexistencia']!=''){
		$reporte='';
		$reporte .= '"Periodo '.$fecha_ini.' al '.$fecha_fin.'",,,,,,,,'."\n";
		$Plazas = mysql_query("SELECT cve, numero, nombre FROM plazas WHERE tipo_plaza!=0 AND estatus!='I' ORDER BY lista");
		while($Plaza = mysql_fetch_assoc($Plazas)){
			$reporte .= '"'.$Plaza['numero'].' '.$Plaza['nombre'].'",,,,,,,,'."\n";
			$reporte .= 'Tipo de Holograma,Inventario Inicial,Costo Inventario Inicial,Compras,Costo Compras,Certificados Utilizados,Costo Certificados Utilizados,Inventario Final,Costo Inventario Final'."\n";
			$resTipo = mysql_query("SELECT cve, nombre FROM engomados WHERE entrega=1 AND cve in (2,3,5,1,19,24) ORDER BY nombre");
			while($Tipo = mysql_fetch_assoc($resTipo)){
				$compras = mysql_fetch_assoc(mysql_query("SELECT 
					SUM(IF(fecha<'{$fecha_ini}',(foliofin+1-folioini),0)) as cantidadanterior, 
					SUM(IF(fecha<'{$fecha_ini}',(foliofin+1-folioini)*costo,0)) as costoanterior, 
					SUM(IF(fecha>='{$fecha_ini}',(foliofin+1-folioini),0)) as cantidad,
					SUM(IF(fecha>='{$fecha_ini}',(foliofin+1-folioini)*costo,0)) as costo
					FROM compra_certificados WHERE plaza={$Plaza['cve']} AND engomado={$Tipo['cve']} AND anio>=24 AND fecha<='{$fecha_fin}' AND estatus!='C'"));
				$entregas = mysql_fetch_assoc(mysql_query("SELECT 
					SUM(IF(fecha<'{$fecha_ini}',1, 0)) as cantidadanterior, 
					SUM(IF(fecha<'{$fecha_ini}',costo, 0)) as costoanterior, 
					SUM(IF(fecha>='{$fecha_ini}',1, 0)) as cantidad,
					SUM(IF(fecha>='{$fecha_ini}', costo, 0)) as costo
					FROM certificados WHERE plaza={$Plaza['cve']} AND engomado={$Tipo['cve']} AND anio>=24 AND fecha<='{$fecha_fin}' AND estatus!='C'"));
				$cancelados = mysql_fetch_assoc(mysql_query("SELECT 
					SUM(IF(fecha<'{$fecha_ini}',1, 0)) as cantidadanterior, 
					SUM(IF(fecha<'{$fecha_ini}',costo, 0)) as costoanterior, 
					SUM(IF(fecha>='{$fecha_ini}',1, 0)) as cantidad,
					SUM(IF(fecha>='{$fecha_ini}', costo, 0)) as costo
					FROM certificados_cancelados WHERE plaza={$Plaza['cve']} AND engomado={$Tipo['cve']} AND anio>=24 AND fecha<='{$fecha_fin}' AND estatus!='C'"));
				$anterior = $compras['cantidadanterior']-$entregas['cantidadanterior']-$cancelados['cantidadanterior'];
				$costoanterior = $compras['costoanterior']-$entregas['costoanterior']-$cancelados['costoanterior'];
				$utilizados=$entregas['cantidad']+$cancelados['cantidad'];
				$costoutilizados=$entregas['costo']+$cancelados['costo'];
				$actual = $anterior+$compras['cantidad']-$entregas['cantidad']-$cancelados['cantidad'];
				$costoaactual = $costoanterior+$compras['costo']-$entregas['costo']-$cancelados['costo'];
				$reporte .= '"'.$Tipo['nombre'].'",'.$anterior.','.round($costoanterior,2).','.$compras['cantidad'].','.round($compras['costo'],2).','.$utilizados.','.round($costoutilizados,2).','.$actual.','.round($costoaactual,2)."\n";
			}
			$reporte .= ',,,,,,,,'."\n";
		}
		file_put_contents("cfdi/exitenciacertificados.csv", $reporte);
	
		$mail = obtener_mail();				
		$mail->FromName = "Gverificentros";
		$mail->Subject = "Reporte de Existencia de Certificados Verificacion Senderos";
		$mail->Body = "Reporte";
		$correos = explode(",",trim($root['correosexistencia']));
		foreach($correos as $correo)
			$mail->AddAddress(trim($correo));
		$mail->AddAttachment("cfdi/exitenciacertificados.csv", "Reporte.csv");
		$mail->Send();
		@unlink("cfdi/exitenciacertificados.csv");
	}
}
?>