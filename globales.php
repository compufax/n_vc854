<?php
$NOMBRE = "Verificacion VC854";
$array_nosi = ['No', 'S�'];


$array_modulos = array(
   3=>"Catalogos Generales",
   1=>"Catalogos Locales",
   20=>"Gastos",
   2=>"Ventas",
   7=>"Facturacion",
   9=>"Compras",
   4=>"Personal",
   8=>"Auditoria",
   99=>"Administracion"
);

$url_impresion = 'https://verificentrosendero.com';

$empresa_timbres=28;

function mostrar_fechas($fecha){
	$datos = explode(' ', $fecha);
	$fechas = explode('-', $datos[0]);
	if(count($datos)>1){
		$fechas[0] .= ' '.$datos[1];
	}
	return $fechas[2].'/'.$fechas[1].'/'.$fechas[0];
}



function obtener_valor($id, $tabla, $nombre = 'nombre'){
	$query = mysql_query("SELECT {$nombre} as nombre FROM {$tabla} WHERE id='{$id}'");
	$registro = mysql_fetch_assoc($query);
	return $registro['nombre'];
}

function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}

function convertir_post_utf8(&$POST){
   foreach($POST as $indice => $valor){
      if(!is_array($valor)){
         if(!isJson($valor)){
            $POST[$indice] = utf8_decode($valor);
         }
      }
      else{
         convertir_post_utf8($POST[$indice]);
      }
   }
}

if(isset($_POST) and is_array($_POST)){
   convertir_post_utf8($_POST);
}

function convertir_a_utf8($arreglo) {
   foreach($arreglo as &$valor) {
      $valor = utf8_encode($valor);
   }
   return $arreglo;
}

function nivelUsuario(){
   global $_POST;
   if($_POST['cveusuario']==1){
      return 3;
   }
   elseif($_SESSION['TipoUsuario']==1){
      return 1;
   }
   else{
      $res=mysql_query("SELECT * FROM usuario_accesos WHERE usuario='{$_POST['cveusuario']}' AND menu='{$_POST['cvemenu']}' AND plaza='{$_POST['cveplaza']}'");
      if($row=mysql_fetch_array($res)){
         return $row['acceso'];
      }
      else{
         return 0;
      }
   }
}

class EnvioEmail{
   public $FromName = '';
   public $Subject = '';
   public $Body = '';
   public $Correos = array();
   public $Archivos = array();

   public function AddAddress($correo) {
      $this->Correos[] = $correo;
   }

   public function AddAttachment($file, $name) {
   		$datos_nombre = explode('.', $name);
   		$type = end($datos_nombre);
   		$existe = false;
   		if (file_exists($file)) {
   			$file = file_get_contents($file);
   			$existe = true;
   		}
   		elseif(file_exists('../'.$file)){
   			$file = file_get_contents('../'.$file);
   			$existe = true;
   		}

   		if ($existe) {
    		$this->Archivos[] = array('file'=>base64_encode($file), 'type'=>$type, 'name' => $name);
    	}
   }

   public function Send() {
      $body = array(
         'from' => $this->FromName,
         'subject' => $this->Subject,
         'html' => $this->Body,
         'to' => $this->Correos,
         'attachments' => $this->Archivos
      );
      $url = 'https://service.2ai.io/api/correo';

      //create a new cURL resource
      $ch = curl_init($url);

      //setup request to send json via POST
      //$payload = json_encode($body);

      $payload = http_build_query($body);

      //echo $payload;
      //attach encoded JSON string to the POST fields
      curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

      //set the content type to application/json
      //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','key:AIzaSyB6bHoe8WXBvR2KToHxLv6QixSXrUdN_as'));

      curl_setopt($ch, CURLOPT_HTTPHEADER, array('key:AIzaSyB6bHoe8WXBvR2KToHxLv6QixSXrUdN_as'));

      //return response instead of outputting
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      //execute the POST request
      $resultado = curl_exec($ch);

      //close cURL resource
      $resultado = json_decode($resultado, true);
      curl_close($ch);
      return $resultado['status'];
   }
}

function obtener_mail(){
   $mail = new EnvioEmail();
   return $mail;
}

