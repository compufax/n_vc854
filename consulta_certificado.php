<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');

function obtener_informacion_entregas($datos){
	$select .= "SELECT p.numero as nomplaza, a.cve, CONCAT(a.fecha,' ',a.hora) as fechahora, a.placa, a.ticket, CONCAT(b.fecha,' ',b.hora) as fechahoraticket, c.nombre as nomengomado, IF(a.estatus='C','',a.certificado) as certificado, d.nombre as nomanio, g.nombre as nomtecnico, h.nombre as nomlinea, i.usuario, a.estatus FROM certificados a INNER JOIN cobro_engomado b ON b.plaza = a.plaza AND b.cve = a.ticket INNER JOIN engomados c ON c.cve = a.engomado INNER JOIN anios_certificados d ON d.cve = a.anio INNER JOIN tecnicos g ON g.plaza = a.plaza AND g.cve = a.tecnico INNER JOIN cat_lineas h ON h.cve = a.linea INNER JOIN usuarios i ON i.cve = a.usuario INNER JOIN plazas p ON p.cve = a.plaza WHERE a.certificado='{$datos['busquedacertificado']}' AND a.estatus!='C' ORDER BY a.fecha DESC, a.cve DESC";
	$res = mysql_query($select);
	return $res;
}

function obtener_informacion_cancelaciones($datos){
	$select .= "SELECT p.numero as nomplaza, a.cve, CONCAT(a.fecha,' ',a.hora) as fechahora, b.nombre as nommotivo, c.nombre as nomengomado, d.nombre as nomanio, a.certificado, a.placa, e.nombre as nomtecnico, f.nombre as nomlinea, a.obs, g.usuario, a.estatus FROM certificados_cancelados a INNER JOIN motivos_cancelacion_certificados b ON b.cve = a.motivo INNER JOIN engomados c ON c.cve = a.engomado INNER JOIN anios_certificados d ON d.cve = a.anio LEFT JOIN tecnicos e ON e.plaza=a.plaza AND e.cve = a.tecnico LEFT JOIN cat_lineas f ON f.cve = a.linea INNER JOIN usuarios g ON g.cve = a.usuario INNER JOIN plazas p ON p.cve = a.plaza WHERE a.certificado='{$datos['busquedacertificado']}' AND a.estatus!='C' ORDER BY a.fecha DESC, a.cve DESC";
	$res = mysql_query($select);
	return $res;
}
require_once('validarloging.php');

if($_POST['cmd']==0){
?>

<div class="row justify-content-center">
	<div class="col-xl-12 col-lg-12 col-md-12">
        <div class="form-group row">
			<label class="col-sm-2 col-form-label">Certificado</label>
			<div class="col-sm-4">
            	<input type="text" class="form-control" id="busquedacertificado" name="busquedacertificado" placeholder="Certificado" value="">
        	</div>
        </div>
        <div class="form-group row">
        	<div class="col-sm-12" align="center">
	        	<button type="button" class="btn btn-primary" onClick="buscar();">
	            	Buscar
	        	</button>
        	</div>
        </div>
    </div>
</div>
<div class="row" id="resultadocorte">
	
</div>
<script>
	function buscar(){
		$.ajax({
		  url: 'consulta_certificado.php',
		  type: "POST",
		  data: {
			menu: $('#cvemenu').val(),
			cmd: 10,
			cveusuario: $('#cveusuario').val(),
    		busquedacertificado: $("#busquedacertificado").val(),
    		cvemenu: $('#cvemenu').val(),
    		cveplaza: $('#cveplaza').val(),
    		cveusuario: $('#cveusuario').val()
		  },
			success: function(data) {
				$('#resultadocorte').html(data);
			}
		});
	}
</script>
<?php
}

