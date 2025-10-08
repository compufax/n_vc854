<?php
error_reporting(E_ERROR | E_PARSE);
require_once('cnx_db.php');
require_once('globales.php');

$tabla = "";
$tablaetiqueta = '';
if ($_GET['table'] == 1){
    $tabla = "cobro_engomado";
    $tablaetiqueta = "ventas";
}
elseif ($_GET['table'] == 2){
    $tabla = "cobro_engomado";
    $tablaetiqueta = "ventas";
}

if($tabla != ""){
    echo '<h1>Se reparo la tabla '.$tablaetiqueta.'</h1>'
    mysql_query("REPAIR TABLE {$tabla}");
}
?>