function getRealIP()
{
   global $_SERVER;
   if( $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
   {
      $client_ip =
         ( !empty($_SERVER['REMOTE_ADDR']) ) ?
            $_SERVER['REMOTE_ADDR']
            :
            ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
               $_ENV['REMOTE_ADDR']
               :
               "unknown" );

      // los proxys van a�adiendo al final de esta cabecera
      // las direcciones ip que van "ocultando". Para localizar la ip real
      // del usuario se comienza a mirar por el principio hasta encontrar
      // una direcci�n ip que no sea del rango privado. En caso de no
      // encontrarse ninguna se toma como valor el REMOTE_ADDR

      $entries = preg_split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);

      reset($entries);
      foreach($entries as $entry)
      {
         $entry = trim($entry);
         if ( preg_match("/^([0-9]+\\.[0-9]+\\.[0-9]+\\.[0-9]+)/", $entry, $ip_list) )
         {
            // http://www.faqs.org/rfcs/rfc1918.html
            $private_ip = array(
                  '/^0\\./',
                  '/^127\\.0\\.0\\.1/',
                  '/^192\\.168\\..*/',
                  '/^172\\.((1[6-9])|(2[0-9])|(3[0-1]))\\..*/',
                  '/^10\\..*/');

            $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

            if ($client_ip != $found_ip)
            {
               $client_ip = $found_ip;
               break;
            }
         }
      }
   }
   else
   {
      $client_ip =
         ( !empty($_SERVER['REMOTE_ADDR']) ) ?
            $_SERVER['REMOTE_ADDR']
            :
            ( ( !empty($_ENV['REMOTE_ADDR']) ) ?
               $_ENV['REMOTE_ADDR']
               :
               "unknown" );
   }

   return $client_ip;

}

function _xmlToArray($node)
{
   $occurance = array();

   if($node->hasChildNodes()){
      foreach($node->childNodes as $child) {
         $occurance[$child->nodeName]++;
      }
   }

   if($node->nodeType == XML_TEXT_NODE) {
      $result = html_entity_decode(htmlentities($node->nodeValue, ENT_COMPAT, 'UTF-8'), ENT_COMPAT,'ISO-8859-15');
   }
   else {
      if($node->hasChildNodes()){
         $children = $node->childNodes;

         for($i=0; $i<$children->length; $i++) {
            $child = $children->item($i);

            if($child->nodeName != '#text') {
               if($occurance[$child->nodeName] > 0 /*1*/) {
                  $result[$child->nodeName][] = _xmlToArray($child);
               }
               else {
                  $result[$child->nodeName] = _xmlToArray($child);
               }
            }
            else if ($child->nodeName == '#text') {
               $text = _xmlToArray($child);

               if (trim($text) != '') {
                  $result[$child->nodeName] = _xmlToArray($child);
               }
            }
         }
      }

      if($node->hasAttributes()) {
         $attributes = $node->attributes;

         if(!is_null($attributes)) {
            foreach ($attributes as $key => $attr) {
               $result["@".$attr->name] = $attr->value;
            }
         }
      }
   }

   return $result;
}

function generar_clave($plaza, $tipo){
   $r1=sprintf("%03s",$plaza);
   $r2=sprintf("%03s",rand(0,999));
   $r3=sprintf("%03s",rand(0,999));
   $r4=sprintf("%03s",rand(0,999));
   $r5=$tipo;
   //$r5=sprintf("%04s",rand(0,9999));
   
   return $r1.$r2.$r3.$r4;
}


function guardaClave($plaza,$ticket,$tipo=0){
   $clave = generar_clave($plaza, $tipo);
   //mysql_query("INSERT claves_facturacion SET cve='$clave', plaza = '$plaza', ticket = '$ticket'") or die(mysql_error());
   while(!$res = mysql_query("INSERT claves_facturacion SET cve='$clave', plaza = '$plaza', ticket = '$ticket', tipo='$tipo'")){
      $clave = generar_clave($plaza, $tipo);
   }
}