if($_POST['cmd']==10){
	$res = obtener_informacion_entregas($_POST);
	$colspan = 9;
?>
	<h3>Entregas</h3>
	<table class="table">
	  <thead>
	    <tr>
	      <th scope="col" style="text-align: center;">Plaza</th>
	      <th scope="col" style="text-align: center;">Folio</th>
	      <th scope="col" style="text-align: center;">Fecha</th>
	      <th scope="col" style="text-align: center;">Placa</th>
		  <th scope="col" style="text-align: center;">Ticket</th> 
		  <th scope="col" style="text-align: center;">Fecha Ticket</th> 
	      <th scope="col" style="text-align: center;">Tipo de Certificado</th> 
	      <th scope="col" style="text-align: center;">Certificado</th> 
	      <th scope="col" style="text-align: center;">A&ntilde;o de Certificaci&oacute;n</th> 
	      <th scope="col" style="text-align: center;">Tecnico</th> 
	      <th scope="col" style="text-align: center;">Linea</th> 
	      <th scope="col" style="text-align: center;">Usuario</th> 
	    </tr>
	  </thead>
	  <tbody>
	<?php
		$totales = array();
		$i = 0;
		while($row = mysql_fetch_assoc($res)){
	?>
	    <tr>
	      <td align="left"><?php echo utf8_encode($row['nomplaza']);?></td>
	      <td align="center"><?php echo $row['cve'];?></td>
	      <td align="center"><?php echo mostrar_fechas(substr($row['fechahora'],0,10)).' '.substr($row['fechahora'],11);?></td>
	      <td align="center"><?php echo $row['placa'];?></td>
	      <td align="center"><?php echo $row['ticket'];?></td>
	      <td align="center"><?php echo mostrar_fechas(substr($row['fechahoraticket'],0,10)).' '.substr($row['fechahoraticket'],11);?></td>
	      <td align="left"><?php echo utf8_encode($row['nomengomado']);?></td>
	      <td align="left"><?php echo utf8_encode($row['certificado']);?></td>
	      <td align="left"><?php echo ($row['nomanio']);?></td>
	      <td align="left"><?php echo utf8_encode($row['nomtecnico']);?></td>
	      <td align="left"><?php echo utf8_encode($row['nomlinea']);?></td>
	      <td align="left"><?php echo ($row['usuario']);?></td>
	    </tr>
	<?php
		}
	?>
		<tr>
			<th colspan="16" style="text-align: left;"><?php echo $i;?> Registro(s)</th>
		</tr>
	  </tbody>
	</table>
	
	<h3>Cancelaciones</h3>
	<table class="table">
	  <thead>
	    <tr>
	      <th scope="col" style="text-align: center;">Plaza</th>
	      <th scope="col" style="text-align: center;">Folio</th>
	      <th scope="col" style="text-align: center;">Fecha</th>
	      <th scope="col" style="text-align: center;">Motivo</th>
	      <th scope="col" style="text-align: center;">Tipo de Certificado</th> 
	      <th scope="col" style="text-align: center;">Semestre</th>
	      <th scope="col" style="text-align: center;">Certificado</th> 
	      <th scope="col" style="text-align: center;">Placa</th> 
	      <th scope="col" style="text-align: center;">Tecnico</th> 
	      <th scope="col" style="text-align: center;">Linea</th> 
	      <th scope="col" style="text-align: center;">Observaciones</th> 
	      <th scope="col" style="text-align: center;">Usuario</th> 
	    </tr>
	  </thead>
	  <tbody>
	<?php
		$res = obtener_informacion_cancelaciones($_POST);
		$totales = array();
		$i = 0;
		while($row = mysql_fetch_assoc($res)){
	?>
	    <tr>
	      <td align="left"><?php echo utf8_encode($row['nomplaza']);?></td>
	      <td align="center"><?php echo $row['cve'];?></td>
	      <td align="center"><?php echo mostrar_fechas(substr($row['fechahora'],0,10)).' '.substr($row['fechahora'],11);?></td>
	      <td align="left"><?php echo utf8_encode($row['nommotivo']);?></td>
	      <td align="left"><?php echo utf8_encode($row['nomengomado']);?></td>
	      <td align="left"><?php echo ($row['nomanio']);?></td>
	      <td align="left"><?php echo utf8_encode($row['certificado']);?></td>
	      <td align="center"><?php echo utf8_encode($row['placa']);?></td>
	      <td align="left"><?php echo utf8_encode($row['nomtecnico']);?></td>
	      <td align="left"><?php echo utf8_encode($row['nomlinea']);?></td>
	      <td align="left"><?php echo utf8_encode($row['obs']);?></td>
	      <td align="left"><?php echo ($row['usuario']);?></td>
	    </tr>
	<?php
		}
	?>
		<tr>
			<th colspan="16" style="text-align: left;"><?php echo $i;?> Registro(s)</th>
		</tr>
	  </tbody>
	</table>
<?php
}

?>