function genera_arreglo_facturacion($plaza, $id, $tipo){
   global $array_tipo_pagos, $array_tipo_pagosat;
   if($tipo == 'I'){
      $tabla = 'facturas';
      $campo = 'factura';
   }
   else{
      $tabla = 'notascredito';
      $campo = 'notacredito';
   }

   $Plaza = mysql_fetch_array(mysql_query("SELECT regimensat FROM plazas WHERE cve='".$plaza."'"));
   $documento = array();
   $res = mysql_query("SELECT * FROM $tabla WHERE plaza='".$plaza."' AND cve='".$id."'");
   $row = mysql_fetch_array($res);

   $TipoPago = mysql_fetch_assoc(mysql_query("SELECT clave_sat FROM tipos_pago_factura WHERE cve='{$row['tipo_pago']}'"));

   if($row['idexterno']==0){
      mysql_query("INSERT INTO idexterno_wstimbre (cve) VALUES ('')");
      $row['idexterno'] = mysql_insert_id();
      mysql_query("UPDATE $tabla SET idexterno = '".$row['idexterno']."' WHERE plaza='".$plaza."' AND cve='".$id."'");
   }
   
   $documento=array();
   $documento['idexterno'] = $row['idexterno'];
   $documento['serie']=$row['serie'];
   $documento['folio']=$row['folio'];
   $documento['fecha']=$row['fecha'].' '.$row['hora'];
   $documento['metodopago']=($row['forma_pago']==1) ? 'PPD' : 'PUE';
   $documento['regimenfiscal']=$Plaza['regimensat'];
   //$documento['idtipodocumento']=1;
   $documento['tipodocumento']=$tipo;
   $documento['observaciones']=$row['obs'];

   $documento['condicionesDePago']=$row['cve'];
   $documento['exportacion']='01';

   if($row['periodicidad']!=''){
      $documento['informacionglobal']=array(
         'periodicidad' => $row['periodicidad'],
         'meses' => $row['meses'],
         'anio' => $row['anio']
      );
   }

   $resTicket=mysql_query("SELECT b.cve,b.fecha,b.placa FROM venta_engomado_factura a INNER JOIN cobro_engomado b ON b.plaza = a.plaza AND b.cve = a.venta WHERE a.plaza='$plaza' AND LEFT(b.fecha,7)='".substr($row['fecha'],0,7)."' AND a.".$campo."='$id' AND b.estatus!='C'");
   while($rowTicket=mysql_fetch_array($resTicket)){
      $documento['observaciones'].="
      ".'Ticket: '.$rowTicket['cve'].' Fecha Venta: '.$rowTicket['fecha'].' Placa: '.$rowTicket['placa'];
   }
   if($row['tipo_relacion'] != ''){
      $documento['cfdirelacionados'] = array(
         'TipoRelacion' => $row['tipo_relacion'],
         'UUIDS' => $row['uuidsrelacionados']
      );
   }
   $documento['formapago']=$TipoPago['clave_sat'];
   $res1 = mysql_query("SELECT * FROM clientes WHERE cve='".$row['cliente']."'");
   $row1 = mysql_fetch_array($res1);
   $row1['cve']=0;
   $emailenvio = $row1['email'];
   //if(($row['tipo_pago']==1 || $row['tipo_pago'] == 2 || $row['tipo_pago']==5) && $row1['cuenta_pago']!='')
   // $documento['numerocuentapago']=$row1['cuenta_pago'];
   $documento['receptor']['codigo']=$row1['cve'];
   $documento['receptor']['rfc']=$row1['rfc'].$row1['homoclave'];
   $documento['receptor']['nombre']=$row1['nombre'];
   $documento['receptor']['calle']=$row1['calle'];
   $documento['receptor']['num_ext']=$row1['numexterior'];
   $documento['receptor']['num_int']=$row1['numinterior'];
   $documento['receptor']['colonia']=$row1['colonia'];
   $documento['receptor']['localidad']=$row1['localidad'];
   $documento['receptor']['municipio']=$row1['municipio'];
   $documento['receptor']['estado']=$row1['estado'];
   //$documento['receptor']['pais']='MEX';
   $documento['receptor']['codigopostal']=$row1['codigopostal'];
   //echo $row1['usocfdi'];
   $documento['receptor']['usodelcomprobante'] = $row1['usocfdi'];

   $documento['receptor']['regimenfiscal'] = $row1['regimensat'];
   //Agregamos los conceptos
   $res2 = mysql_query("SELECT * FROM {$tabla}mov WHERE plaza='".$plaza."' AND cvefact='".$id."'");
   
   $i=0;
   $partidas = mysql_num_rows($res2);
   $saldoiva = $row['iva'];
   $baseiva=0;
   $totaliva=0;
   while($row2 = mysql_fetch_array($res2)){
      //if(abs($row2['importe']-($row2['cantidad'] * $row2['precio'])) >= 1)
      //   $row2['importe'] = round($row2['cantidad'] * $row2['precio'],2);
      $documento['conceptos'][$i]['clave']=$row2['claveprodsat'];
      $documento['conceptos'][$i]['codigo']=$row2['ticket'];
      $documento['conceptos'][$i]['codigounidad']=$row2['claveunidadsat'];
      $documento['conceptos'][$i]['cantidad']=$row2['cantidad'];
      $documento['conceptos'][$i]['unidad']=$row2['unidad'];
      $documento['conceptos'][$i]['descripcion']=$row2['concepto'];/*iconv('UTF-8','ISO-8859-1',$row2['concepto']);*/
      $documento['conceptos'][$i]['valorUnitario']=$row2['precio'];
      $documento['conceptos'][$i]['importe']=$row2['importe'];
      $documento['conceptos'][$i]['objetoimpuesto']='02';
      //$documento['conceptos'][$i]['importe_iva']=$row2['importe_iva'];
      if($row2['importe_iva'] > 0){
         if(($i+1)==$partidas) $row2['importe_iva'] = round($saldoiva,2);
         $documento['conceptos'][$i]['impuestostrasladados'][] = array(
            'impuesto' => '002',
            'base' => $row2['importe'],
            'tipofactor' => 'Tasa',
            'tasaocuota' => 0.16,
            'importe' => $row2['importe_iva']
         );
         $saldoiva-=$row2['importe_iva'];
         $baseiva+=$row2['importe'];
         $totaliva=$row2['importe_iva'];
      }
      if($row2['retiene_iva'] > 0 || $row2['retiene_isr'] > 0){
         if($row2['retiene_iva'] > 0 ){
            $documento['conceptos'][$i]['impuestosretenidos'][] = array(
               'impuesto' => '002',
               'base' => $row2['importe'],
               'factor' => 'Tasa',
               'tasaocuota' => $row['por_iva_retenido']/100,
               'importe' => round($row2['importe'] * $row['por_iva_retenido']/100,2)
            );
         }
         if($row2['retiene_isr'] > 0 ){
            $documento['conceptos'][$i]['impuestosretenidos'][] = array(
               'impuesto' => '001',
               'base' => $row2['importe'],
               'factor' => 'Tasa',
               'tasaocuota' => $row['por_isr_retenido']/100,
               'importe' => round($row2['importe'] * $row['por_isr_retenido']/100,2)
            );
         }
      }
      $i++;
   }
   $documento['subtotal']=$row['subtotal'];
   //$documento['descuento']=0;
   //Traslados
   #IVA
   if($row['iva']>0){
      $documento['traslados']['totaltraslados']=$row['iva'];
      $documento['traslados']['impuestostrasladados'][] = array(
         'importe' => $row['iva'],
         'impuesto' => '002',
         'base' => round($baseiva,2),
         'tipofactor' => 'Tasa',
         'tasaocuota' => 0.16
      );

   }

   if($row['iva_retenido'] > 0 || $row['isr_retenido'] > 0){
      $documento['retenciones']['total_retenciones']=$row['iva_retenido'] + $row['isr_retenido'];  
      if($row['iva_retenido'] > 0 ){
         $documento['retenciones']['impuestosretenidos'][] = array('importe'=>$row['iva_retenido'], 'impuesto' => '002');
      }
      if($row['isr_retenido'] > 0 ){
         $documento['retenciones']['impuestosretenidos'][] = array('importe'=>$row['isr_retenido'], 'impuesto'=>'001');  
      }
   }
   
   //total
   $documento['total']=$row['total'];
   //Moneda
   $documento['moneda']     = 'MXN'; //1=pesos, 2=Dolar, 3=Euro
   $documento['tipocambio'] = 1;


   return $documento;
}

function existencia_timbres($plaza){
   $res = mysql_query("SELECT SUM(cantidad) as cantidad FROM compra_timbres WHERE plaza='{$plaza}' AND estatus='P'");
   $row = mysql_fetch_assoc($res);
   $existencia = $row['cantidad'];
   $res = mysql_query("SELECT SUM(IF(estatus='C',2,1)) as cantidad FROM facturas WHERE plaza='{$plaza}' AND respuesta1!=''");
   $row = mysql_fetch_assoc($res);
   $existencia -= $row['cantidad'];
   return $existencia;
}

function validar_timbres($plaza){
   $resultado['seguir'] = true;
   $res = mysql_query("SELECT validar_timbres FROM plazas WHERE cve='{$plaza}'");
   $row = mysql_fetch_assoc($res);
   if($row['validar_timbres']==1){
      $existencia = existencia_timbres($plaza);
      if ($existencia <= 0) {
         $resultado['seguir'] = false;
      }
   }
   return $resultado;
}


function fecha_letra($fecha){
   $fecven=split("-",$fecha);
   $fecha_letra=$fecven[2]." de ";;
   switch($fecven[1]){
      case "01":$fecha_letra.="Enero";break;
      case "02":$fecha_letra.="Febrero";break;
      case "03":$fecha_letra.="Marzo";break;
      case "04":$fecha_letra.="Abril";break;
      case "05":$fecha_letra.="Mayo";break;
      case "06":$fecha_letra.="Junio";break;
      case "07":$fecha_letra.="Julio";break;
      case "08":$fecha_letra.="Agosto";break;
      case "09":$fecha_letra.="Septiembre";break;
      case "10":$fecha_letra.="Octubre";break;
      case "11":$fecha_letra.="Noviembre";break;
      case "12":$fecha_letra.="Diciembre";break;
   }
   $fecha_letra.=" del ".$fecven[0]."";
   return $fecha_letra;
}
?>